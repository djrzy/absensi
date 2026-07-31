<?php

namespace App\Livewire\Admin;

use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ClassroomManager extends Component
{
    use WithPagination;

    public $name;
    public $teacher_id = null;

    // State untuk fitur Edit / Assign Wali Kelas
    public $editingClassroomId = null;
    public $edit_name;
    public $edit_teacher_id = null;

    // Properti tracking detail kelas terpilih
    public $selectedClassroomId = null;

    // Reset pagination murid saat berpindah kelas
    public function showStudents($classroomId)
    {
        $this->selectedClassroomId = $classroomId;
        $this->resetPage('studentPage'); // Reset ke halaman 1 list murid
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:50|unique:classrooms,name',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) {
            session()->flash('error', 'Gagal membuat kelas! Silakan aktifkan salah satu Tahun Ajaran terlebih dahulu.');
            return;
        }

        Classroom::create([
            'name' => strtoupper($this->name),
            'academic_year_id' => $activeYear->id,
            'teacher_id' => $this->teacher_id ?: null,
        ]);

        $this->reset(['name', 'teacher_id']);
        session()->flash('success', 'Kelas baru berhasil dibuat!');
    }

    public function editClassroom($classroomId)
    {
        $classroom = Classroom::findOrFail($classroomId);
        $this->editingClassroomId = $classroom->id;
        $this->edit_name = $classroom->name;
        $this->edit_teacher_id = $classroom->teacher_id;
    }

    public function cancelEdit()
    {
        $this->reset(['editingClassroomId', 'edit_name', 'edit_teacher_id']);
    }

    public function updateClassroom()
    {
        $this->validate([
            'edit_name' => 'required|string|max:50|unique:classrooms,name,' . $this->editingClassroomId,
            'edit_teacher_id' => 'nullable|exists:users,id',
        ]);

        $classroom = Classroom::findOrFail($this->editingClassroomId);
        $classroom->update([
            'name' => strtoupper($this->edit_name),
            'teacher_id' => $this->edit_teacher_id ?: null,
        ]);

        $this->reset(['editingClassroomId', 'edit_name', 'edit_teacher_id']);
        session()->flash('success', 'Data kelas & Wali Kelas berhasil diperbarui!');
    }

    public function delete($id)
    {
        Classroom::findOrFail($id)->delete();

        if ($this->selectedClassroomId == $id) {
            $this->reset('selectedClassroomId');
        }

        session()->flash('success', 'Kelas berhasil dihapus!');
    }

    public function render()
    {
        // 1. Ambil ID guru yang SUDAH menjadi wali kelas di tempat lain
        $assignedTeacherIds = Classroom::whereNotNull('teacher_id')
            ->when($this->editingClassroomId, function ($q) {
                $q->where('id', '!=', $this->editingClassroomId);
            })
            ->pluck('teacher_id')
            ->toArray();

        // 2. Filter Guru yang BELUM ditugaskan di kelas mana pun
        $availableTeachers = User::where('role', 'Guru')
            ->whereNotIn('id', $assignedTeacherIds)
            ->orderBy('name', 'asc')
            ->get();

        // 3. Query daftar kelas dengan Pagination (5 kelas per halaman)
        $classrooms = Classroom::with(['waliKelas'])
            ->withCount('students')
            ->orderBy('name')
            ->paginate(5, ['*'], 'classroomPage');

        // 4. Query murid terdaftar di kelas terpilih dengan Pagination (10 murid per halaman)
        $selectedClassroom = null;
        $students = collect();

        if ($this->selectedClassroomId) {
            $selectedClassroom = Classroom::with('waliKelas')->find($this->selectedClassroomId);
            if ($selectedClassroom) {
                $students = $selectedClassroom->students()
                    ->orderBy('name', 'asc')
                    ->paginate(10, ['*'], 'studentPage');
            }
        }

        return view('livewire.admin.classroom-manager', [
            'availableTeachers' => $availableTeachers,
            'classrooms' => $classrooms,
            'selectedClassroom' => $selectedClassroom,
            'students' => $students
        ]);
    }
}
