<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            $redirect = match ($user->tipo) {
                'aluno'     => redirect()->route('dashboard.aluno'),
                'professor' => redirect()->route('dashboard.professor'),
                'adm'       => redirect()->route('users.index'),
                default     => redirect('/'),
            };

            return $redirect->with('success', 'Login realizado com sucesso!');
        }

        return back()
            ->withErrors(['email' => 'Credenciais inválidas.'])
            ->onlyInput('email');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'matricula' => 'required|string|unique:users,matricula',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'matricula' => $request->matricula,
            'tipo'      => 'aluno',
            'password'  => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect()->route('dashboard.aluno')->with('success', 'Cadastro realizado com sucesso!');
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logout realizado com sucesso.');
    }
}
