<?php

namespace App\Livewire\Admin;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\AttendanceDetail;
use Livewire\Component;
use Carbon\Carbon;

class AttendanceReport extends Component
{
    public $classrooms;
    public $selectedClassroomId;
    public $selectedMonth;
    public $daysInMonth = [];
    public $reportData = [];

    public function mount()
    {
        $this->classrooms = Classroom::all();
        // Set default filter ke kelas pertama dan bulan sekarang
        $this->selectedClassroomId = $this->classrooms->first()?->id;
        $this->selectedMonth = Carbon::now()->format('Y-m');

        $this->generateReport();
    }

    // Dipanggil otomatis ketika filter di UI berubah
    public function updatedSelectedClassroomId()
    {
        $this->generateReport();
    }

    public function updatedSelectedMonth()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        if (!$this->selectedClassroomId) return;

        // 1. Hitung jumlah hari dalam bulan yang dipilih
        $yearMonth = explode('-', $this->selectedMonth);
        $year = $yearMonth[0];
        $month = $yearMonth[1];

        $startOfMonth = Carbon::createFromDate($year, $month, 1);
        $daysCount = $startOfMonth->daysInMonth;

        $this->daysInMonth = [];
        for ($i = 1; $i <= $daysCount; $i++) {
            $this->daysInMonth[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        // 2. Tarik semua siswa di kelas tersebut
        $students = Student::where('classroom_id', $this->selectedClassroomId)->orderBy('name')->get();

        // 3. Ambil data detail absensi pada bulan tersebut untuk kelas terpilih
        $attendanceDetails = AttendanceDetail::whereHas('attendance', function ($query) use ($year, $month) {
            $query->whereYear('date', $year)->whereMonth('date', $month);
        })
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // 4. Ambil daftar hari libur dari kalender akademik
        $holidayDates = \App\Models\AcademicCalendar::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('description', 'date')
            ->toArray();

        // 5. Strukturkan data ke dalam Array untuk di-render ke Table Matrix
        $this->reportData = [];
        foreach ($students as $student) {
            $studentAttendance = $attendanceDetails->get($student->id) ?? collect();

            $daysStatus = [];
            $summary = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];

            foreach ($this->daysInMonth as $day) {
                $fullDate = "{$year}-{$month}-{$day}";
                $carbonDate = Carbon::createFromDate($year, $month, $day);

                // Cek record absen siswa di tanggal terkait
                $dayRecords = $studentAttendance->filter(function ($detail) use ($fullDate) {
                    return $detail->attendance->date == $fullDate;
                });

                if ($dayRecords->isNotEmpty()) {
                    $totalJam = $dayRecords->count();
                    $countStatus = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
                    $sessionDetails = [];

                    foreach ($dayRecords as $record) {
                        $countStatus[$record->status]++;

                        // Ambil data audit trail untuk keperluan drill-down modal
                        $sessionDetails[] = [
                            'period' => $record->attendance->schedule->period_start ?? '-',
                            'subject' => $record->attendance->schedule->subject->name ?? 'Mapel',
                            'time' => substr($record->attendance->schedule->time_start, 0, 5) . ' - ' . substr($record->attendance->schedule->time_end, 0, 5),
                            'status' => $record->status,
                            'notes' => $record->notes ?? '-',
                            'input_by' => $record->attendance->teacher->name ?? 'Sistem / Guru Piket',
                            'input_at' => $record->created_at->format('H:i') . ' WIB'
                        ];
                    }

                    // Hitung bobot persentase harian (Hadir jika >= 50%)
                    $totalHadirDanTelat = $countStatus['Hadir'] + $countStatus['Terlambat'];
                    if (($totalHadirDanTelat / $totalJam) >= 0.5) {
                        $finalStatus = $countStatus['Hadir'] >= $countStatus['Terlambat'] ? 'Hadir' : 'Terlambat';
                    } else {
                        $absenceStatuses = ['Alpa' => $countStatus['Alpa'], 'Sakit' => $countStatus['Sakit'], 'Izin' => $countStatus['Izin']];
                        $finalStatus = array_search(max($absenceStatuses), $absenceStatuses);
                    }

                    $daysStatus[$day] = [
                        'letter' => substr($finalStatus, 0, 1),
                        'status' => $finalStatus,
                        'details' => $sessionDetails,
                        'student_name' => $student->name,
                        'date_text' => Carbon::parse($fullDate)->translatedFormat('l, d F Y')
                    ];

                    $summary[$finalStatus]++;
                }
                // Skenario Hari Libur / Weekend / Belum Absen
                elseif (array_key_exists($fullDate, $holidayDates)) {
                    $daysStatus[$day] = ['letter' => 'L', 'status' => 'Libur', 'details' => []];
                } elseif ($carbonDate->isWeekend()) {
                    $daysStatus[$day] = ['letter' => 'O', 'status' => 'Off', 'details' => []];
                } else {
                    $daysStatus[$day] = ['letter' => '-', 'status' => 'Belum Absen', 'details' => []];
                }
            }

            // Hitung persentase kehadiran: (Hadir + Terlambat) / Total Hari Aktif
            $totalAttended = $summary['Hadir'] + $summary['Terlambat'];
            $totalActiveDays = array_sum($summary);
            $percentage = $totalActiveDays > 0 ? round(($totalAttended / $totalActiveDays) * 100) : 100;

            $this->reportData[] = [
                'name' => $student->name,
                'days' => $daysStatus,
                'summary' => $summary,
                'percentage' => $percentage
            ];
        }
    }

    public function render()
    {
        return view('livewire.admin.attendance-report');
    }
}
