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

        // Deteksi apakah input berupa Format Email atau Username/NISN
        $fieldType = filter_var($this->loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $this->loginInput,
            'password'  => $this->password,
        ];

        if (Auth::attempt($credentials)) {
            session()->regenerate();

            $user = Auth::user();
            if ($user->role === 'Admin') {
                return redirect()->intended('/admin/tahun-ajaran');
            } elseif ($user->role === 'Guru') {
                return redirect()->intended('/dashboard');
            }

            return redirect()->intended('/parent/dashboard'); // Wali murid
        }

        $this->addError('loginInput', 'Email / Username / NISN atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app');
    }
}
