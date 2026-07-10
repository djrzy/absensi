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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // Guru pengajar (relasi ke tabel users)
            $table->string('day'); // Senin, Selasa, dst.
            $table->integer('period_start'); // Jam ke- (misal: 1)
            $table->integer('period_end');   // Selesai jam ke- (misal: 3)
            $table->time('time_start');      // 07:00
            $table->time('time_end');        // 09:15
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
