<?php

namespace App\Http\Controllers;

use App\Events\AnnouncementCreated;
use App\Models\Hackathon;
use App\Models\User;
use App\Notifications\HackathonEndedNotification;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class HackathonController extends Controller
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    /**
     * Lista hackathons para o professor.
     */
    public function index(): View
    {
        $hackathons = Hackathon::with('grupos', 'winnerGroup')
            ->latest()
            ->get();
        
        return view('hackathons.professor.index', [
            'hackathons' => $hackathons,
            'user' => Auth::user()
        ]);
    }

    /**
     * Lista hackathons para o aluno.
     */
    public function alunoIndex(): View
    {
        // Hackathons ativos (não finalizados) E que ainda não passaram da data de fim
        $hackathons = Hackathon::where('data_fim', '>=', now())
            ->where('status', '!=', 'finalized')
            ->with('winnerGroup')
            ->orderBy('data_inicio', 'asc')
            ->get();

        return view('hackathons.aluno.index', [
            'hackathons' => $hackathons,
            'user' => Auth::user()->load('grupos')
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            $validatedData['banner'] = $request->banner->store('hackathons', 'public');
        }

        $hackathon = Hackathon::create($validatedData);

        // Dispara evento para distribuir anúncio a todos os alunos
        event(new AnnouncementCreated(
            title: '🚀 Novo Hackathon: ' . $hackathon->nome,
            body: 'O Hackathon ' . $hackathon->nome . ' está aberto para inscrições!',
            icon: 'megaphone',
            type: 'info',
            category: 'general',
            actionUrl: route('aluno.hackathons.index'),
            expiresAt: $hackathon->data_fim
        ));

        return redirect()->route('dashboard.professor')->with('success', 'Hackathon criado com sucesso!');
    }

    /**
     * Finalizar hackathon e premiar vencedor.
     */
    public function finalize(Request $request, Hackathon $hackathon): RedirectResponse
    {
        // Validação: winner_group_id é obrigatório
        $validated = $request->validate([
            'winner_group_id' => 'required|exists:grupos,id',
        ], [
            'winner_group_id.required' => 'Você deve selecionar o grupo vencedor.',
            'winner_group_id.exists' => 'O grupo selecionado não existe.',
        ]);

        // Verificar se o grupo pertence a este hackathon
        $winnerGroup = $hackathon->grupos()->where('id', $validated['winner_group_id'])->first();
        
        if (!$winnerGroup) {
            return back()->withErrors(['winner_group_id' => 'O grupo selecionado não pertence a este hackathon.']);
        }

        // Verificar se já está finalizado
        if ($hackathon->isFinalized()) {
            return back()->withErrors(['hackathon' => 'Este hackathon já foi finalizado.']);
        }

        DB::transaction(function () use ($hackathon, $winnerGroup, $validated) {
            // 1. Atualizar status do hackathon
            $hackathon->update([
                'status' => 'finalized',
                'winner_group_id' => $validated['winner_group_id'],
                'finalized_at' => now(),
            ]);

            // 2. Coletar IDs dos membros vencedores
            $winnerUserIds = $winnerGroup->membros->pluck('id')->toArray();

            // 3. Dar 1000 XP para os membros do grupo vencedor
            foreach ($winnerGroup->membros as $member) {
                $this->gamificationService->awardPoints(
                    $member,
                    'hackathon_winner',
                    $hackathon,
                    'Vencedor do hackathon: ' . $hackathon->nome
                );
            }

            // 4. Buscar outros participantes (membros de todos os grupos do hackathon, exceto vencedores)
            $otherParticipants = User::whereHas('grupos', function ($query) use ($hackathon) {
                    $query->where('hackathon_id', $hackathon->id);
                })
                ->whereNotIn('id', $winnerUserIds)
                ->get();

            // 5. Dar 200 XP para os demais participantes
            foreach ($otherParticipants as $participant) {
                $this->gamificationService->awardPoints(
                    $participant,
                    'hackathon_participation',
                    $hackathon,
                    'Participação no hackathon: ' . $hackathon->nome
                );
            }

            // 6. Enviar notificação Global de vitória para todos os usuários (Broadcast)
            event(new AnnouncementCreated(
                title: '🏆 Temos um Vencedor!',
                body: 'O grupo "' . $winnerGroup->nome . '" foi o grande vencedor do hackathon "' . $hackathon->nome . '"!',
                icon: 'trophy',
                type: 'success',
                category: 'general',
                actionUrl: route('aluno.hackathons.index'),
                expiresAt: now()->addDays(7)
            ));
        });

        return redirect()->route('hackathons.index')
            ->with('success', 'Hackathon finalizado com sucesso! O grupo "' . $winnerGroup->nome . '" foi premiado como vencedor.');
    }

    // Métodos placeholders
    public function create() {}
    public function show(Hackathon $hackathon) {}
    public function edit(Hackathon $hackathon) {}
    
    public function update(Request $request, Hackathon $hackathon): RedirectResponse
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            // Remove banner antigo
            if ($hackathon->banner && Storage::disk('public')->exists($hackathon->banner)) {
                Storage::disk('public')->delete($hackathon->banner);
            }
            $validatedData['banner'] = $request->banner->store('hackathons', 'public');
        }

        $hackathon->update($validatedData);

        return redirect()->route('dashboard.professor')->with('success', 'Hackathon atualizado com sucesso!');
    }

    /**
     * Excluir hackathon.
     */
    public function destroy(Hackathon $hackathon): RedirectResponse
    {
        // Verificar se tem grupos associados
        if ($hackathon->grupos()->exists()) {
            return back()->withErrors(['hackathon' => 'Não é possível excluir um hackathon com grupos inscritos.']);
        }

        // Remover banner se existir
        if ($hackathon->banner && Storage::disk('public')->exists($hackathon->banner)) {
            Storage::disk('public')->delete($hackathon->banner);
        }

        $hackathon->delete();

        return redirect()->route('hackathons.index')
            ->with('success', 'Hackathon excluído com sucesso!');
    }
}