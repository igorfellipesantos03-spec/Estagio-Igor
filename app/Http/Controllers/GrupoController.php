<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Hackathon;
use App\Models\User;
use App\Notifications\GroupMediaRemovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GrupoController extends Controller
{
    /**
     * Listagem de grupos do aluno
     */
    public function index()
    {
        $user = Auth::user();
        $grupos = $user->grupos()->with(['hackathon', 'lider', 'membros'])->get();

        return view('grupos.aluno.index', compact('grupos'));
    }

    /**
     * Criar novo grupo
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'hackathon_id' => 'required|exists:hackathons,id',
        ]);

        $user = Auth::user();
        $existingGroup = $user->grupos()->where('hackathon_id', $request->hackathon_id)->first();

        if ($existingGroup) {
            return back()->withErrors(['msg' => 'Você já está em um grupo para este hackathon.']);
        }

        // Validate if inscriptions are still open (24 hours before start)
        $hackathon = Hackathon::findOrFail($request->hackathon_id);
        $inscricaoLimite = \Carbon\Carbon::parse($hackathon->data_inicio)->subDay();

        if (now() > $inscricaoLimite) {
            return back()->withErrors(['msg' => 'As inscrições para este hackathon já foram encerradas.']);
        }

        if ($hackathon->status === 'finalized') {
            return back()->withErrors(['msg' => 'Este hackathon já foi finalizado.']);
        }

        $grupo = Grupo::create([
            'nome' => $request->nome,
            'hackathon_id' => $request->hackathon_id,
            'lider_id' => $user->id,
            'codigo' => strtoupper(Str::random(6)),
        ]);

        $grupo->membros()->attach($user->id);

        return redirect()->route('aluno.hackathons.index')->with('success', 'Grupo criado! Agora você já está inscrito neste Hackathon. (Código do seu grupo: ' . $grupo->codigo . ')');
    }

    /**
     * Entrar em um grupo existente
     */
    public function join(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
        ], [
            'codigo.required' => 'Informe o código do grupo.',
        ]);

        // Buscar grupo pelo código
        $grupo = Grupo::where('codigo', $request->codigo)->first();

        if (!$grupo) {
            return back()->withErrors(['codigo' => 'Código de grupo inválido. Verifique o código e tente novamente.']);
        }

        $user = Auth::user();

        // Verificar se o aluno já está neste grupo
        if ($grupo->membros->contains($user->id)) {
            return back()->withErrors(['codigo' => 'Você já é membro deste grupo.']);
        }

        // Validate if inscriptions are still open (24 hours before start)
        $hackathon = $grupo->hackathon;
        $inscricaoLimite = \Carbon\Carbon::parse($hackathon->data_inicio)->subDay();

        if (now() > $inscricaoLimite) {
            return back()->withErrors(['msg' => 'As inscrições para este hackathon já foram encerradas.']);
        }

        if ($hackathon->status === 'finalized') {
            return back()->withErrors(['msg' => 'Este hackathon já foi finalizado.']);
        }

        // Verificar se o aluno já está em outro grupo para este hackathon
        $existingGroup = $user->grupos()->where('hackathon_id', $grupo->hackathon_id)->first();

        if ($existingGroup) {
            return back()->withErrors([
                'codigo' => 'Você já está no grupo "' . $existingGroup->nome . '" que pertence ao hackathon "' . $grupo->hackathon->nome . '". Saia do grupo atual antes de entrar em outro.'
            ]);
        }

        $grupo->membros()->attach($user->id);

        return redirect()->route('aluno.hackathons.index')->with('success', 'Você entrou no grupo "' . $grupo->nome . '" com sucesso! Agora você está participando deste Hackathon.');
    }

    /**
     * Atualizar grupo (nome e imagem) - Apenas líder
     */
    public function update(Request $request, Grupo $grupo)
    {
        $user = Auth::user();

        // Verifica se é o líder
        if ($grupo->lider_id !== $user->id) {
            return back()->withErrors(['msg' => 'Apenas o líder pode editar o grupo.']);
        }

        $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'imagem' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Atualiza nome se fornecido
        if ($request->has('nome')) {
            $grupo->nome = $request->nome;
        }

        // Atualiza imagem se fornecida
        if ($request->hasFile('imagem')) {
            // Remove imagem antiga se existir
            if ($grupo->imagem) {
                Storage::disk('public')->delete($grupo->imagem);
            }

            $path = $request->file('imagem')->store('grupos', 'public');
            $grupo->imagem = $path;
        }

        $grupo->save();

        return back()->with('success', 'Grupo atualizado com sucesso!');
    }

    /**
     * Deletar grupo - Apenas líder
     */
    public function destroy(Grupo $grupo)
    {
        $user = Auth::user();

        // Verifica se é o líder
        if ($grupo->lider_id !== $user->id) {
            return back()->withErrors(['msg' => 'Apenas o líder pode excluir o grupo.']);
        }

        // Remove imagem se existir
        if ($grupo->imagem) {
            Storage::disk('public')->delete($grupo->imagem);
        }

        $grupo->membros()->detach();
        $grupo->delete();

        return redirect()->route('grupos.index')->with('success', 'Grupo excluído com sucesso!');
    }

    /**
     * Remover membro do grupo - Apenas líder
     */
    public function removeMember(Grupo $grupo, User $user)
    {
        $currentUser = Auth::user();

        // Verifica se é o líder
        if ($grupo->lider_id !== $currentUser->id) {
            return back()->withErrors(['msg' => 'Apenas o líder pode remover membros.']);
        }

        // Não pode remover o próprio líder
        if ($user->id === $grupo->lider_id) {
            return back()->withErrors(['msg' => 'O líder não pode ser removido do grupo.']);
        }

        // Verifica se o usuário é membro do grupo
        if (!$grupo->membros->contains($user->id)) {
            return back()->withErrors(['msg' => 'Este usuário não é membro do grupo.']);
        }

        $grupo->membros()->detach($user->id);

        return back()->with('success', $user->name . ' foi removido do grupo.');
    }

    /**
     * Sair do grupo - Qualquer membro (exceto líder)
     */
    public function leave(Grupo $grupo)
    {
        $user = Auth::user();

        // Líder não pode sair, deve deletar o grupo
        if ($grupo->lider_id === $user->id) {
            return back()->withErrors(['msg' => 'O líder não pode sair do grupo. Delete o grupo se necessário.']);
        }

        $grupo->membros()->detach($user->id);

        return redirect()->route('grupos.index')->with('success', 'Você saiu do grupo ' . $grupo->nome . '.');
    }

    // ==========================================
    // MÉTODOS DO PROFESSOR
    // ==========================================

    /**
     * Listagem de todos os grupos - Professor
     */
    public function professorIndex()
    {
        $grupos = Grupo::with(['hackathon', 'lider', 'membros'])->latest()->get();

        return view('grupos.professor.index', compact('grupos'));
    }

    /**
     * Deletar grupo - Professor
     */
    public function professorDestroy(Grupo $grupo)
    {
        // Remove imagem se existir
        if ($grupo->imagem) {
            Storage::disk('public')->delete($grupo->imagem);
        }

        $grupo->membros()->detach();
        $grupo->delete();

        return back()->with('success', 'Grupo "' . $grupo->nome . '" foi excluído.');
    }

    /**
     * Remover imagem do grupo - Professor
     */
    public function professorRemoveImage(Grupo $grupo)
    {
        if ($grupo->imagem) {
            Storage::disk('public')->delete($grupo->imagem);
            $grupo->imagem = null;
            $grupo->save();

            // Notify the group leader
            if ($grupo->lider) {
                $grupo->lider->notify(new GroupMediaRemovedNotification($grupo));
            }

            return back()->with('success', 'Imagem do grupo removida com sucesso.');
        }

        return back()->withErrors(['msg' => 'Este grupo não possui imagem.']);
    }
}
