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
    public $todayDate;

    public function mount()
    {
        Carbon::setLocale('id');
        $this->currentDay = Carbon::now()->translatedFormat('l');
        $this->todayDate = Carbon::now()->toDateString();

        $this->refreshJadwal();
    }

    public function refreshJadwal()
    {
        $teacherId = auth()->id();

        $schedules = Schedule::with(['classroom', 'subject'])
            ->where('teacher_id', $teacherId)
            ->where('day', $this->currentDay)
            ->orderBy('period_start', 'asc')
            ->get();

        $todayAttendances = Attendance::where('date', $this->todayDate)
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->get()
            ->keyBy('schedule_id');

        $now = Carbon::now();

        $this->todaySchedules = $schedules->map(function ($sch) use ($todayAttendances, $now) {
            $startTime = Carbon::parse($sch->time_start);
            $endTime   = Carbon::parse($sch->time_end);

            $attendance = $todayAttendances->get($sch->id);
            $isFilled   = $attendance ? true : false;
            $isLockedDb = $attendance?->is_locked ?? false;

            // Penentuan Status Akses Jam Realtime
            if ($now->lessThan($startTime)) {
                $timeStatus = 'UPCOMING'; // Belum masuk jam
            } elseif ($now->between($startTime, $endTime)) {
                $timeStatus = 'ACTIVE';   // Jam pelajaran sedang berlangsung
            } else {
                $timeStatus = 'PASSED';   // Jam pelajaran sudah berakhir
            }

            // KUNCI TOTAL: Jika jam sudah LEWAT dan guru BELUM sempat mengisi absennya sama sekali
            $isExpiredUnfilled = ($timeStatus === 'PASSED' && !$isFilled);

            return (object) [
                'schedule'          => $sch,
                'attendance'        => $attendance,
                'isFilled'          => $isFilled,
                'isLockedDb'        => $isLockedDb,
                'timeStatus'        => $timeStatus,
                'isExpiredUnfilled' => $isExpiredUnfilled,
            ];
        });
    }

    public function render()
    {
        return view('livewire.teacher-dashboard');
    }
}
