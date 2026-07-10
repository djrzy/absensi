<?php

namespace App\Livewire\Admin;

use App\Models\Classroom;
use App\Models\Student;
use Livewire\Component;

class ClassroomManager extends Component
{
    public $classrooms;
    public $name; // Untuk form tambah kelas

    // Properti baru untuk tracking kelas yang dipilih
    public $selectedClassroomId = null;
    public $selectedClassroom = null;

    public function mount()
    {
        $this->refreshClassrooms();
    }

    public function refreshClassrooms()
    {
        $this->classrooms = Classroom::withCount('students')->orderBy('name')->get();

        // Jika sedang melihat kelas tertentu, refresh juga datanya
        if ($this->selectedClassroomId) {
            $this->showStudents($this->selectedClassroomId);
        }
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:50|unique:classrooms,name',
        ]);

        Classroom::create([
            'name' => strtoupper($this->name),
        ]);

        $this->reset('name');
        $this->refreshClassrooms();
        session()->flash('success', 'Kelas baru berhasil dibuat!');
    }

    // Fungsi baru untuk memuat daftar murid di kelas terpilih
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
