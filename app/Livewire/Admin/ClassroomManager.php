<?php

namespace App\Livewire\Admin;

use App\Models\Classroom;
use App\Models\AcademicYear;
use Livewire\Component;

class ClassroomManager extends Component
{
    public $classrooms;
    public $name; // Untuk input teks nama kelas baru

    // Properti tracking kelas terpilih
    public $selectedClassroomId = null;
    public $selectedClassroom = null;

    public function mount()
    {
        $this->refreshClassrooms();
    }

    public function refreshClassrooms()
    {
        // Hitung total murid aktif yang terdaftar di dalam kelas
        $this->classrooms = Classroom::withCount('students')->orderBy('name')->get();

        if ($this->selectedClassroomId) {
            $this->showStudents($this->selectedClassroomId);
        }
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:50|unique:classrooms,name',
        ]);

        // 1. Cari data Tahun Ajaran yang saat ini sedang AKTIF (bernilai 1)
        $activeYear = AcademicYear::where('is_active', true)->first();

        // Security check: cegah simpan jika belum ada tahun ajaran yang aktif sama sekali
        if (!$activeYear) {
            session()->flash('error', 'Gagal membuat kelas! Silakan aktifkan salah satu Tahun Ajaran terlebih dahulu di menu Tahun Ajaran.');
            return;
        }

        // 2. Simpan kelas baru dengan menyertakan foreign key academic_year_id secara otomatis
        Classroom::create([
            'name' => strtoupper($this->name),
            'academic_year_id' => $activeYear->id // Menyelesaikan QueryException database Anda
        ]);

        $this->reset('name');
        $this->refreshClassrooms();
        session()->flash('success', 'Kelas baru berhasil dibuat!');
    }

    public function showStudents($classroomId)
    {
        $this->selectedClassroomId = $classroomId;
        $this->selectedClassroom = Classroom::with(['students' => function ($query) {
            $query->orderBy('name', 'asc');
        }])->findOrFail($classroomId);
    }

    public function delete($id)
    {
        Classroom::findOrFail($id)->delete();

        if ($this->selectedClassroomId == $id) {
            $this->reset(['selectedClassroomId', 'selectedClassroom']);
        }

        $this->refreshClassrooms();
        session()->flash('success', 'Kelas berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.admin.classroom-manager');
    }
}
