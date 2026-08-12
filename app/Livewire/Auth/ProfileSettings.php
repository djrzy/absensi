<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileSettings extends Component
{
    public $name;
    public $username;
    public $email;
    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->name     = $user->name;
        $this->username = $user->username;
        $this->email    = $user->email;
    }

    public function updateProfile()
    {
        $userId = Auth::id();

        $this->validate([
            'name'     => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'max:50',
                Rule::unique('users', 'username')->ignore($userId)
            ],
            'email'    => [
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'nullable|min:6|same:password_confirmation',
        ], [
            'name.required'       => 'Nama lengkap wajib diisi.',
            'username.required'   => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique'     => 'Username ini sudah digunakan.',
            'email.required'      => 'Alamat email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Alamat email ini sudah digunakan.',
            'password.min'        => 'Password baru minimal 6 karakter.',
            'password.same'       => 'Konfirmasi password tidak cocok dengan password baru.',
        ]);

        $user = Auth::user();

        $updateData = [
            'name'     => $this->name,
            'username' => $this->username,
            'email'    => $this->email,
        ];

        if (!empty($this->password)) {
            $updateData['password'] = Hash::make($this->password);
        }

        $user->update($updateData);

        // Reset field password setelah sukses simpan
        $this->reset(['password', 'password_confirmation']);
        session()->flash('success', 'Pengaturan profil dan akun berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.auth.profile-settings');
    }
}
