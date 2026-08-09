<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password = 'password';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();

            // Redirect cerdas berdasarkan Role
            $user = Auth::user();
            if ($user->role === 'Admin') {
                return redirect()->intended('/admin/tahun-ajaran');
            } elseif ($user->role === 'Guru') {
                return redirect()->intended('/dashboard');
            }

            return redirect()->intended('/'); // Wali murid
        }

        // Jika gagal, lempar error ke input email
        $this->addError('email', 'Email atau password yang Anda masukkan salah.');
    }

    public function render()
    {
        // Kita set layout kosong (blank) khusus login agar tidak memunculkan navbar utama aplikasi
        return view('livewire.auth.login')->layout('layouts.app');
    }
}
