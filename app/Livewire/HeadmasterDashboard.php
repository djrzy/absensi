<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Schedule;
use App\Models\Student;
use Livewire\Component;
use Carbon\Carbon;

class HeadmasterDashboard extends Component
{
    // Filter State
    public $selectedDate;
    public $selectedClassroomId = '';

    public function mount()
    {
        Carbon::setLocale('id');
        $this->selectedDate = Carbon::today()->toDateString();
    }

    public function updatedSelectedDate()
    {
        // Auto refresh saat tanggal diubah
    }

    public function updatedSelectedClassroomId()
    {
        // Auto refresh saat kelas diubah
    }

    public function render()
    {
        $dayName = Carbon::parse($this->selectedDate)->locale('id')->translatedFormat('l');
        $dateFormatted = Carbon::parse($this->selectedDate)->locale('id')->translatedFormat('d F Y');

        // 1. Ambil Seluruh Kelas untuk Dropdown
        $classrooms = Classroom::orderBy('name')->get();

        // 2. Filter Siswa Berdasarkan Kelas (jika dipilih)
        $studentsQuery = Student::query();
        if ($this->selectedClassroomId) {
            $studentsQuery->where('classroom_id', $this->selectedClassroomId);
        }
        $totalStudents = $studentsQuery->count();

        // 3. Tarik Rekapitulasi Presensi Hari & Kelas Terpilih
        $detailsQuery = AttendanceDetail::whereHas('attendance', function ($query) {
            $query->where('date', $this->selectedDate);
            if ($this->selectedClassroomId) {
                $query->whereHas('schedule', function ($sq) {
                    $sq->where('classroom_id', $this->selectedClassroomId);
                });
            }
        });

        // Agregasi Status Absen
        $summary = [
            'Hadir'     => (clone $detailsQuery)->where('status', 'Hadir')->count(),
            'Terlambat' => (clone $detailsQuery)->where('status', 'Terlambat')->count(),
            'Sakit'     => (clone $detailsQuery)->where('status', 'Sakit')->count(),
            'Izin'      => (clone $detailsQuery)->where('status', 'Izin')->count(),
            'Alpa'      => (clone $detailsQuery)->where('status', 'Alpa')->count(),
        ];

        $totalAttended = $summary['Hadir'] + $summary['Terlambat'];
        $totalRecorded = array_sum($summary);
        $attendancePercentage = $totalRecorded > 0 ? round(($totalAttended / $totalRecorded) * 100, 1) : 0;

        // 4. Monitoring Pengisian Jam Mengajar Guru pada Hari Tersebut
        $schedulesQuery = Schedule::with(['classroom', 'subject', 'teacher'])
            ->where('day', $dayName);

        if ($this->selectedClassroomId) {
            $schedulesQuery->where('classroom_id', $this->selectedClassroomId);
        }

        $schedulesToday = $schedulesQuery->orderBy('period_start')->get();

        // Check Jadwal mana yang sudah di-absen di DB
        $filledAttendances = Attendance::where('date', $this->selectedDate)
            ->with(['teacher', 'details'])
            ->get()
            ->keyBy('schedule_id');

        $scheduleMonitoring = $schedulesToday->map(function ($sch) use ($filledAttendances) {
            $attendance = $filledAttendances->get($sch->id);
            $isFilled = $attendance ? true : false;

            return (object) [
                'schedule'     => $sch,
                'isFilled'     => $isFilled,
                'isLocked'     => $attendance?->is_locked ?? false,
                'inputBy'      => $attendance?->teacher->name ?? '-',
                'notes'        => $attendance?->notes ?? '-',
                'totalPresent' => $attendance ? $attendance->details->whereIn('status', ['Hadir', 'Terlambat'])->count() : 0,
                'totalAbsent'  => $attendance ? $attendance->details->whereIn('status', ['Sakit', 'Izin', 'Alpa'])->count() : 0,
            ];
        });

        return view('livewire.headmaster-dashboard', [
            'dayName'              => $dayName,
            'dateFormatted'        => $dateFormatted,
            'classrooms'           => $classrooms,
            'totalStudents'        => $totalStudents,
            'summary'              => $summary,
            'attendancePercentage' => $attendancePercentage,
            'scheduleMonitoring'   => $scheduleMonitoring,
        ]);
    }
}
