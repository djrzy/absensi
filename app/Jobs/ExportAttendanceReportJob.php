<?php

namespace App\Jobs;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\AttendanceDetail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportAttendanceReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classroomId;
    public $startMonth;
    public $endMonth;
    public $userId;

    public function __construct($classroomId, $startMonth, $endMonth, $userId)
    {
        $this->classroomId = $classroomId;
        $this->startMonth = $startMonth;
        $this->endMonth = $endMonth;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        Carbon::setLocale('id');

        $classroom = Classroom::with('waliKelas')->find($this->classroomId);
        if (!$classroom) return;

        $startCarbon = Carbon::parse($this->startMonth . '-01')->startOfMonth();
        $endCarbon   = Carbon::parse($this->endMonth . '-01')->endOfMonth();

        $startDateStr = $startCarbon->format('Y-m-d');
        $endDateStr   = $endCarbon->format('Y-m-d');

        $students = Student::where('classroom_id', $this->classroomId)->orderBy('name')->get();

        $details = AttendanceDetail::whereHas('attendance', function ($query) use ($startDateStr, $endDateStr) {
            $query->whereBetween('date', [$startDateStr, $endDateStr]);
        })
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Presensi');

        // Header Laporan
        $sheet->setCellValue('A1', 'REKAPITULASI PRESENSI SISWA');
        $sheet->setCellValue('A2', "Kelas: {$classroom->name} | Periode: {$this->startMonth} s/d {$this->endMonth}");
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);

        $sheet->setCellValue('A4', 'NO');
        $sheet->setCellValue('B4', 'NISN');
        $sheet->setCellValue('C4', 'NAMA SISWA');
        $sheet->setCellValue('D4', 'HADIR');
        $sheet->setCellValue('E4', 'TERLAMBAT');
        $sheet->setCellValue('F4', 'SAKIT');
        $sheet->setCellValue('G4', 'IZIN');
        $sheet->setCellValue('H4', 'ALPA');

        $row = 5;
        foreach ($students as $idx => $st) {
            $stDetails = $details->get($st->id) ?? collect();
            $counts = $stDetails->groupBy('status')->map->count();

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, "'" . $st->nisn);
            $sheet->setCellValue('C' . $row, $st->name);
            $sheet->setCellValue('D' . $row, $counts['Hadir'] ?? 0);
            $sheet->setCellValue('E' . $row, $counts['Terlambat'] ?? 0);
            $sheet->setCellValue('F' . $row, $counts['Sakit'] ?? 0);
            $sheet->setCellValue('G' . $row, $counts['Izin'] ?? 0);
            $sheet->setCellValue('H' . $row, $counts['Alpa'] ?? 0);
            $row++;
        }

        // Simpan File ke Storage Public
        $fileName = "exports/Rekap_Presensi_{$classroom->name}_{$this->startMonth}_to_{$this->endMonth}_" . time() . ".xlsx";

        $writer = new Xlsx($spreadsheet);
        $tempPath = storage_path('app/public/' . $fileName);

        // Ensure directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $writer->save($tempPath);

        // Opsi: Kirim Notifikasi via Database Notification / Broadcast
    }
}
