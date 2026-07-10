<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\Classroom;
use Livewire\Component;
use Livewire\WithPagination;

class StudentManager extends Component
{
    use WithPagination;

    // Properti Form Input (untuk Tambah/Edit)
    public $name, $nisn, $gender, $classroom_id;
    public $selectedStudentId = null;
    public $isEditMode = false;

    // Properti STATE FILTER (Baru)
    public $search = '';
    public $filterClassroom = '';
    public $filterGender = '';

    // Reset halaman/page pagination otomatis ketika filter berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterClassroom()
    {
        $this->resetPage();
    }
    public function updatingFilterGender()
    {
        $this->resetPage();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $this->selectedStudentId,
            'gender' => 'required|in:L,P',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        if ($this->isEditMode) {
            $student = Student::findOrFail($this->selectedStudentId);
            $student->update([
                'name' => $this->name,
                'nisn' => $this->nisn,
                'gender' => $this->gender,
                'classroom_id' => $this->classroom_id,
            ]);
            session()->flash('success', 'Data murid berhasil diperbarui!');
        } else {
            Student::create([
                'name' => $this->name,
                'nisn' => $this->nisn,
                'gender' => $this->gender,
                'classroom_id' => $this->classroom_id,
            ]);
            session()->flash('success', 'Murid baru berhasil didaftarkan!');
        }

        $this->resetInputFields();
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $this->selectedStudentId = $student->id;
        $this->name = $student->name;
        $this->nisn = $student->nisn;
        $this->gender = $student->gender;
        $this->classroom_id = $student->classroom_id;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        Student::findOrFail($id)->delete();
        session()->flash('success', 'Data murid berhasil dihapus!');
    }

    public function resetInputFields()
    {
        $this->reset(['name', 'nisn', 'gender', 'classroom_id', 'selectedStudentId', 'isEditMode']);
    }

    public function render()
    {
        // Jalankan Query Eloquent dengan saringan filter secara dinamis
        $query = Student::with('classroom')
            ->when($this->search, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterClassroom, function ($q) {
                $q->where('classroom_id', $this->filterClassroom);
            })
            ->when($this->filterGender, function ($q) {
                $q->where('gender', $this->filterGender);
            })
            ->orderBy('name', 'asc');

        return view('livewire.admin.student-manager', [
            'students' => $query->paginate(10), // Batasi 10 siswa per halaman
            'classrooms' => Classroom::orderBy('name')->get() // Untuk pilihan dropdown kelas
        ]);
    }
}
