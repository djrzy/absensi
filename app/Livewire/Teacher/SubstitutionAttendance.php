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
        // Simulasi Guru yang sedang login (Guru Piket)
        // Nanti ganti dengan auth()->id() jika sistem login sudah siap
        $this->currentTeacherId = User::first()->id;

        $this->loadSchedules();
    }

    public function loadSchedules()
    {
        $hariIni = now()->locale('id')->dayName;

        // 1. Ambil semua jadwal yang berlangsung HARI INI
        $allTodaySchedules = Schedule::with(['classroom', 'subject', 'teacher'])
            ->where('day', $hariIni)
            ->orderBy('period_start', 'asc')
            ->get();

        // 2. Ambil ID jadwal yang SUDAH DI-ABSEN hari ini
        $filledAttendanceScheduleIds = Attendance::where('date', now()->toDateString())
            ->pluck('schedule_id')
            ->toArray();

        // 3. Filter jadwal: Masukkan ke daftar jika BELUM diabsen
        // Ini memberi ruang untuk guru piket menggantikan kelas yang kosong
        $this->availableSchedules = $allTodaySchedules->filter(function ($schedule) use ($filledAttendanceScheduleIds) {
            return !in_array($schedule->id, $filledAttendanceScheduleIds);
        });
    }

    public function takeOver($scheduleId)
    {
        // Arahkan langsung ke lembar absensi yang sudah kita buat sebelumnya
        // Karena di fungsi save() TakeAttendance, teacher_id akan otomatis mencatat siapa yang login/mengisi absen!
        return redirect()->route('absensi.take', ['scheduleId' => $scheduleId]);
    }

    public function render()
    {
        return view('livewire.teacher.substitution-attendance');
    }
}
