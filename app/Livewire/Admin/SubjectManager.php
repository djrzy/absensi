<?php

namespace App\Livewire\Admin;

use App\Models\Subject;
use Livewire\Component;

class SubjectManager extends Component
{
    public $subjects;
    public $name;
    public $code;

    public function mount()
    {
        $this->refreshSubjects();
    }

    public function refreshSubjects()
    {
        $this->subjects = Subject::orderBy('name')->get();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:subjects,code',
        ]);

        Subject::create([
            'name' => $this->name,
            'code' => strtoupper($this->code),
        ]);

        $this->reset(['name', 'code']);
        $this->refreshSubjects();
        session()->flash('success', 'Mata Pelajaran baru berhasil ditambahkan!');
    }

    public function delete($id)
    {
        Subject::findOrFail($id)->delete();
        $this->refreshSubjects();
        session()->flash('success', 'Mata Pelajaran berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.admin.subject-manager');
    }
}
