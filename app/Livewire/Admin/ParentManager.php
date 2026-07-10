<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ParentManager extends Component
{
    use WithPagination;

    // Properti Form Input Account & Assignment
    public $name;
    public $email;
    public $password;
    public $student_id; // Menyimpan ID murid yang dipilih

    // Properti State Pencarian/Filter di Tabel
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function store()
    {
        // Validasi input akun wali dan keabsahan murid yang dipilih
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'student_id' => 'required|exists:students,id',
        ], [
            'student_id.required' => 'Anda harus memilih salah satu murid untuk ditautkan.',
        ]);

        // Gunakan Database Transaction agar jika salah satu proses gagal, data tidak korup
        DB::transaction(function () {
            // 1. Buat User Account untuk Wali Murid agar bisa login
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'WaliMurid',
            ]);

            // 2. Tautkan Akun User Baru tersebut ke tabel pivot jembatan student_parents
            DB::table('student_parents')->insert([
                'user_id' => $user->id,
                'student_id' => $this->student_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $this->reset(['name', 'email', 'password', 'student_id']);
        session()->flash('success', 'Akun Wali Murid berhasil dibuat dan berhasil ditautkan ke siswa!');
    }

    public function delete($userId)
    {
        // Menghapus User Wali otomatis akan memutus relasi di student_parents jika Anda menggunakan cascade,
        // Namun kita bersihkan manual demi keamanan integritas data
        DB::transaction(function () use ($userId) {
            DB::table('student_parents')->where('user_id', $userId)->delete();
            User::findOrFail($userId)->delete();
        });

        session()->flash('success', 'Akun Wali Murid dan data tautan berhasil dihapus.');
    }

    public function render()
    {
        // Cari daftar wali murid yang sudah terdaftar beserta nama anak yang diwalinya
        $parentsQuery = User::where('role', 'WaliMurid')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc');

        return view('livewire.admin.parent-manager', [
            'parents' => $parentsQuery->paginate(10),
            // Mengambil seluruh siswa aktif untuk opsi dropdown pilihan penautan anak
            'allStudents' => Student::orderBy('name', 'asc')->get()
        ]);
    }
}
