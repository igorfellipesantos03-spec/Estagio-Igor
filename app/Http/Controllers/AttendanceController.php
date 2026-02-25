<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceRecord;
use App\Models\Hackathon;
use App\Notifications\PresenceValidatedNotification;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Exibe o formulário de upload de foto para o aluno
     */
    public function create(): View
    {
        $user = Auth::user();
        
        // Hackathons disponíveis (ativos e o aluno deve estar participando)
        $hackathons = Hackathon::where('data_inicio', '<=', now())
            ->where('data_fim', '>=', now())
            ->where('status', 'active')
            ->whereHas('grupos.membros', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->orderBy('data_inicio', 'asc')
            ->get();

        // Presenças já enviadas pelo aluno (agora retornamos a coleção em vez de apenas o ID)
        $presencas = AttendanceRecord::where('user_id', $user->id)
            ->get()
            ->keyBy('hackathon_id');

        return view('attendance.aluno.create', [
            'hackathons' => $hackathons,
            'presencas' => $presencas,
            'user' => $user,
        ]);
    }

    /**
     * Processa o upload da foto de presença
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'hackathon_id' => [
                'required',
                'exists:hackathons,id',
            ],
            'photo' => [
                'required',
                Rule::file()->image()->max(5120), // Max 5MB
            ],
        ], [
            'hackathon_id.required' => 'Selecione um hackathon.',
            'hackathon_id.exists' => 'Hackathon inválido.',
            'hackathon_id.unique' => 'Você já enviou uma foto de presença para este hackathon.',
            'photo.required' => 'Selecione uma foto.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.max' => 'A imagem não pode ter mais de 5MB.',
        ]);

        // Verificação extra de segurança: o hackathon está em andamento?
        $hackathon = Hackathon::findOrFail($validated['hackathon_id']);
        
        if ($hackathon->status !== 'active' || now() < $hackathon->data_inicio || now() > $hackathon->data_fim) {
            return back()->withErrors(['hackathon_id' => 'Não é possível validar presença neste hackathon (fora do período ou inativo).']);
        }

        // Verificação extra de segurança: o usuário participa do hackathon?
        $participante = $hackathon->whereHas('grupos.membros', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->where('id', $hackathon->id)->exists();

        if (!$participante) {
            return back()->withErrors(['hackathon_id' => 'Você não está participando deste hackathon.']);
        }

        // Verificação manual de duplicidade considerando status
        $existingRecord = AttendanceRecord::where('user_id', $user->id)
            ->where('hackathon_id', $validated['hackathon_id'])
            ->first();

        if ($existingRecord) {
            if ($existingRecord->status !== AttendanceStatus::REJECTED) {
                return back()->withErrors(['hackathon_id' => 'Sua presença já está em análise ou foi aprovada.']);
            }
        }

        // Salva a foto em storage privado com nome hash
        $path = $request->file('photo')->store('attendance', 'local');

        if ($existingRecord) {
            // Se já existia e estava rejeitado, deleta a foto antiga do servidor e atualiza o registro
            if ($existingRecord->photo_path && Storage::disk('local')->exists($existingRecord->photo_path)) {
                Storage::disk('local')->delete($existingRecord->photo_path);
            }
            $existingRecord->update([
                'photo_path' => $path,
                'status' => AttendanceStatus::PENDING,
                'admin_note' => null,
            ]);
        } else {
            AttendanceRecord::create([
                'user_id' => $user->id,
                'hackathon_id' => $validated['hackathon_id'],
                'photo_path' => $path,
                'status' => AttendanceStatus::PENDING,
            ]);
        }

        return redirect()
            ->route('aluno.presenca.create')
            ->with('success', 'Foto de presença enviada com sucesso! Aguarde a validação do professor.');
    }

    /**
     * Lista as presenças pendentes de um hackathon (Professor)
     */
    public function index(Hackathon $hackathon): View
    {
        $presencas = AttendanceRecord::where('hackathon_id', $hackathon->id)
            ->with('user')
            ->latest()
            ->get();

        return view('attendance.professor.index', [
            'hackathon' => $hackathon,
            'presencas' => $presencas,
        ]);
    }

    /**
     * Lista todos os hackathons para o professor selecionar
     */
    public function hackathonList(): View
    {
        $hackathons = Hackathon::withCount([
            'attendanceRecords as pendentes_count' => function ($query) {
                $query->where('status', AttendanceStatus::PENDING->value);
            }
        ])->latest()->get();

        return view('attendance.professor.hackathons', [
            'hackathons' => $hackathons,
        ]);
    }

    /**
     * Atualiza o status de uma presença (Aprovar/Rejeitar)
     */
    public function update(Request $request, AttendanceRecord $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(AttendanceStatus::class)],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ], [
            'status.required' => 'Selecione uma ação.',
            'admin_note.max' => 'A observação não pode ter mais de 500 caracteres.',
        ]);

        $oldStatus = $attendance->status;

        $attendance->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        $newStatus = AttendanceStatus::from($validated['status']);

        // Enviar notificação ao aluno
        $attendance->user->notify(new PresenceValidatedNotification(
            $attendance,
            $newStatus,
            $validated['admin_note'] ?? null
        ));

        // Conceder pontos de gamificação se aprovado
        if ($newStatus === AttendanceStatus::APPROVED) {
            $gamificationService = app(GamificationService::class);
            $gamificationService->awardPoints(
                $attendance->user,
                'presence_confirmed',
                $attendance,
                "Presença validada no hackathon: {$attendance->hackathon->nome}"
            );
        }

        $statusLabel = $newStatus->label();
        
        return redirect()
            ->route('professor.presenca.index', $attendance->hackathon_id)
            ->with('success', "Presença de {$attendance->user->name} marcada como: {$statusLabel}");
    }

    /**
     * Gera URL temporária assinada para visualização segura da foto
     */
    public function showPhoto(AttendanceRecord $attendance): Response
    {
        // Verifica se o arquivo existe
        if (!Storage::disk('local')->exists($attendance->photo_path)) {
            abort(404, 'Foto não encontrada.');
        }

        // Retorna a imagem diretamente
        $file = Storage::disk('local')->get($attendance->photo_path);
        $mimeType = Storage::disk('local')->mimeType($attendance->photo_path);

        return response($file, 200)->header('Content-Type', $mimeType);
    }
}
