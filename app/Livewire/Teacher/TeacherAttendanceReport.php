<?php

namespace App\Livewire\Teacher;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Student;
use App\Models\AttendanceDetail;
use App\Models\AcademicCalendar;
use Livewire\Component;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TeacherAttendanceReport extends Component
{
    // Filter State Dependent Dropdown
    public $availableClassrooms = []; // List Kelas Unik Guru Ini
    public $availableSubjects = [];   // List Mapel Unik di Kelas Terpilih

    public $selectedClassroomId = null;
    public $selectedSubjectId = null;

    // Filter Rentang Bulan
    public $startMonth;
    public $endMonth;

    public $reportData = [];

    public function mount()
    {
        Carbon::setLocale('id');

        // 1. Ambil seluruh jadwal mengajar milik guru yang sedang login
        $teacherSchedules = Schedule::with(['classroom', 'subject'])
            ->where('teacher_id', auth()->id())
            ->get();

        // 2. Extract daftar Kelas Unik yang diampu
        $this->availableClassrooms = $teacherSchedules
            ->pluck('classroom')
            ->filter()
            ->unique('id')
            ->values();

        if ($this->availableClassrooms->isNotEmpty()) {
            $this->selectedClassroomId = $this->availableClassrooms->first()->id;
            $this->loadSubjectsForSelectedClassroom($teacherSchedules);
        }

        // Default rentang: Bulan saat ini
        $currentMonth = Carbon::now()->format('Y-m');
        $this->startMonth = $currentMonth;
        $this->endMonth = $currentMonth;

        $this->generateReport();
    }

    // Pemicu saat kelas diubah -> Update daftar Mapel (Dependent Dropdown)
    public function updatedSelectedClassroomId()
    {
        $teacherSchedules = Schedule::with(['classroom', 'subject'])
            ->where('teacher_id', auth()->id())
            ->get();

        $this->loadSubjectsForSelectedClassroom($teacherSchedules);
        $this->generateReport();
    }

    public function updatedSelectedSubjectId()
    {
        $this->generateReport();
    }
    public function updatedStartMonth()
    {
        $this->generateReport();
    }
    public function updatedEndMonth()
    {
        $this->generateReport();
    }

    private function loadSubjectsForSelectedClassroom($teacherSchedules)
    {
        if (!$this->selectedClassroomId) {
            $this->availableSubjects = collect();
            $this->selectedSubjectId = null;
            return;
        }

        // Filter Mapel Unik khusus di kelas yang dipilih
        $this->availableSubjects = $teacherSchedules
            ->where('classroom_id', $this->selectedClassroomId)
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->values();

        if ($this->availableSubjects->isNotEmpty()) {
            // Jika mapel saat ini tidak ada di kelas baru, set ke mapel pertama
            if (!$this->availableSubjects->contains('id', $this->selectedSubjectId)) {
                $this->selectedSubjectId = $this->availableSubjects->first()->id;
            }
        } else {
            $this->selectedSubjectId = null;
        }
    }

    public function generateReport()
    {
        if (!$this->selectedClassroomId || !$this->selectedSubjectId || !$this->startMonth || !$this->endMonth) {
            $this->reportData = [];
            return;
        }

        // Validasi format YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', $this->startMonth) || !preg_match('/^\d{4}-\d{2}$/', $this->endMonth)) {
            return;
        }

        if (strcmp($this->startMonth, $this->endMonth) > 0) {
            $this->endMonth = $this->startMonth;
        }

        // Cari seluruh schedule_id milik guru ini untuk kelas & mapel terpilih (Misal jadwal Senin & Kamis)
        $relevantScheduleIds = Schedule::where('teacher_id', auth()->id())
            ->where('classroom_id', $this->selectedClassroomId)
            ->where('subject_id', $this->selectedSubjectId)
            ->pluck('id')
            ->toArray();

        $startCarbon = Carbon::parse($this->startMonth . '-01')->startOfMonth();
        $endCarbon   = Carbon::parse($this->endMonth . '-01')->endOfMonth();

        $startDateStr = $startCarbon->format('Y-m-d');
        $endDateStr   = $endCarbon->format('Y-m-d');

        // Tarik Siswa di kelas terkait
        $students = Student::where('classroom_id', $this->selectedClassroomId)->orderBy('name')->get();

        // Ambil data absensi
        $attendanceDetails = AttendanceDetail::with([
            'attendance.teacher',
            'attendance.schedule.subject'
        ])
            ->whereHas('attendance', function ($query) use ($relevantScheduleIds, $startDateStr, $endDateStr) {
                $query->whereIn('schedule_id', $relevantScheduleIds)
                    ->whereBetween('date', [$startDateStr, $endDateStr]);
            })
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $holidayDates = AcademicCalendar::whereBetween('date', [$startDateStr, $endDateStr])
            ->pluck('description', 'date')
            ->toArray();

        // Generate List Bulan
        $startYear  = (int) $startCarbon->year;
        $startMonth = (int) $startCarbon->month;
        $endYear    = (int) $endCarbon->year;
        $endMonth   = (int) $endCarbon->month;

        $monthsInRange = [];
        $y = $startYear;
        $m = $startMonth;

        while ($y < $endYear || ($y == $endYear && $m <= $endMonth)) {
            $dateObj = Carbon::createFromDate($y, $m, 1)->locale('id');

            $monthsInRange[] = [
                'key'               => $dateObj->format('Y-m'),
                'label'             => $dateObj->translatedFormat('F Y'),
                'year'              => $y,
                'month'             => $m,
                'days_count'        => $dateObj->daysInMonth,
                'first_day_of_week' => $dateObj->dayOfWeekIso
            ];

            $m++;
            if ($m > 12) {
                $m = 1;
                $y++;
            }
        }

        $selectedSubject = Subject::find($this->selectedSubjectId);
        $subjectName = $selectedSubject->name ?? 'Mapel';

        $this->reportData = [];

        foreach ($students as $student) {
            $studentAttendance = $attendanceDetails->get($student->id) ?? collect();

            $summaryTotal = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
            $monthlyBreakdown = [];

            foreach ($monthsInRange as $mInfo) {
                $yearNum  = $mInfo['year'];
                $monthNum = $mInfo['month'];
                $daysStatus = [];

                for ($d = 1; $d <= $mInfo['days_count']; $d++) {
                    $dayStr = str_pad($d, 2, '0', STR_PAD_LEFT);
                    $monthStr = str_pad($monthNum, 2, '0', STR_PAD_LEFT);
                    $fullDate = "{$yearNum}-{$monthStr}-{$dayStr}";
                    $carbonDate = Carbon::createFromDate($yearNum, $monthNum, $d);

                    $dayRecords = $studentAttendance->filter(fn($detail) => $detail->attendance->date == $fullDate);

                    if ($dayRecords->isNotEmpty()) {
                        $countStatus = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
                        $sessionDetails = [];

                        foreach ($dayRecords as $record) {
                            $countStatus[$record->status]++;
                            $sessionDetails[] = [
                                'period'   => $record->attendance->schedule->period_start ?? '-',
                                'subject'  => $subjectName,
                                'time'     => substr($record->attendance->schedule->time_start ?? '00:00', 0, 5) . ' - ' . substr($record->attendance->schedule->time_end ?? '00:00', 0, 5),
                                'status'   => $record->status,
                                'notes'    => $record->notes ?? '-',
                                'input_by' => $record->attendance->teacher->name ?? 'Guru Mapel',
                                'input_at' => $record->created_at ? $record->created_at->format('H:i') . ' WIB' : '-'
                            ];
                        }

                        // TANPA BOBOT 50%: Ambil status murni yang tercatat di database.
                        // Jika dalam 1 hari ada 2 jam ganda mapel ini, prioritaskan status tertinggi/dominan.
                        if ($countStatus['Hadir'] > 0 && $countStatus['Hadir'] >= $countStatus['Terlambat']) {
                            $finalStatus = 'Hadir';
                        } elseif ($countStatus['Terlambat'] > 0) {
                            $finalStatus = 'Terlambat';
                        } else {
                            $absenceStatuses = [
                                'Sakit' => $countStatus['Sakit'],
                                'Izin'  => $countStatus['Izin'],
                                'Alpa'  => $countStatus['Alpa'],
                            ];
                            $finalStatus = array_search(max($absenceStatuses), $absenceStatuses);

                            // Fallback jika nilai maksimum tetap 0
                            if ($absenceStatuses[$finalStatus] === 0) {
                                $finalStatus = $dayRecords->first()->status;
                            }
                        }

                        $daysStatus[$d] = [
                            'day_num'      => $d,
                            'letter'       => substr($finalStatus, 0, 1),
                            'status'       => $finalStatus,
                            'details'      => $sessionDetails,
                            'student_name' => $student->name,
                            'date_text'    => Carbon::parse($fullDate)->locale('id')->translatedFormat('l, d F Y')
                        ];

                        $summaryTotal[$finalStatus]++;
                    } elseif (array_key_exists($fullDate, $holidayDates)) {
                        $daysStatus[$d] = ['day_num' => $d, 'letter' => 'L', 'status' => 'Libur', 'details' => []];
                    } elseif ($carbonDate->isWeekend()) {
                        $daysStatus[$d] = ['day_num' => $d, 'letter' => 'O', 'status' => 'Off', 'details' => []];
                    } else {
                        $daysStatus[$d] = ['day_num' => $d, 'letter' => '-', 'status' => 'Belum Absen', 'details' => []];
                    }
                }

                $grid = [];
                for ($i = 1; $i < $mInfo['first_day_of_week']; $i++) {
                    $grid[] = ['is_empty' => true];
                }
                foreach ($daysStatus as $ds) {
                    $ds['is_empty'] = false;
                    $grid[] = $ds;
                }

                $monthlyBreakdown[$mInfo['key']] = [
                    'label'         => $mInfo['label'],
                    'calendar_grid' => $grid
                ];
            }

            $totalAttended = $summaryTotal['Hadir'] + $summaryTotal['Terlambat'];
            $totalActiveDays = array_sum($summaryTotal);
            $percentage = $totalActiveDays > 0 ? round(($totalAttended / $totalActiveDays) * 100) : 100;

            $this->reportData[] = [
                'nisn'              => $student->nisn,
                'name'              => $student->name,
                'summary'           => $summaryTotal,
                'percentage'        => $percentage,
                'monthly_breakdown' => $monthlyBreakdown
            ];
        }
    }

    public function exportExcel()
    {
        if (empty($this->reportData) || !$this->selectedClassroomId || !$this->selectedSubjectId) return;

        $classroom = Classroom::find($this->selectedClassroomId);
        $subject = Subject::find($this->selectedSubjectId);

        $className = $classroom->name ?? 'Kelas';
        $subjectName = $subject->name ?? 'Mata Pelajaran';

        $startText = Carbon::parse($this->startMonth . '-01')->locale('id')->translatedFormat('F Y');
        $endText   = Carbon::parse($this->endMonth . '-01')->locale('id')->translatedFormat('F Y');
        $periodeText = ($this->startMonth === $this->endMonth) ? $startText : "{$startText} - {$endText}";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Presensi Mapel');

        $sheet->setCellValue('A1', 'REKAPITULASI PRESENSI MATA PELAJARAN');
        $sheet->setCellValue('A2', 'Mapel: ' . $subjectName . ' | Kelas: ' . $className . ' | Periode: ' . $periodeText);
        $sheet->setCellValue('A3', 'Guru Pengampu: ' . auth()->user()->name);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'NO');
        $sheet->setCellValue('B5', 'NISN');
        $sheet->setCellValue('C5', 'NAMA SISWA');
        $sheet->setCellValue('D5', 'HADIR (H)');
        $sheet->setCellValue('E5', 'TERLAMBAT (T)');
        $sheet->setCellValue('F5', 'SAKIT (S)');
        $sheet->setCellValue('G5', 'IZIN (I)');
        $sheet->setCellValue('H5', 'ALPA (A)');
        $sheet->setCellValue('I5', '% KEHADIRAN');

        $row = 6;
        foreach ($this->reportData as $idx => $st) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, "'" . $st['nisn']);
            $sheet->setCellValue('C' . $row, $st['name']);
            $sheet->setCellValue('D' . $row, $st['summary']['Hadir']);
            $sheet->setCellValue('E' . $row, $st['summary']['Terlambat']);
            $sheet->setCellValue('F' . $row, $st['summary']['Sakit']);
            $sheet->setCellValue('G' . $row, $st['summary']['Izin']);
            $sheet->setCellValue('H' . $row, $st['summary']['Alpa']);
            $sheet->setCellValue('I' . $row, $st['percentage'] . '%');
            $row++;
        }

        $sheet->getStyle("A5:I5")->getFont()->setBold(true);
        $sheet->getStyle("A5:I5")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet->getStyle("A5:I" . ($row - 1))->applyFromArray($styleArray);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(15);

        $filename = "Rekap_Mapel_{$subjectName}_{$className}_{$this->startMonth}_sd_{$this->endMonth}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }

    public function render()
    {
        $selectedClassroom = Classroom::find($this->selectedClassroomId);
        $selectedSubject = Subject::find($this->selectedSubjectId);

        $startText = Carbon::parse($this->startMonth . '-01')->locale('id')->translatedFormat('F Y');
        $endText   = Carbon::parse($this->endMonth . '-01')->locale('id')->translatedFormat('F Y');

        $periodeText = ($this->startMonth === $this->endMonth) ? $startText : "{$startText} - {$endText}";

        return view('livewire.teacher.teacher-attendance-report', [
            'selectedClassroom' => $selectedClassroom,
            'selectedSubject'   => $selectedSubject,
            'periodeText'       => $periodeText
        ]);
    }
}
