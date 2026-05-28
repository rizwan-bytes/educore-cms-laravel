<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\StaffAttendanceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Placeholder closure for unbuilt modules
        $ph = fn() => view('admin.placeholder');

        // ── People ──────────────────────────────────────────────
        Route::get('/users',              $ph)->name('users.index');

        // Students CRUD
        Route::get('/students',               [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/data',          [StudentController::class, 'data'])->name('students.data');
        Route::post('/students',              [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{id}/edit',     [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{id}',          [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{id}',       [StudentController::class, 'destroy'])->name('students.destroy');
        Route::patch('/students/{id}/toggle', [StudentController::class, 'toggleStatus'])->name('students.toggle');

        // Teachers CRUD
        Route::get('/teachers',               [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/data',          [TeacherController::class, 'data'])->name('teachers.data');
        Route::post('/teachers',              [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{id}/edit',     [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{id}',          [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{id}',       [TeacherController::class, 'destroy'])->name('teachers.destroy');
        Route::patch('/teachers/{id}/toggle', [TeacherController::class, 'toggleStatus'])->name('teachers.toggle');

        // Classes CRUD
        Route::get('/classes',               [ClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/data',          [ClassController::class, 'data'])->name('classes.data');
        Route::post('/classes',              [ClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{id}/edit',     [ClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{id}',          [ClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{id}',       [ClassController::class, 'destroy'])->name('classes.destroy');
        Route::patch('/classes/{id}/toggle', [ClassController::class, 'toggleStatus'])->name('classes.toggle');

        // Subjects CRUD
        Route::get('/subjects',               [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/data',          [SubjectController::class, 'data'])->name('subjects.data');
        Route::post('/subjects',              [SubjectController::class, 'store'])->name('subjects.store');
        Route::get('/subjects/{id}/edit',     [SubjectController::class, 'edit'])->name('subjects.edit');
        Route::put('/subjects/{id}',          [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{id}',       [SubjectController::class, 'destroy'])->name('subjects.destroy');
        Route::patch('/subjects/{id}/toggle', [SubjectController::class, 'toggleStatus'])->name('subjects.toggle');

        // ── Academics ───────────────────────────────────────────
        // Attendance
        Route::get('/attendance',                [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/data',           [AttendanceController::class, 'data'])->name('attendance.data');
        Route::get('/attendance/mark',           [AttendanceController::class, 'mark'])->name('attendance.mark');
        Route::get('/attendance/students',       [AttendanceController::class, 'students'])->name('attendance.students');
        Route::post('/attendance/store',         [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/timetable',          $ph)->name('timetable.index');
        Route::get('/exams',              $ph)->name('exams.index');
        Route::get('/results',            $ph)->name('results.index');
        Route::get('/report-card',        $ph)->name('report-card.index');

        // ── Finance ─────────────────────────────────────────────
        Route::get('/fees',               $ph)->name('fees.index');
        Route::get('/fee-structures',     $ph)->name('fee-structures.index');
        Route::get('/fee-generate',       $ph)->name('fee-generate.index');
        Route::get('/scholarships',       $ph)->name('scholarships.index');
        Route::get('/fee-analytics',      $ph)->name('fee-analytics.index');
        Route::get('/fee-reports',        $ph)->name('fee-reports.index');
        Route::get('/fee-portal-links',   $ph)->name('fee-portal-links.index');
        Route::get('/payment-proofs',     $ph)->name('payment-proofs.index');

        // ── Diary ───────────────────────────────────────────────
        Route::get('/diary',              $ph)->name('diary.index');
        Route::get('/diary-qr',           $ph)->name('diary-qr.index');

        // ── Communication ────────────────────────────────────────
        // Notices CRUD
        Route::get('/notices',               [NoticeController::class, 'index'])->name('notices.index');
        Route::get('/notices/data',          [NoticeController::class, 'data'])->name('notices.data');
        Route::post('/notices',              [NoticeController::class, 'store'])->name('notices.store');
        Route::get('/notices/{id}/edit',     [NoticeController::class, 'edit'])->name('notices.edit');
        Route::put('/notices/{id}',          [NoticeController::class, 'update'])->name('notices.update');
        Route::delete('/notices/{id}',       [NoticeController::class, 'destroy'])->name('notices.destroy');
        Route::patch('/notices/{id}/toggle', [NoticeController::class, 'toggleStatus'])->name('notices.toggle');
        Route::get('/notifications',      $ph)->name('notifications.index');
        Route::get('/syllabus',           $ph)->name('syllabus.index');
        Route::get('/library',            $ph)->name('library.index');
        Route::get('/at-risk',            $ph)->name('at-risk.index');
        Route::get('/transcripts',        $ph)->name('transcripts.index');

        // ── HR / Staff ───────────────────────────────────────────
        // Staff CRUD
        Route::get('/staff',               [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/data',          [StaffController::class, 'data'])->name('staff.data');
        Route::post('/staff',              [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{id}/edit',     [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{id}',          [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}',       [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::patch('/staff/{id}/toggle', [StaffController::class, 'toggleStatus'])->name('staff.toggle');

        // Staff Attendance
        Route::get('/staff-attendance',          [StaffAttendanceController::class, 'index'])->name('staff-attendance.index');
        Route::get('/staff-attendance/data',     [StaffAttendanceController::class, 'data'])->name('staff-attendance.data');
        Route::get('/staff-attendance/mark',     [StaffAttendanceController::class, 'mark'])->name('staff-attendance.mark');
        Route::get('/staff-attendance/members',  [StaffAttendanceController::class, 'members'])->name('staff-attendance.members');
        Route::post('/staff-attendance/store',   [StaffAttendanceController::class, 'store'])->name('staff-attendance.store');

        Route::get('/leaves',             $ph)->name('leaves.index');
        Route::get('/payroll',            $ph)->name('payroll.index');
        Route::get('/ptm',                $ph)->name('ptm.index');
        Route::get('/admissions',         $ph)->name('admissions.index');

        // ── System ──────────────────────────────────────────────
        Route::get('/profile',            $ph)->name('profile.index');
        Route::get('/security',           $ph)->name('security.index');
        Route::get('/hec-reports',        $ph)->name('hec-reports.index');
        Route::get('/settings',           $ph)->name('settings.index');
    });
