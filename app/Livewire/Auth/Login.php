<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $loginInput; // Bisa berisi Email, Username, atau NISN
    public $password;

    public function login()
    {
        $this->validate([
            'loginInput' => 'required|string',
            'password'   => 'required',
        ], [
            'loginInput.required' => 'Email / Username / NISN wajib diisi.',
            'password.required'   => 'Password wajib diisi.',
        ]);

        // Fleksibel: Coba autentikasi menggunakan Email ATAU Username/NISN
        $isEmail = filter_var($this->loginInput, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'username';

        if (Auth::attempt([$field => $this->loginInput, 'password' => $this->password])) {
            session()->regenerate();

            $user = Auth::user();

            // Redirect cerdas berdasarkan Role
            if ($user->role === 'Admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role === 'Guru') {
                return redirect()->intended('/dashboard');
            } elseif ($user->role === 'Kepala') {
                return redirect()->intended('/kepala-sekolah/dashboard');
            } elseif ($user->role === 'WaliMurid') {
                return redirect()->intended('/');
            }

            return redirect()->intended('/dashboard');
        }

        $this->addError('loginInput', 'Email / Username / NISN atau password yang Anda masukkan salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app');
    }
}
