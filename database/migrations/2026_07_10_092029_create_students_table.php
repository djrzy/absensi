<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nisn')->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P']);

            // Kolom penampung seluruh data dari 79 kolom template Excel
            $table->json('bio_details')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
