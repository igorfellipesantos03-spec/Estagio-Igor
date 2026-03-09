<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hackathon; // Import Hackathon model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->input('filtro');
        $query = User::query();

        if ($tipo && in_array($tipo, ['aluno', 'professor', 'adm'])) {
            $query->where('tipo', $tipo);
        }

        $users = $query->get();
        $user = Auth::user(); 
        
        // Fix: Pass all hackathons or an empty collection to avoid "Undefined variable" error in the view
        $hackathons = Hackathon::all(); 

        return view('users.index', compact('users', 'user', 'hackathons'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'tipo' => 'required|in:aluno,professor,adm',
            'matricula' => 'required_if:tipo,aluno|nullable|string|unique:users,matricula',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $matricula = ($request->tipo === 'aluno') ? $request->matricula : null;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'matricula' => $matricula,
            'tipo' => $request->tipo,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'tipo' => 'required|in:aluno,professor,adm',
            'matricula' => 'required_if:tipo,aluno|nullable|string|unique:users,matricula,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->avatar->store('avatars', 'public');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($data['tipo'] !== 'aluno') {
            $data['matricula'] = null;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilizador bloqueado com sucesso!');
    }
}