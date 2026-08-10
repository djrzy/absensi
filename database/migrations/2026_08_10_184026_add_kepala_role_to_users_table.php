<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum role pada tabel users
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'Guru', 'WaliMurid', 'Kepala') DEFAULT 'Guru'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'Guru', 'WaliMurid') DEFAULT 'Guru'");
    }
};
