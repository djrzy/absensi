<?php

namespace App\Livewire;

use App\Models\Schedule;
use App\Models\Attendance;
use Livewire\Component;
use Carbon\Carbon;

class TeacherDashboard extends Component
{
    public $todaySchedules;
    public $currentDay;

    public function mount()
    {
        Carbon::setLocale('id');
        $this->currentDay = Carbon::now()->translatedFormat('l'); // Mengambil nama hari (Senin, Selasa, dst)

        $this->refreshJadwal();
    }

    public function refreshJadwal()
    {
        // PENGUATAN: Filter jadwal HANYA untuk guru yang sedang login
        $teacherId = auth()->id();

        $this->todaySchedules = Schedule::with(['classroom', 'subject'])
            ->where('teacher_id', $teacherId)
            ->where('day', $this->currentDay)
            ->orderBy('period_start', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.teacher-dashboard');
    }
}
