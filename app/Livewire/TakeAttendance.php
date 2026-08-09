<?php

namespace App\Livewire;

use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\AcademicCalendar;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TakeAttendance extends Component
{
    use WithFileUploads;

    public $scheduleId;
    public $schedule;
    public $students = []; // Disimpan ke properti agar instan di Blade
    public $date;
    public $notes;
    public $isLocked = false;
    public $isLateForAttendance = false;

    public $photoProof;    // File foto baru dari kamera (terkompresi dari client)
    public $existingPhoto; // Foto tersimpan sebelumnya

    public $attendanceData = [];
    public $studentNotes = [];
    public $holidayDescription = null;

    public $inheritedStatuses = [];

    public function mount($scheduleId)
    {
        $this->scheduleId = $scheduleId;
        $this->schedule = Schedule::with(['classroom.students', 'subject'])->findOrFail($scheduleId);
        $this->students = $this->schedule->classroom->students;
        $this->date = Carbon::today()->toDateString();

        $currentTeacherId = auth()->id();
        $currentUserRole = auth()->user()->role ?? '';

        $isOwner = $this->schedule->teacher_id === $currentTeacherId;
        $canTakeAttendance = $isOwner || in_array($currentUserRole, ['Admin', 'Guru', 'Piket', 'GuruPiket']);

        if (!$canTakeAttendance) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk membuka presensi di kelas ini.');
            return redirect('/dashboard');
        }

        // Cek Kalender Akademik (Libur)
        $holiday = AcademicCalendar::where('date', $this->date)->first();
        if ($holiday) {
            $this->isLocked = true;
            $this->holidayDescription = $holiday->description;
        }

        // Indikator Keterlambatan Pengisian (Lebih dari 15 menit)
        $currentTime = Carbon::now();
        $scheduleStartTime = Carbon::parse($this->schedule->time_start);
        if ($currentTime->greaterThan($scheduleStartTime->copy()->addMinutes(15)) && $currentTime->toDateString() == $this->date) {
            $this->isLateForAttendance = true;
        }

        // Load data absensi jika sudah pernah diisi
        $existingAttendance = Attendance::where('schedule_id', $this->scheduleId)
            ->where('date', $this->date)
            ->with('details')
            ->first();

        if ($existingAttendance) {
            $this->notes = $existingAttendance->notes;
            $this->existingPhoto = $existingAttendance->photo_proof ?? null;

            if ($existingAttendance->is_locked) {
                $this->isLocked = true;
            }

            foreach ($existingAttendance->details as $detail) {
                $this->attendanceData[$detail->student_id] = $detail->status;
                $this->studentNotes[$detail->student_id] = $detail->notes;
            }
        } else {
            // Pengisian baru: Cek status warisan dari jam sebelumnya
            foreach ($this->students as $student) {
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
            session()->flash('error', 'Silakan isi dan simpan absensi terlebih dahulu sebelum dikunci.');
        }
    }

    public function save()
    {
        if ($this->isLocked) {
            session()->flash('error', 'Gagal! Absensi hari ini sudah dikunci dan tidak dapat diubah lagi.');
            return;
        }

        // Validasi ketersediaan status presensi seluruh siswa
        foreach ($this->students as $student) {
            if (!isset($this->attendanceData[$student->id]) || is_null($this->attendanceData[$student->id])) {
                session()->flash('error', 'Mohon isi semua absensi siswa sebelum menyimpan!');
                return;
            }
        }

        // Validasi Foto (Batas ukuran disesuaikan menjadi 5MB)
        $photoRules = $this->existingPhoto ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120' : 'required|image|mimes:jpg,jpeg,png,webp|max:5120';

        $this->validate([
            'photoProof' => $photoRules,
        ], [
            'photoProof.required' => 'Mohon ambil atau unggah foto bukti mengajar di kelas terlebih dahulu sebelum menyimpan.',
            'photoProof.image'    => 'File bukti harus berupa foto/gambar.',
            'photoProof.mimes'    => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'photoProof.max'      => 'Ukuran foto maksimal adalah 5MB.',
        ]);

        DB::transaction(function () {
            $photoPath = $this->existingPhoto;

            if ($this->photoProof) {
                if ($this->existingPhoto && Storage::disk('public')->exists($this->existingPhoto)) {
                    Storage::disk('public')->delete($this->existingPhoto);
                }

                $photoPath = $this->photoProof->store('attendance_proofs', 'public');
            }

            $attendance = Attendance::updateOrCreate(
                ['schedule_id' => $this->scheduleId, 'date' => $this->date],
                [
                    'teacher_id'  => auth()->id() ?: $this->schedule->teacher_id,
                    'notes'       => $this->notes,
                    'photo_proof' => $photoPath
                ]
            );

            foreach ($this->attendanceData as $studentId => $status) {
                AttendanceDetail::updateOrCreate(
                    ['attendance_id' => $attendance->id, 'student_id' => $studentId],
                    ['status' => $status, 'notes' => $this->studentNotes[$studentId] ?? null]
                );
            }
        });

        session()->flash('success', 'Absensi dan foto bukti mengajar berhasil disimpan!');
    }

    public function setAllHadir()
    {
        if ($this->isLocked) return;

        foreach ($this->students as $student) {
            $this->attendanceData[$student->id] = 'Hadir';
        }

        session()->flash('success', 'Berhasil menandai SEMUA SISWA sebagai Hadir. Silakan ubah siswa yang berhalangan jika ada.');
    }

    public function resetAllStatus()
    {
        if ($this->isLocked) return;

        foreach ($this->students as $student) {
            $this->attendanceData[$student->id] = null;
        }
    }

    public function render()
    {
        return view('livewire.take-attendance', [
            'students' => $this->students
        ]);
    }
}
