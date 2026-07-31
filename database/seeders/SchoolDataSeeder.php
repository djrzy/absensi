<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Aktor Pengguna Utama (Admin & Guru)
        $admin = User::create([
            'name' => 'Administrator Utama',
            'email' => 'admin@school.id',
            'password' => Hash::make('password'),
            'role' => 'Admin'
        ]);

        $guru1 = User::create([
            'name' => 'Budi Santoso, S.Kom',
            'email' => 'guru@school.id',
            'password' => Hash::make('password'),
            'role' => 'Guru'
        ]);

        $guru2 = User::create([
            'name' => 'Siti Aminah, M.Pd',
            'email' => 'siti@school.id',
            'password' => Hash::make('password'),
            'role' => 'Guru'
        ]);

        $guru3 = User::create([
            'name' => 'Hendra Wijaya, S.Pd',
            'email' => 'hendra@school.id',
            'password' => Hash::make('password'),
            'role' => 'Guru'
        ]);

        // 2. Tahun Ajaran Aktif
        $academicYear = AcademicYear::create([
            'year' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        // 3. Mata Pelajaran Standar
        $mtk  = Subject::create(['code' => 'MTK', 'name' => 'Matematika']);
        $bing = Subject::create(['code' => 'BING', 'name' => 'Bahasa Inggris']);
        $ipa  = Subject::create(['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam']);
        $bin  = Subject::create(['code' => 'BIN', 'name' => 'Bahasa Indonesia']);

        // 4. Struktur Kelas (Menyiapkan Skenario Pindah Kelas)
        $kelas7A = Classroom::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'VII-A',
            'teacher_id' => $guru1->id, // Pak Budi Wali Kelas 7A
        ]);

        $kelas7B = Classroom::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'VII-B',
            'teacher_id' => $guru2->id, // Ibu Siti Wali Kelas 7B
        ]);

        $kelas8A = Classroom::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'VIII-A',
            'teacher_id' => null, // Belum ada Wali Kelas
        ]);

        // 5. Data Siswa Real Case (Siswa Berkelas & Siswa Impor Hasil Excel)

        // Siswa Kelas VII-A
        $s1 = Student::create([
            'classroom_id' => $kelas7A->id,
            'nisn' => '0081234501',
            'name' => 'Ahmad Rian',
            'gender' => 'L',
            'bio_details' => [
                'nomor_induk' => '202607001',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2012-05-14',
                'agama' => 'Islam',
                'nik' => '3273011405120001',
                'data_ayah' => ['nama' => 'Hendrawan', 'telepon' => '081234567890'],
            ]
        ]);

        $s2 = Student::create([
            'classroom_id' => $kelas7A->id,
            'nisn' => '0081234502',
            'name' => 'Candra Wijaya',
            'gender' => 'L',
        ]);

        $s3 = Student::create([
            'classroom_id' => $kelas7A->id,
            'nisn' => '0081234503',
            'name' => 'Dinda Lestari',
            'gender' => 'P',
        ]);

        // Siswa Kelas VII-B
        $s4 = Student::create([
            'classroom_id' => $kelas7B->id,
            'nisn' => '0081234504',
            'name' => 'Bunga Citra', // Anak ke-2 Pak Hendrawan
            'gender' => 'P',
            'bio_details' => [
                'nomor_induk' => '202607004',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2013-08-20',
                'agama' => 'Islam',
                'data_ayah' => ['nama' => 'Hendrawan', 'telepon' => '081234567890'],
            ]
        ]);

        $s5 = Student::create([
            'classroom_id' => $kelas7B->id,
            'nisn' => '0081234505',
            'name' => 'Eko Prasetyo',
            'gender' => 'L',
        ]);

        // Siswa Hasil Impor Excel (classroom_id = NULL) -> Siap di-Penetapan Kelas Massal
        Student::create([
            'classroom_id' => null,
            'nisn' => '0081234506',
            'name' => 'Fajri Ramadhan (Siswa Impor 1)',
            'gender' => 'L',
            'bio_details' => ['nomor_induk' => '202607006', 'tempat_lahir' => 'Jakarta']
        ]);

        Student::create([
            'classroom_id' => null,
            'nisn' => '0081234507',
            'name' => 'Gita Gutawa (Siswa Impor 2)',
            'gender' => 'P',
            'bio_details' => ['nomor_induk' => '202607007', 'tempat_lahir' => 'Surabaya']
        ]);

        // 6. Akun Wali Murid (Disimulasikan Memiliki 2 Anak)
        $parentUser = User::create([
            'name' => 'Hendrawan',
            'email' => 'wali@school.id',
            'password' => Hash::make('password'),
            'role' => 'WaliMurid'
        ]);

        // Tautkan Pak Hendrawan ke Ahmad Rian (7A) dan Bunga Citra (7B)
        DB::table('student_parents')->insert([
            ['user_id' => $parentUser->id, 'student_id' => $s1->id, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $parentUser->id, 'student_id' => $s4->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 7. Master Jadwal Pelajaran (Hari Ini & Hari Senin)
        $hariIni = now()->locale('id')->dayName; // Mengambil nama hari saat seeder dijalankan

        // Jadwal Hari Ini untuk Kelas VII-A (Bisa langsung diuji presensinya hari ini)
        Schedule::create([
            'classroom_id' => $kelas7A->id,
            'subject_id'   => $mtk->id,
            'teacher_id'   => $guru1->id,
            'day'          => $hariIni,
            'period_start' => 1,
            'period_end'   => 2,
            'time_start'   => '07:00:00',
            'time_end'     => '08:30:00',
        ]);

        Schedule::create([
            'classroom_id' => $kelas7A->id,
            'subject_id'   => $bing->id,
            'teacher_id'   => $guru2->id,
            'day'          => $hariIni,
            'period_start' => 3,
            'period_end'   => 4,
            'time_start'   => '08:45:00',
            'time_end'     => '10:15:00',
        ]);

        // Jadwal Hari Senin untuk Kelas VII-B
        Schedule::create([
            'classroom_id' => $kelas7B->id,
            'subject_id'   => $ipa->id,
            'teacher_id'   => $guru3->id,
            'day'          => 'Senin',
            'period_start' => 1,
            'period_end'   => 2,
            'time_start'   => '07:00:00',
            'time_end'     => '08:30:00',
        ]);
    }
}
