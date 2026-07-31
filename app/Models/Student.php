<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $guarded = [];

    protected $casts = [
        'bio_details' => 'array', // Otomatis convert JSON ke Array
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function attendanceDetails(): HasMany
    {
        return $this->hasMany(AttendanceDetail::class);
    }
}
