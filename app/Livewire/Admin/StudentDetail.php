<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use Livewire\Component;

class StudentDetail extends Component
{
    public $student;

    public function mount($id)
    {
        // Tarik data siswa beserta relasi kelasnya
        $this->student = Student::with('classroom')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.student-detail')
            ->layout('layouts.app'); // Sesuaikan dengan nama file layout utama Anda
    }
}
