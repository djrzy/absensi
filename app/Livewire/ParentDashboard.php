<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\AttendanceDetail;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParentDashboard extends Component
{
    public $student;
    public $attendanceLogs = []; // Menampung data lini masa presensi
    public $summary = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
    public $currentMonthName;

    public function mount()
    {
        Carbon::setLocale('id');
        $this->currentMonthName = Carbon::now()->translatedFormat('F Y');

        // 1. Cari siswa yang terikat dengan Akun Wali Murid
        $parentRecord = DB::table('student_parents')
            ->where('user_id', auth()->id())
            ->first();

        if (!$parentRecord) return;

        $this->student = Student::with('classroom')->find($parentRecord->student_id);

        if ($this->student) {
            $this->loadDetailedAttendance();
        }
    }

    private function loadDetailedAttendance()
    {
        // 2. Ambil seluruh detail absensi murid pada bulan berjalan, urutkan dari tanggal & jam pelajaran terbaru
        $details = AttendanceDetail::with(['attendance.schedule.subject', 'attendance.schedule'])
            ->where('student_id', $this->student->id)
            ->whereHas('attendance', function ($query) {
                $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year);
            })
            ->get();

        // 3. Hitung ringkasan skor bulanan berdasarkan total baris status murni
        foreach ($details as $detail) {
            $this->summary[$detail->status]++;
        }

        // 4. Grouping data berdasarkan tanggal agar di UI penampilannya tetap rapi per hari
        $groupedByDate = $details->groupBy(function ($item) {
            return $item->attendance->date;
        });

        foreach ($groupedByDate as $date => $records) {
            // Urutkan rekam jam pelajaran di hari itu dari jam pertama (pagi) ke siang
            $sortedRecords = $records->sortBy(function ($item) {
                return $item->attendance->schedule->period_start ?? $item->id;
            });

            $this->attendanceLogs[] = [
                'formatted_date' => Carbon::parse($date)->translatedFormat('l, d F Y'),
                'records' => $sortedRecords
            ];
        }

        // Urutkan grup tanggal agar tanggal terbaru muncul di paling atas dashboard
        usort($this->attendanceLogs, function ($a, $b) {
            return strtotime($b['records']->first()->attendance->date) - strtotime($a['records']->first()->attendance->date);
        });
    }

    public function render()
    {
        return view('livewire.parent-dashboard');
    }
}
