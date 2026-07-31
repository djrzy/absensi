<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Composite Index untuk tabel attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['schedule_id', 'date'], 'idx_attendances_schedule_date');
            $table->index(['date'], 'idx_attendances_date');
        });

        // 2. Composite Index untuk tabel attendance_details
        Schema::table('attendance_details', function (Blueprint $table) {
            $table->index(['student_id', 'attendance_id', 'status'], 'idx_details_student_attendance_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendances_schedule_date');
            $table->dropIndex('idx_attendances_date');
        });

        Schema::table('attendance_details', function (Blueprint $table) {
            $table->dropIndex('idx_details_student_attendance_status');
        });
    }
};
