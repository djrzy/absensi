<?php

namespace App\Livewire\Admin;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;

class ScheduleManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $classrooms;
    public $subjects;
    public $teachers;

    // Form properties (Tambah Data)
    public $classroom_id;
    public $subject_id;
    public $teacher_id;
    public $day;
    public $period_start;
    public $period_end;
    public $time_start = '';
    public $time_end = '';

    // Form properties (Edit Data & Modal)
    public $showEditModal = false;
    public $editingScheduleId = null;
    public $edit_classroom_id;
    public $edit_subject_id;
    public $edit_teacher_id;
    public $edit_day;
    public $edit_period_start;
    public $edit_period_end;
    public $edit_time_start = '';
    public $edit_time_end = '';

    // Filter & Pagination Properties
    public $filter_classroom_id = '';
    public $filter_day = '';
    public $filter_teacher_id = '';
    public $perPage = 10;

    // Bulk Delete Properties
    public array $selectedSchedules = [];
    public bool $selectAll = false;

    // Excel Import Properties
    public $excelFile;
    public $importSummary = null;

    public function mount()
    {
        $this->classrooms = Classroom::orderBy('name', 'asc')->get();
        $this->subjects = Subject::orderBy('name', 'asc')->get();
        $this->teachers = User::where('role', 'Guru')->orderBy('name', 'asc')->get();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filter_classroom_id', 'filter_day', 'filter_teacher_id', 'perPage'])) {
            $this->resetPage();
            $this->selectedSchedules = [];
            $this->selectAll = false;
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedSchedules = array_map('strval', $this->getCurrentPageScheduleIds());
        } else {
            $this->selectedSchedules = [];
        }
    }

    private function getCurrentPageScheduleIds()
    {
        $query = Schedule::query()
            ->join('classrooms', 'schedules.classroom_id', '=', 'classrooms.id')
            ->select('schedules.id'); // Hanya ambil ID milik schedules

        if ($this->filter_classroom_id) {
            $query->where('schedules.classroom_id', $this->filter_classroom_id);
        }

        if ($this->filter_day) {
            $query->where('schedules.day', $this->filter_day);
        }

        if ($this->filter_teacher_id) {
            $query->where('schedules.teacher_id', $this->filter_teacher_id);
        }

        return $query->orderByRaw("FIELD(schedules.day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('classrooms.name', 'asc')
            ->orderBy('schedules.period_start', 'asc')
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();
    }

    public function delete($id)
    {
        Schedule::where('id', $id)->delete();
        $this->selectedSchedules = array_values(array_diff($this->selectedSchedules, [(string)$id, (int)$id]));
        session()->flash('success', 'Jadwal pelajaran berhasil dihapus!');
    }

    public function deleteSelected()
    {
        if (empty($this->selectedSchedules)) {
            return;
        }

        $count = count($this->selectedSchedules);
        Schedule::whereIn('id', $this->selectedSchedules)->delete();

        $this->selectedSchedules = [];
        $this->selectAll = false;

        session()->flash('success', "{$count} jadwal pelajaran berhasil dihapus sekaligus!");
    }

    public function store()
    {
        $this->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id'   => 'required|exists:subjects,id',
            'teacher_id'   => 'required|exists:users,id',
            'day'          => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'period_start' => 'required|integer|min:1',
            'period_end'   => 'required|integer|gte:period_start',
            'time_start'   => 'required|date_format:H:i',
            'time_end'     => 'required|date_format:H:i|after:time_start',
        ], [
            'classroom_id.required' => 'Kelas wajib dipilih.',
            'subject_id.required'   => 'Mata pelajaran wajib dipilih.',
            'teacher_id.required'   => 'Guru pengajar wajib dipilih.',
            'day.required'          => 'Hari wajib dipilih.',
            'period_start.required' => 'Jam ke (mulai) wajib diisi.',
            'period_end.gte'        => 'Jam ke (selesai) harus lebih besar atau sama dengan jam mulai.',
            'time_start.required'   => 'Jam mulai wajib diisi.',
            'time_start.date_format' => 'Format jam mulai harus 24 jam (contoh: 07:00).',
            'time_end.required'     => 'Jam selesai wajib diisi.',
            'time_end.date_format'   => 'Format jam selesai harus 24 jam (contoh: 13:30).',
            'time_end.after'         => 'Jam selesai harus lebih akhir daripada jam mulai.',
        ]);

        Schedule::create([
            'classroom_id' => $this->classroom_id,
            'subject_id'   => $this->subject_id,
            'teacher_id'   => $this->teacher_id,
            'day'          => $this->day,
            'period_start' => $this->period_start,
            'period_end'   => $this->period_end,
            'time_start'   => $this->time_start,
            'time_end'     => $this->time_end,
        ]);

        $this->reset(['classroom_id', 'subject_id', 'teacher_id', 'day', 'period_start', 'period_end', 'time_start', 'time_end']);
        session()->flash('success', 'Jadwal pelajaran berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        $this->editingScheduleId = $schedule->id;
        $this->edit_classroom_id = $schedule->classroom_id;
        $this->edit_subject_id   = $schedule->subject_id;
        $this->edit_teacher_id   = $schedule->teacher_id;
        $this->edit_day          = $schedule->day;
        $this->edit_period_start = $schedule->period_start;
        $this->edit_period_end   = $schedule->period_end;
        $this->edit_time_start   = substr($schedule->time_start, 0, 5);
        $this->edit_time_end     = substr($schedule->time_end, 0, 5);

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset([
            'editingScheduleId',
            'edit_classroom_id',
            'edit_subject_id',
            'edit_teacher_id',
            'edit_day',
            'edit_period_start',
            'edit_period_end',
            'edit_time_start',
            'edit_time_end'
        ]);
    }

    public function update()
    {
        $this->validate([
            'edit_classroom_id' => 'required|exists:classrooms,id',
            'edit_subject_id'   => 'required|exists:subjects,id',
            'edit_teacher_id'   => 'required|exists:users,id',
            'edit_day'          => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'edit_period_start' => 'required|integer|min:1',
            'edit_period_end'   => 'required|integer|gte:edit_period_start',
            'edit_time_start'   => 'required|date_format:H:i',
            'edit_time_end'     => 'required|date_format:H:i|after:edit_time_start',
        ]);

        $schedule = Schedule::findOrFail($this->editingScheduleId);
        $schedule->update([
            'classroom_id' => $this->edit_classroom_id,
            'subject_id'   => $this->edit_subject_id,
            'teacher_id'   => $this->edit_teacher_id,
            'day'          => $this->edit_day,
            'period_start' => $this->edit_period_start,
            'period_end'   => $this->edit_period_end,
            'time_start'   => $this->edit_time_start,
            'time_end'     => $this->edit_time_end,
        ]);

        $this->closeEditModal();
        session()->flash('success', 'Jadwal pelajaran berhasil diperbarui!');
    }

    public function importExcel()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        $filePath = $this->excelFile->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $classroomsMap = Classroom::pluck('id', 'name')
            ->mapWithKeys(fn($id, $name) => [strtoupper(trim($name)) => $id])
            ->toArray();

        $subjectsMapByCode = Subject::pluck('id', 'code')
            ->mapWithKeys(fn($id, $code) => [strtoupper(trim($code)) => $id])
            ->toArray();

        $subjectsMapByName = Subject::pluck('id', 'name')
            ->mapWithKeys(fn($id, $name) => [strtoupper(trim($name)) => $id])
            ->toArray();

        $teachersMapByName = User::where('role', 'Guru')
            ->pluck('id', 'name')
            ->mapWithKeys(fn($id, $name) => [strtoupper(trim($name)) => $id])
            ->toArray();

        $teachersMapByEmail = User::where('role', 'Guru')
            ->pluck('id', 'email')
            ->mapWithKeys(fn($id, $email) => [strtolower(trim($email)) => $id])
            ->toArray();

        $successCount = 0;
        $failedCount = 0;
        $failedDetails = [];

        foreach ($rows as $index => $row) {
            if ($index < 1) continue;

            $day          = trim((string)($row[0] ?? ''));
            $className    = strtoupper(trim((string)($row[1] ?? '')));
            $subjectInput = strtoupper(trim((string)($row[2] ?? '')));
            $teacherInput = trim((string)($row[3] ?? ''));
            $periodStart  = trim((string)($row[4] ?? ''));
            $periodEnd    = trim((string)($row[5] ?? ''));
            $timeStart    = trim((string)($row[6] ?? ''));
            $timeEnd      = trim((string)($row[7] ?? ''));

            if (empty($day) && empty($className) && empty($subjectInput)) continue;

            $validDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $formattedDay = ucfirst(strtolower($day));
            if (!in_array($formattedDay, $validDays)) {
                $failedCount++;
                $failedDetails[] = ['row' => $index + 1, 'reason' => "Hari '{$day}' tidak valid"];
                continue;
            }

            if (!isset($classroomsMap[$className])) {
                $failedCount++;
                $failedDetails[] = ['row' => $index + 1, 'reason' => "Kelas '{$className}' tidak ditemukan"];
                continue;
            }
            $classroomId = $classroomsMap[$className];

            $subjectId = $subjectsMapByCode[$subjectInput] ?? ($subjectsMapByName[$subjectInput] ?? null);
            if (!$subjectId) {
                $failedCount++;
                $failedDetails[] = ['row' => $index + 1, 'reason' => "Mapel '{$subjectInput}' tidak ditemukan"];
                continue;
            }

            $teacherId = $teachersMapByName[strtoupper($teacherInput)] ?? ($teachersMapByEmail[strtolower($teacherInput)] ?? null);
            if (!$teacherId) {
                $failedCount++;
                $failedDetails[] = ['row' => $index + 1, 'reason' => "Guru '{$teacherInput}' tidak ditemukan"];
                continue;
            }

            $timeStart = strlen($timeStart) === 4 ? '0' . $timeStart : $timeStart;
            $timeEnd   = strlen($timeEnd) === 4 ? '0' . $timeEnd : $timeEnd;

            try {
                Schedule::create([
                    'classroom_id' => $classroomId,
                    'subject_id'   => $subjectId,
                    'teacher_id'   => $teacherId,
                    'day'          => $formattedDay,
                    'period_start' => (int)$periodStart ?: 1,
                    'period_end'   => (int)$periodEnd ?: 1,
                    'time_start'   => $timeStart,
                    'time_end'     => $timeEnd,
                ]);
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $failedDetails[] = ['row' => $index + 1, 'reason' => $e->getMessage()];
            }
        }

        $this->importSummary = [
            'success' => $successCount,
            'failed'  => $failedCount,
            'details' => $failedDetails
        ];

        $this->reset('excelFile');
        session()->flash('success_import', "Impor selesai! Berhasil: {$successCount}, Gagal: {$failedCount}");
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = [
            'A1' => 'Hari',
            'B1' => 'Nama Kelas',
            'C1' => 'Nama Mapel',
            'D1' => 'Nama Guru',
            'E1' => 'Jam Ke (Mulai)',
            'F1' => 'Jam Ke (S/D)',
            'G1' => 'Jam Mulai',
            'H1' => 'Jam Selesai'
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'Template_Import_Jadwal_Pelajaran.xlsx');
    }

    public function render()
    {
        $query = Schedule::query()
            ->join('classrooms', 'schedules.classroom_id', '=', 'classrooms.id')
            ->select('schedules.*') // Pastikan ID yang diambil murni milik schedules
            ->with(['classroom', 'subject', 'teacher']);

        if ($this->filter_classroom_id) {
            $query->where('schedules.classroom_id', $this->filter_classroom_id);
        }

        if ($this->filter_day) {
            $query->where('schedules.day', $this->filter_day);
        }

        if ($this->filter_teacher_id) {
            $query->where('schedules.teacher_id', $this->filter_teacher_id);
        }

        $schedules = $query->orderByRaw("FIELD(schedules.day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('classrooms.name', 'asc')
            ->orderBy('schedules.period_start', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.schedule-manager', [
            'schedules' => $schedules
        ]);
    }
}
