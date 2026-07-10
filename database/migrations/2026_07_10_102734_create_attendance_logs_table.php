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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_detail_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable(); // Siapa yang mengubah/menginput (Guru)
            $table->string('action'); // 'INSERT' atau 'UPDATE'
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('reason')->nullable(); // Alasan perubahan jika diperlukan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
