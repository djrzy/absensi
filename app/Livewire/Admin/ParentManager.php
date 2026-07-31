<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Student;
use App\Models\Classroom;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ParentManager extends Component
{
    use WithPagination;

    // Properti Form Input Akun Wali Utama
    public $name;
    public $email;
    public $password;
    public $student_id;

    // Properti State Pencarian & Filter Kelas
    public $search = '';
    public $filterClassroom = ''; // Filter berdasarkan kelas anak

    // Properti State Modal "+ Tautkan Anak"
    public $showLinkModal = false;
    public $targetParent = null;
    public $additional_student_id = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterClassroom()
    {
        $this->resetPage();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'student_id' => 'nullable|exists:students,id',
        ]);

        DB::transaction(function () {
            // 1. Buat User Account untuk Wali Murid
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'WaliMurid',
            ]);

            // 2. Jika anak pertama dipilih, tautkan ke student_parents
            if ($this->student_id) {
                DB::table('student_parents')->insert([
                    'user_id' => $user->id,
                    'student_id' => $this->student_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $this->reset(['name', 'email', 'password', 'student_id']);
        session()->flash('success', 'Akun Wali Murid berhasil dibuat!');
    }

    public function openLinkModal($parentId)
    {
        $this->targetParent = User::findOrFail($parentId);
        $this->additional_student_id = null;
        $this->showLinkModal = true;
    }

    public function closeLinkModal()
    {
        $this->showLinkModal = false;
        $this->targetParent = null;
        $this->additional_student_id = null;
    }

    public function linkStudent()
    {
        $this->validate([
            'additional_student_id' => 'required|exists:students,id',
        ], [
            'additional_student_id.required' => 'Silakan pilih siswa yang ingin ditautkan.',
        ]);

        $exists = DB::table('student_parents')
            ->where('user_id', $this->targetParent->id)
            ->where('student_id', $this->additional_student_id)
            ->exists();

        if ($exists) {
            session()->flash('modal_error', 'Siswa ini sudah terikat dengan akun wali tersebut.');
            return;
        }

        DB::table('student_parents')->insert([
            'user_id' => $this->targetParent->id,
            'student_id' => $this->additional_student_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('success', 'Berhasil menautkan anak tambahan ke akun ' . $this->targetParent->name);
        $this->closeLinkModal();
    }

    public function unlinkStudent($parentId, $studentId)
    {
        DB::table('student_parents')
            ->where('user_id', $parentId)
            ->where('student_id', $studentId)
            ->delete();

        session()->flash('success', 'Tautan anak berhasil dilepaskan dari akun wali tersebut.');
    }

    public function delete($userId)
    {
        DB::transaction(function () use ($userId) {
            DB::table('student_parents')->where('user_id', $userId)->delete();
            User::findOrFail($userId)->delete();
        });

        session()->flash('success', 'Akun Wali Murid beserta seluruh tautan anak berhasil dihapus.');
    }

    public function render()
    {
        // Query Wali Murid dengan relasi anak (students) dan kelasnya (classroom)
        $parentsQuery = User::where('role', 'WaliMurid')
            ->with(['students.classroom'])
            // Filter Pencarian: Nama Wali, Email Wali, ATAU Nama Anak (Multi-anak)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhereHas('students', function ($studentQuery) {
                            $studentQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('nisn', 'like', '%' . $this->search . '%');
                        });
                });
            })
            // Filter Berdasarkan Kelas Anak
            ->when($this->filterClassroom, function ($query) {
                $query->whereHas('students', function ($studentQuery) {
                    $studentQuery->where('classroom_id', $this->filterClassroom);
                });
            })
            ->orderBy('id', 'desc');

        return view('livewire.admin.parent-manager', [
            'parents' => $parentsQuery->paginate(10),
            'allStudents' => Student::with('classroom')->orderBy('name', 'asc')->get(),
            'classrooms' => Classroom::orderBy('name', 'asc')->get()
        ]);
    }
}
