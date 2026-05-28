<?php

use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\NoticeController;
use Illuminate\Support\Facades\Route;

Route::prefix('student')
    ->middleware(['auth', 'role:student'])
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Attendance (view own)
        Route::get('/attendance',      [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/data', [AttendanceController::class, 'data'])->name('attendance.data');

        // Notices (read-only)
        Route::get('/notices',         [NoticeController::class, 'index'])->name('notices.index');

        $ph = fn() => view('admin.placeholder');

        // Placeholder routes (future modules)
        Route::get('/timetable',       $ph)->name('timetable.index');
        Route::get('/results',         $ph)->name('results.index');
        Route::get('/report-card',     $ph)->name('report-card.index');
        Route::get('/syllabus',        $ph)->name('syllabus.index');
        Route::get('/library',         $ph)->name('library.index');
        Route::get('/transcript',      $ph)->name('transcript.index');
        Route::get('/fees',            $ph)->name('fees.index');
        Route::get('/diary',           $ph)->name('diary.index');
        Route::get('/profile',         $ph)->name('profile.index');
    });
