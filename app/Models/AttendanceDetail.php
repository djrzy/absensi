<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDetail extends Model
{
    protected $guarded = [];

    // Gunakan boot method untuk merekam history secara otomatis
    protected static function booted()
    {
        // Pemicu saat data pertama kali dibuat
        static::created(function ($detail) {
            AttendanceLog::create([
                'attendance_detail_id' => $detail->id,
                'user_id' => auth()->id() ?: $detail->attendance->teacher_id,
                'action' => 'INSERT',
                'old_status' => null,
                'new_status' => $detail->status,
                'reason' => 'Input awal presensi kelas',
            ]);
        });

        // Pemicu saat data di-update / diubah statusnya
        static::updating(function ($detail) {
            // Hanya rekam jika memang statusnya berubah
            if ($detail->isDirty('status')) {
                AttendanceLog::create([
                    'attendance_detail_id' => $detail->id,
                    'user_id' => auth()->id() ?: $detail->attendance->teacher_id,
                    'action' => 'UPDATE',
                    'old_status' => $detail->getOriginal('status'),
                    'new_status' => $detail->status,
                    'reason' => 'Perubahan status presensi oleh pengajar/piket',
                ]);
            }
        });
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
