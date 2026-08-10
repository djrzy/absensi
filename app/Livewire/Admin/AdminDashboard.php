<?php

namespace App\Livewire\Admin;

use App\Models\Classroom;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;

class AdminDashboard extends Component
{
    // Filter State
    public $selectedDate;
    public $selectedClassroomId = '';

    public function mount()
    {
        Carbon::setLocale('id');
        $this->selectedDate = Carbon::today()->toDateString();
    }

    public function unlockAttendance($attendanceId)
    {
        $attendance = Attendance::find($attendanceId);
        if ($attendance) {
            $attendance->update(['is_locked' => false]);
            session()->flash('success', 'Kunci presensi berhasil dibuka kembali oleh Admin.');
        }
    }

    public function render()
    {
        $dayName = Carbon::parse($this->selectedDate)->locale('id')->translatedFormat('l');
        $dateFormatted = Carbon::parse($this->selectedDate)->locale('id')->translatedFormat('d F Y');

        // 1. Quick System Counters
        $totalStudentsCount = Student::count();
        $totalClassroomsCount = Classroom::count();
        $totalTeachersCount = User::where('role', 'Guru')->count();

        // 2. Filter Kelas
        $classrooms = Classroom::orderBy('name')->get();

        // 3. Filter Query Presensi
        $studentsQuery = Student::query();
        if ($this->selectedClassroomId) {
            $studentsQuery->where('classroom_id', $this->selectedClassroomId);
        }
        $totalStudentsInScope = $studentsQuery->count();

        $detailsQuery = AttendanceDetail::whereHas('attendance', function ($query) {
            $query->where('date', $this->selectedDate);
            if ($this->selectedClassroomId) {
                $query->whereHas('schedule', function ($sq) {
                    $sq->where('classroom_id', $this->selectedClassroomId);
                });
            }
        });

        // 4. Summary Status
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

        // 5. Monitoring Control Tower Jam Mengajar
        $schedulesQuery = Schedule::with(['classroom', 'subject', 'teacher'])
            ->where('day', $dayName);

        if ($this->selectedClassroomId) {
            $schedulesQuery->where('classroom_id', $this->selectedClassroomId);
        }

        $schedulesToday = $schedulesQuery->orderBy('period_start')->get();

        $filledAttendances = Attendance::where('date', $this->selectedDate)
            ->with(['teacher', 'details'])
            ->get()
            ->keyBy('schedule_id');

        $scheduleMonitoring = $schedulesToday->map(function ($sch) use ($filledAttendances) {
            $attendance = $filledAttendances->get($sch->id);
            $isFilled = $attendance ? true : false;

            return (object) [
                'schedule'     => $sch,
                'attendanceId' => $attendance?->id,
                'isFilled'     => $isFilled,
                'isLocked'     => $attendance?->is_locked ?? false,
                'inputBy'      => $attendance?->teacher->name ?? '-',
                'notes'        => $attendance?->notes ?? '-',
                'totalPresent' => $attendance ? $attendance->details->whereIn('status', ['Hadir', 'Terlambat'])->count() : 0,
                'totalAbsent'  => $attendance ? $attendance->details->whereIn('status', ['Sakit', 'Izin', 'Alpa'])->count() : 0,
            ];
        });

        return view('livewire.admin.admin-dashboard', [
            'dayName'              => $dayName,
            'dateFormatted'        => $dateFormatted,
            'totalStudentsCount'   => $totalStudentsCount,
            'totalClassroomsCount' => $totalClassroomsCount,
            'totalTeachersCount'   => $totalTeachersCount,
            'classrooms'           => $classrooms,
            'totalStudentsInScope' => $totalStudentsInScope,
            'summary'              => $summary,
            'attendancePercentage' => $attendancePercentage,
            'scheduleMonitoring'   => $scheduleMonitoring,
        ]);
    }
}
