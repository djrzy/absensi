<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class TeacherManager extends Component
{
    public $teachers;

    // Form properties
    public $name;
    public $email;
    public $password;

    public function mount()
    {
        $this->refreshTeachers();
    }

    public function refreshTeachers()
    {
        // Hanya ambil user dengan role 'Guru'
        $this->teachers = User::where('role', 'Guru')->orderBy('name')->get();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'Guru',
        ]);

        $this->reset(['name', 'email', 'password']);
        $this->refreshTeachers();
        session()->flash('success', 'Akun Guru baru berhasil didaftarkan!');
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        $this->refreshTeachers();
        session()->flash('success', 'Data Guru berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.admin.teacher-manager');
    }
}
