<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom is_locked di tabel attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('notes');
        });

        // 2. Modifikasi ENUM di attendance_details untuk mendukung 'Terlambat'
        // Karena mengganti enum bawaan di Laravel membutuhkan sedikit trik SQL murni:
        DB::statement("ALTER TABLE attendance_details MODIFY COLUMN status ENUM('Hadir', 'Sakit', 'Izin', 'Alpa', 'Terlambat') DEFAULT 'Hadir'");
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });

        DB::statement("ALTER TABLE attendance_details MODIFY COLUMN status ENUM('Hadir', 'Sakit', 'Izin', 'Alpa') DEFAULT 'Hadir'");
    }
};
