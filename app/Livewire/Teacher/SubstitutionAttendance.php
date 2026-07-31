<?php

namespace App\Livewire\Teacher;

use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\User;
use Livewire\Component;

class SubstitutionAttendance extends Component
{
    public $availableSchedules;
    public $currentTeacherId;

    public function mount()
    {
        // Ambil ID Guru yang sedang login
        $this->currentTeacherId = auth()->id() ?? User::first()->id;

        $this->loadSchedules();
    }

    public function loadSchedules()
    {
        $hariIni = now()->locale('id')->dayName;

        // 1. Ambil jadwal HARI INI khusus untuk GURU LAIN (Bukan guru yang sedang login)
        $allTodaySchedules = Schedule::with(['classroom', 'subject', 'teacher'])
            ->where('day', $hariIni)
            ->where('teacher_id', '!=', $this->currentTeacherId) // 👈 FILTER UTAMA: Kecualikan kelas sendiri
            ->orderBy('period_start', 'asc')
            ->get();

        // 2. Ambil ID jadwal yang SUDAH DI-ABSEN hari ini
        $filledAttendanceScheduleIds = Attendance::where('date', now()->toDateString())
            ->pluck('schedule_id')
            ->toArray();

        // 3. Filter: Hanya tampilkan kelas guru lain yang BELUM diisi absensinya
        $this->availableSchedules = $allTodaySchedules->filter(function ($schedule) use ($filledAttendanceScheduleIds) {
            return !in_array($schedule->id, $filledAttendanceScheduleIds);
        });
    }

    public function takeOver($scheduleId)
    {
        return redirect()->route('absensi.take', ['scheduleId' => $scheduleId]);
    }

    public function render()
    {
        return view('livewire.teacher.substitution-attendance');
    }
}
