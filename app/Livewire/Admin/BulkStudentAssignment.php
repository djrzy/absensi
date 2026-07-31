<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\Classroom;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class BulkStudentAssignment extends Component
{
    use WithPagination;

    // Filter State
    public $filterSourceClassroom = 'unassigned'; // Default: siswa belum berkelas
    public $search = '';

    // Target Assignment State
    public $targetClassroomId = '';
    public $selectedStudentIds = [];
    public $selectAll = false;

    public function updatingFilterSourceClassroom()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Ambil seluruh ID siswa yang sesuai dengan filter saat ini
            $this->selectedStudentIds = $this->getFilteredStudentsQuery()
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedStudentIds = [];
        }
    }

    public function resetSelection()
    {
        $this->reset(['selectedStudentIds', 'selectAll']);
    }

    public function assignStudents()
    {
        // Validasi Input
        if (empty($this->selectedStudentIds)) {
            session()->flash('error', 'Silakan pilih minimal satu siswa untuk dimasukkan ke kelas.');
            return;
        }

        if (empty($this->targetClassroomId)) {
            session()->flash('error', 'Silakan pilih Kelas Tujuan terlebih dahulu.');
            return;
        }

        // Eksekusi Massal
        $count = count($this->selectedStudentIds);

        DB::transaction(function () {
            Student::whereIn('id', $this->selectedStudentIds)->update([
                'classroom_id' => $this->targetClassroomId
            ]);
        });

        $targetClass = Classroom::find($this->targetClassroomId);
        session()->flash('success', "Berhasil memasukkan {$count} siswa ke dalam kelas " . ($targetClass->name ?? ''));

        $this->resetSelection();
    }

    private function getFilteredStudentsQuery()
    {
        return Student::when($this->filterSourceClassroom, function ($q) {
            if ($this->filterSourceClassroom === 'unassigned') {
                $q->whereNull('classroom_id');
            } else {
                $q->where('classroom_id', $this->filterSourceClassroom);
            }
        })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name', 'asc');
    }

    public function render()
    {
        $students = $this->getFilteredStudentsQuery()->paginate(15);
        $classrooms = Classroom::orderBy('name', 'asc')->get();

        return view('livewire.admin.bulk-student-assignment', [
            'students' => $students,
            'classrooms' => $classrooms,
            'totalUnassigned' => Student::whereNull('classroom_id')->count()
        ]);
    }
}
