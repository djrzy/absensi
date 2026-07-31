<?php

namespace App\Livewire\Admin;

use App\Models\Classroom;
use App\Models\Student;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ClassPromotionManager extends Component
{
    // Filter & Selection State
    public $sourceClassroomId = null;
    public $targetAction = 'promote'; // 'promote' (Naik Kelas) atau 'graduate' (Lulus)
    public $targetClassroomId = null;

    // Array penampung ID siswa yang dicentang
    public $selectedStudentIds = [];
    public $selectAll = true;

    // Dipanggil saat kelas asal berubah
    public function updatedSourceClassroomId()
    {
        $this->loadStudents();
    }

    // Centang/hapus centang semua siswa
    public function updatedSelectAll($value)
    {
        if ($value && $this->sourceClassroomId) {
            $this->selectedStudentIds = Student::where('classroom_id', $this->sourceClassroomId)
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedStudentIds = [];
        }
    }

    public function loadStudents()
    {
        if ($this->sourceClassroomId) {
            $this->selectedStudentIds = Student::where('classroom_id', $this->sourceClassroomId)
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
            $this->selectAll = true;
        } else {
            $this->selectedStudentIds = [];
        }
    }

    public function executePromotion()
    {
        // 1. Validasi Input
        if (!$this->sourceClassroomId) {
            session()->flash('error', 'Silakan pilih Kelas Asal terlebih dahulu.');
            return;
        }

        if (empty($this->selectedStudentIds)) {
            session()->flash('error', 'Pilih minimal satu siswa untuk diproses.');
            return;
        }

        if ($this->targetAction === 'promote') {
            if (!$this->targetClassroomId) {
                session()->flash('error', 'Silakan pilih Kelas Tujuan untuk kenaikan kelas.');
                return;
            }

            if ($this->sourceClassroomId == $this->targetClassroomId) {
                session()->flash('error', 'Kelas Tujuan tidak boleh sama dengan Kelas Asal.');
                return;
            }
        }

        // 2. Eksekusi Pemindahan / Kelulusan Massal
        DB::transaction(function () {
            if ($this->targetAction === 'promote') {
                // Pindahkan siswa terpilih ke Kelas Tujuan
                Student::whereIn('id', $this->selectedStudentIds)->update([
                    'classroom_id' => $this->targetClassroomId
                ]);

                session()->flash('success', count($this->selectedStudentIds) . ' Siswa berhasil dipindahkan / dinaikkan kelas!');
            } else {
                // Skenario Kelulusan: Lepaskan relasi kelas (set classroom_id = NULL)
                Student::whereIn('id', $this->selectedStudentIds)->update([
                    'classroom_id' => null
                ]);

                session()->flash('success', count($this->selectedStudentIds) . ' Siswa berhasil dinyatakan LULUS & dilepaskan dari kelas aktif!');
            }
        });

        // Reset state
        $this->reset(['sourceClassroomId', 'targetClassroomId', 'selectedStudentIds', 'selectAll']);
    }

    public function render()
    {
        $classrooms = Classroom::orderBy('name', 'asc')->get();

        $sourceStudents = collect();
        if ($this->sourceClassroomId) {
            $sourceStudents = Student::where('classroom_id', $this->sourceClassroomId)
                ->orderBy('name', 'asc')
                ->get();
        }

        return view('livewire.admin.class-promotion-manager', [
            'classrooms' => $classrooms,
            'sourceStudents' => $sourceStudents
        ]);
    }
}
