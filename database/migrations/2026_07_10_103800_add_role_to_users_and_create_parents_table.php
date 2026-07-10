<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom role di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Guru', 'WaliMurid'])->default('Guru')->after('password');
        });

        // 2. Buat tabel penghubung untuk Wali Murid ke Siswa
        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Akun login wali
            $table->foreignId('student_id')->constrained()->onDelete('cascade'); // Anak yg dipantau
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_parents');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
