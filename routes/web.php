<?php

use App\Livewire\Admin\AcademicYearManager;
use App\Livewire\Admin\AttendanceReport;
use App\Livewire\Admin\BulkStudentAssignment;
use App\Livewire\Admin\CalendarManager;
use App\Livewire\Admin\ClassPromotionManager;
use App\Livewire\Admin\ClassroomManager;
use App\Livewire\Admin\ParentManager;
use App\Livewire\Admin\ScheduleManager;
use App\Livewire\Admin\StudentDetail;
use App\Livewire\Admin\StudentManager;
use App\Livewire\Admin\SubjectManager;
use App\Livewire\Admin\TeacherManager;
use App\Livewire\Auth\Login;
use App\Livewire\HeadmasterDashboard;
use App\Livewire\ParentDashboard;
use App\Livewire\TakeAttendance;
use App\Livewire\Teacher\ClassroomWaliReport;
use App\Livewire\Teacher\SubstitutionAttendance;
use App\Livewire\Teacher\TeacherAttendanceReport;
use App\Livewire\TeacherDashboard;
use Illuminate\Support\Facades\Route;

// Halaman Guest (Belum Login)
Route::get('/login', Login::class)->name('login')->middleware('guest');

// Route khusus yang SUDAH LOGIN
Route::middleware('auth')->group(function () {

    // 1. KELOMPOK GURU (Hanya Guru yang boleh masuk)
    Route::middleware('role:Guru')->group(function () {
        Route::get('/dashboard', TeacherDashboard::class);
        Route::get('/absensi/{scheduleId}', TakeAttendance::class)->name('absensi.take');
        Route::get('/piket', SubstitutionAttendance::class);

        Route::get('/rekap-mapel', TeacherAttendanceReport::class)->name('teacher.rekap');
        Route::get('/rekap-wali-kelas', ClassroomWaliReport::class)->name('teacher.rekap-wali');
    });

    // 2. KELOMPOK ADMIN (Hanya Admin yang boleh masuk)
    Route::middleware('role:Admin')->group(function () {
        Route::get('/admin/tahun-ajaran', AcademicYearManager::class);
        Route::get('/admin/kelas', ClassroomManager::class);
        Route::get('/admin/jadwal', ScheduleManager::class);
        Route::get('/admin/mapel', SubjectManager::class);
        Route::get('/admin/rekap', AttendanceReport::class);
        Route::get('/admin/kalender', CalendarManager::class);
        Route::get('/admin/pindah-kelas', ClassPromotionManager::class);
        Route::get('/admin/penetapan-kelas', BulkStudentAssignment::class);

        Route::get('/admin/guru', TeacherManager::class);
        Route::get('/admin/siswa', StudentManager::class)->name('admin.students');
        Route::get('/admin/siswa/{id}', StudentDetail::class)->name('admin.students.show');
        Route::get('/admin/wali-murid', ParentManager::class);
    });

    Route::middleware('role:WaliMurid')->group(function () {
        Route::get('/', ParentDashboard::class)->name('parent.dashboard');
    });

    Route::middleware('role:Kepala')->group(function () {
        Route::get('/', HeadmasterDashboard::class)->name('headmaster.dashboard');
    });

    // Fitur Logout Singkat
    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
