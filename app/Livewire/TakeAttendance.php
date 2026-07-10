<?php

namespace App\Livewire;

use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TakeAttendance extends Component
{
    public $scheduleId;
    public $schedule;
    public $date;
    public $notes;
    public $isLocked = false;
    public $isLateForAttendance = false; // Deteksi apakah guru telat buka absen

    public $attendanceData = [];
    public $studentNotes = [];
    public $holidayDescription = null;

    public $inheritedStatuses = []; // Array untuk menampung tanda status warisan jam sebelumnya

    public function mount($scheduleId)
    {
        $this->scheduleId = $scheduleId;
        $this->schedule = Schedule::with(['classroom.students', 'subject'])->findOrFail($scheduleId);
        $this->date = Carbon::today()->toDateString();

        $currentTeacherId = auth()->id();
        $currentUserRole = auth()->user()->role; // Ambil role user aktif

        $isFilled = Attendance::where('schedule_id', $this->scheduleId)->where('date', $this->date)->exists();

        // --- PENGUATAN LOGIKA HAK AKSES ---
        // HANYA blokir jika user yang masuk BUKAN Admin, BUKAN pemilik jadwal, DAN jadwal tersebut sudah diisi.
        if ($currentUserRole !== 'Admin' && $this->schedule->teacher_id !== $currentTeacherId && $isFilled) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk mengubah presensi di kelas ini.');
            return redirect('/dashboard');
        }

        // --- CHECK KALENDER AKADEMIK (HARI LIBUR) ---
        $holiday = \App\Models\AcademicCalendar::where('date', $this->date)->first();
        if ($holiday) {
            $this->isLocked = true;
            $this->holidayDescription = $holiday->description;
        }

        // Cek toleransi keterlambatan jadwal
        $currentTime = Carbon::now();
        $scheduleStartTime = Carbon::parse($this->schedule->time_start);
        if ($currentTime->greaterThan($scheduleStartTime->copy()->addMinutes(15)) && $currentTime->toDateString() == $this->date) {
            $this->isLateForAttendance = true;
        }

        // Cek record absensi yang sudah ada
        $existingAttendance = Attendance::where('schedule_id', $this->scheduleId)
            ->where('date', $this->date)
            ->with('details')
            ->first();

        if ($existingAttendance) {
            $this->notes = $existingAttendance->notes;

            // Jika absensi sudah dikunci permanen ATAU user yang masuk bukan Admin/pemilik jadwal, set Read-Only
            if ($existingAttendance->is_locked || ($currentUserRole !== 'Admin' && $this->schedule->teacher_id !== $currentTeacherId)) {
                $this->isLocked = true;
            } else {
                $this->isLocked = false; // Tetap buka edit mode bagi Admin/Pemilik Jadwal selama belum dikunci permanen
            }

            foreach ($existingAttendance->details as $detail) {
                $this->attendanceData[$detail->student_id] = $detail->status;
                $this->studentNotes[$detail->student_id] = $detail->notes;
            }
        } else {
            // Logika Cross-Period Auto-Fill untuk jadwal kosong
            foreach ($this->schedule->classroom->students as $student) {
                $priorAttendanceDetail = AttendanceDetail::whereHas('attendance', function ($query) {
                    $query->where('date', $this->date);
                })
                    ->where('student_id', $student->id)
                    ->whereIn('status', ['Sakit', 'Izin', 'Alpa'])
                    ->first();

                if ($priorAttendanceDetail) {
                    $this->attendanceData[$student->id] = $priorAttendanceDetail->status;
                    $this->studentNotes[$student->id] = $priorAttendanceDetail->notes ?? 'Otomatis mengikuti status jam sebelumnya.';
                    $this->inheritedStatuses[$student->id] = true;
                } else {
                    $this->attendanceData[$student->id] = null;
                    $this->studentNotes[$student->id] = '';
                    $this->inheritedStatuses[$student->id] = false;
                }
            }
        }
    }

    // Fitur khusus untuk Admin atau Guru guna mengunci absensi secara permanen
    public function lockAttendance()
    {
        $existingAttendance = Attendance::where('schedule_id', $this->scheduleId)
            ->where('date', $this->date)
            ->first();

        if ($existingAttendance) {
            $existingAttendance->update(['is_locked' => true]);
            $this->isLocked = true;
            session()->flash('success', 'Absensi kelas ini telah resmi DIKUNCI secara permanen.');
        } else {
            session()->flash('error', 'Silahkan isi dan simpan absensi terlebih dahulu sebelum dikunci.');
        }
    }

    public function save()
    {
        // --- LOGIKA 2: VALIDASI KUNCI ABSENSI ---
        if ($this->isLocked) {
            session()->flash('error', 'Gagal! Absensi hari ini sudah dikunci dan tidak dapat diubah lagi.');
            return;
        }

        // Validasi pengisian kosong
        foreach ($this->schedule->classroom->students as $student) {
            if (!isset($this->attendanceData[$student->id]) || is_null($this->attendanceData[$student->id])) {
                session()->flash('error', 'Mohon isi semua absensi siswa sebelum menyimpan!');
                return;
            }
        }

        DB::transaction(function () {
            $attendance = Attendance::updateOrCreate(
                ['schedule_id' => $this->scheduleId, 'date' => $this->date],
                ['teacher_id' => auth()->id() ?: $this->schedule->teacher_id, 'notes' => $this->notes]
            );

            foreach ($this->attendanceData as $studentId => $status) {
                AttendanceDetail::updateOrCreate(
                    ['attendance_id' => $attendance->id, 'student_id' => $studentId],
                    ['status' => $status, 'notes' => $this->studentNotes[$studentId] ?? null]
                );
            }
        });

        session()->flash('success', 'Absensi berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.take-attendance', [
            'students' => $this->schedule->classroom->students
        ]);
    }
}
