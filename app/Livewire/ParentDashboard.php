<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\Schedule;
use App\Models\AttendanceDetail;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParentDashboard extends Component
{
    public $students = [];            // Kumpulan model anak yang dimiliki wali ini
    public $selectedStudentId = null; // ID anak yang aktif dipilih
    public $selectedStudent = null;   // Model anak aktif

    public $attendanceLogs = [];
    public $summary = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
    public $currentMonthName;

    public $studentSchedules = [];    // 👈 Properti baru untuk menampung Jadwal Mapel
    public $todayName;                 // 👈 Nama hari ini untuk highlight

    public function mount()
    {
        Carbon::setLocale('id');
        $this->currentMonthName = Carbon::now()->translatedFormat('F Y');
        $this->todayName = Carbon::now()->translatedFormat('l'); // Misal: "Senin", "Selasa"

        $this->loadParentsStudents();
    }

    public function loadParentsStudents()
    {
        $studentIds = DB::table('student_parents')
            ->where('user_id', auth()->id())
            ->pluck('student_id');

        if ($studentIds->isNotEmpty()) {
            $this->students = Student::with('classroom')
                ->whereIn('id', $studentIds)
                ->get();

            if (!$this->selectedStudentId && $this->students->isNotEmpty()) {
                $this->selectedStudentId = $this->students->first()->id;
            }

            $this->loadDetailedAttendance();
            $this->loadStudentSchedules(); // 👈 Panggil fungsi load jadwal
        }
    }

    public function updatedSelectedStudentId()
    {
        $this->loadDetailedAttendance();
        $this->loadStudentSchedules(); // 👈 Update jadwal jika ortu ganti anak
    }

    public function loadStudentSchedules()
    {
        $this->studentSchedules = [];

        if (!$this->selectedStudent || !$this->selectedStudent->classroom_id) {
            return;
        }

        // Ambil seluruh jadwal pelajaran di kelas anak tersebut beserta Mapel dan Guru
        $schedules = Schedule::with(['subject', 'teacher'])
            ->where('classroom_id', $this->selectedStudent->classroom_id)
            ->get();

        // Urutan hari sekolah
        $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Grouping berdasarkan hari dan urutkan jam pelajaran
        $grouped = $schedules->groupBy('day');

        foreach ($dayOrder as $day) {
            if ($grouped->has($day)) {
                $sortedSchedules = $grouped->get($day)->sortBy('period_start');
                $this->studentSchedules[$day] = $sortedSchedules;
            }
        }
    }

    public function loadDetailedAttendance()
    {
        $this->attendanceLogs = [];
        $this->summary = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];

        $this->selectedStudent = $this->students->firstWhere('id', $this->selectedStudentId);

        if (!$this->selectedStudent) return;

        $details = AttendanceDetail::with(['attendance.schedule.subject', 'attendance.schedule'])
            ->where('student_id', $this->selectedStudent->id)
            ->whereHas('attendance', function ($query) {
                $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year);
            })
            ->get();

        foreach ($details as $detail) {
            if (isset($this->summary[$detail->status])) {
                $this->summary[$detail->status]++;
            }
        }

        $groupedByDate = $details->groupBy(function ($item) {
            return $item->attendance->date;
        });

        foreach ($groupedByDate as $date => $records) {
            $sortedRecords = $records->sortBy(function ($item) {
                return $item->attendance->schedule->period_start ?? $item->id;
            });

            $this->attendanceLogs[] = [
                'formatted_date' => Carbon::parse($date)->translatedFormat('l, d F Y'),
                'records' => $sortedRecords
            ];
        }

        usort($this->attendanceLogs, function ($a, $b) {
            return strtotime($b['records']->first()->attendance->date) - strtotime($a['records']->first()->attendance->date);
        });
    }

    public function render()
    {
        return view('livewire.parent-dashboard');
    }
}
