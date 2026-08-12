<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherManager extends Component
{
    public $teachers;

    // Form properties (Tambah Data)
    public $name;
    public $email;
    public $password;

    // Form properties (Edit Data & Modal - Tanpa Password)
    public $showEditModal = false;
    public $editingTeacherId = null;
    public $edit_name;
    public $edit_email;

    public function mount()
    {
        $this->refreshTeachers();
    }

    public function refreshTeachers()
    {
        $this->teachers = User::where('role', 'Guru')->orderBy('name')->get();
    }

    public function store()
    {
        $this->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ], [
            'name.required'     => 'Nama guru wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'role'     => 'Guru',
        ]);

        $this->reset(['name', 'email', 'password']);
        $this->refreshTeachers();
        session()->flash('success', 'Akun Guru baru berhasil didaftarkan!');
    }

    public function edit($id)
    {
        $teacher = User::findOrFail($id);
        $this->editingTeacherId = $teacher->id;
        $this->edit_name        = $teacher->name;
        $this->edit_email       = $teacher->email;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editingTeacherId', 'edit_name', 'edit_email']);
    }

    public function update()
    {
        $this->validate([
            'edit_name'  => 'required|string|max:100',
            'edit_email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingTeacherId)],
        ], [
            'edit_name.required'  => 'Nama guru wajib diisi.',
            'edit_email.required' => 'Email wajib diisi.',
            'edit_email.unique'   => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $teacher = User::findOrFail($this->editingTeacherId);
        $teacher->update([
            'name'  => $this->edit_name,
            'email' => $this->edit_email,
        ]);

        $this->closeEditModal();
        $this->refreshTeachers();
        session()->flash('success', 'Data Guru berhasil diperbarui!');
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
