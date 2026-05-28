<?php

use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\NoticeController;
use Illuminate\Support\Facades\Route;

Route::prefix('teacher')
    ->middleware(['auth', 'role:teacher'])
    ->name('teacher.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        $ph = fn() => view('admin.placeholder');

        // Attendance
        Route::get('/attendance',           [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/students',  [AttendanceController::class, 'students'])->name('attendance.students');
        Route::post('/attendance/store',    [AttendanceController::class, 'store'])->name('attendance.store');

        // Notices (read-only)
        Route::get('/notices',              [NoticeController::class, 'index'])->name('notices.index');

        // Placeholder routes (future modules)
        Route::get('/timetable',            $ph)->name('timetable.index');
        Route::get('/results',              $ph)->name('results.index');
        Route::get('/diary',                $ph)->name('diary.index');
        Route::get('/diary-analytics',      $ph)->name('diary-analytics.index');
        Route::get('/homework-submissions', $ph)->name('homework-submissions.index');
        Route::get('/diary-templates',      $ph)->name('diary-templates.index');
        Route::get('/syllabus',             $ph)->name('syllabus.index');
        Route::get('/library',              $ph)->name('library.index');
        Route::get('/leaves',               $ph)->name('leaves.index');
        Route::get('/ptm',                  $ph)->name('ptm.index');
        Route::get('/profile',              $ph)->name('profile.index');
    });
