<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin
        User::create([
            'name' => 'Administrator Utama',
            'email' => 'admin@school.id',
            'password' => Hash::make('password'),
            'role' => 'Admin'
        ]);

        // 2. Buat Guru Utama (Pak Budi)
        $teacher = User::create([
            'name' => 'Budi Santoso, S.Kom',
            'email' => 'guru@school.id',
            'password' => Hash::make('password'),
            'role' => 'Guru'
        ]);

        // 3. Buat Guru Kedua (Ibu Siti) untuk simulasi Guru Pengganti / Banyak Guru
        User::create([
            'name' => 'Siti Aminah, M.Pd',
            'email' => 'siti@school.id',
            'password' => Hash::make('password'),
            'role' => 'Guru'
        ]);

        // 2. Buat Tahun Ajaran Aktif
        $academicYear = AcademicYear::create([
            'year' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        // 3. Buat Mata Pelajaran
        $mtk = Subject::create(['code' => 'MTK', 'name' => 'Matematika']);
        $bing = Subject::create(['code' => 'BING', 'name' => 'Bahasa Inggris']);

        // 4. Buat Kelas
        $classroom = Classroom::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'XII-RPL-1',
            'teacher_id' => $teacher->id, // Pak Budi jadi wali kelas juga
        ]);

        // 5. Buat Data Siswa Dummy
        $students = [
            ['nisn' => '0012345671', 'name' => 'Ahmad Rian', 'gender' => 'L'],
            ['nisn' => '0012345672', 'name' => 'Bunga Citra', 'gender' => 'P'],
            ['nisn' => '0012345673', 'name' => 'Candra Wijaya', 'gender' => 'L'],
            ['nisn' => '0012345674', 'name' => 'Dinda Lestari', 'gender' => 'P'],
            ['nisn' => '0012345675', 'name' => 'Eko Prasetyo', 'gender' => 'L'],
        ];

        foreach ($students as $student) {
            Student::create([
                'classroom_id' => $classroom->id,
                'nisn' => $student['nisn'],
                'name' => $student['name'],
                'gender' => $student['gender'],
            ]);
        }

        // 6. Dapatkan nama hari ini dalam Bahasa Indonesia untuk Schedule
        // (Atau sesuaikan dengan hari saat Anda mencobanya nanti)
        $hariIni = now()->locale('id')->dayName;

        // Buat Jadwal 1: Matematika (Jam ke 1-2)
        Schedule::create([
            'classroom_id' => $classroom->id,
            'subject_id' => $mtk->id,
            'teacher_id' => $teacher->id,
            'day' => $hariIni,
            'period_start' => 1,
            'period_end' => 2,
            'time_start' => '07:00:00',
            'time_end' => '08:30:00',
        ]);

        // Buat Jadwal 2: Bahasa Inggris (Jam ke 3-4)
        Schedule::create([
            'classroom_id' => $classroom->id,
            'subject_id' => $bing->id,
            'teacher_id' => $teacher->id,
            'day' => $hariIni,
            'period_start' => 3,
            'period_end' => 4,
            'time_start' => '08:45:00',
            'time_end' => '10:15:00',
        ]);

        // Buat User Wali Murid
        $parentUser = \App\Models\User::create([
            'name' => 'Hendrawan',
            'email' => 'wali@school.id',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'WaliMurid'
        ]);

        // Ambil salah satu siswa contoh dari seeder Anda (misal siswa pertama)
        $student = \App\Models\Student::first();

        if ($student) {
            // Hubungkan akun wali dengan siswa tersebut ke dalam tabel student_parents
            \DB::table('student_parents')->insert([
                'user_id' => $parentUser->id,
                'student_id' => $student->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
