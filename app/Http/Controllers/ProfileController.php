<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the user profile edit form.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update the user's profile information (avatar and/or password).
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        // We will validate only what is passed
        $rules = [];
        
        if ($request->hasFile('avatar')) {
            $rules['avatar'] = ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        }
        
        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', Password::defaults(), 'confirmed'];
        }

        $validatedData = $request->validate($rules);

        // Update Avatar
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->avatar->store('avatars', 'public');
        }

        // Update Password
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Save if any changes were made
        if ($request->hasFile('avatar') || $request->filled('password')) {
            $user->save();
            return back()->with('success', 'Perfil atualizado com sucesso!');
        }

        return back()->with('info', 'Nenhuma alteração foi feita.');
    }
}
