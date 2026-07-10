<?php

namespace App\Livewire\Admin;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Livewire\Component;

class ScheduleManager extends Component
{
    public $schedules;
    public $classrooms;
    public $subjects;
    public $teachers;

    // Form properties
    public $classroom_id;
    public $subject_id;
    public $teacher_id;
    public $day;
    public $period_start;
    public $period_end;
    public $time_start;
    public $time_end;

    // Filter di UI agar admin bisa melihat jadwal per kelas
    public $filter_classroom_id;

    public function mount()
    {
        $this->classrooms = Classroom::all();
        $this->subjects = Subject::all();
        $this->teachers = User::all();
        $this->refreshSchedules();
    }

    public function refreshSchedules()
    {
        $query = Schedule::with(['classroom', 'subject', 'teacher']);

        if ($this->filter_classroom_id) {
            $query->where('classroom_id', $this->filter_classroom_id);
        }

        $this->schedules = $query->orderBy('day')
            ->orderBy('period_start')
            ->get();
    }

    // Dipanggil otomatis oleh Livewire ketika properti filter berubah
    public function updatedFilterClassroomId()
    {
        $this->refreshSchedules();
    }

    public function store()
    {
        $this->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'period_start' => 'required|integer|min:1',
            'period_end' => 'required|integer|gte:period_start',
            'time_start' => 'required',
            'time_end' => 'required',
        ]);

        Schedule::create([
            'classroom_id' => $this->classroom_id,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            'day' => $this->day,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'time_start' => $this->time_start,
            'time_end' => $this->time_end,
        ]);

        $this->reset(['classroom_id', 'subject_id', 'teacher_id', 'day', 'period_start', 'period_end', 'time_start', 'time_end']);
        $this->refreshSchedules();
        session()->flash('success', 'Jadwal pelajaran berhasil ditambahkan!');
    }

    public function delete($id)
    {
        Schedule::findOrFail($id)->delete();
        $this->refreshSchedules();
        session()->flash('success', 'Jadwal pelajaran berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.admin.schedule-manager');
    }
}
