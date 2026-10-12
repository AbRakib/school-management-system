<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('dashboards')->name('dashboard.')->group(function () {
        Route::get('admin', [DashboardController::class, 'admin'])
            ->middleware('role:super-admin|admin')
            ->name('admin');
        Route::get('teacher', [DashboardController::class, 'teacher'])
            ->middleware('role:teacher')
            ->name('teacher');
        Route::get('student', [DashboardController::class, 'student'])
            ->middleware('role:student')
            ->name('student');
        Route::get('parent', [DashboardController::class, 'parent'])
            ->middleware('role:parent')
            ->name('parent');
    });

    Route::middleware('role:super-admin|admin|teacher')->group(function () {
        Route::get('students/id-cards', [StudentController::class, 'idCards'])
            ->name('students.id-cards');
        Route::resource('students', StudentController::class)
            ->only(['index', 'show'])
            ->whereNumber('student');
    });

    Route::get('notices', [NoticeController::class, 'index'])->name('notices.index');

    Route::middleware('role:super-admin|admin')->group(function () {
        Route::resource('notices', NoticeController::class)->except(['index', 'show']);
        Route::resource('classes', SchoolClassController::class)->except('show');
        Route::resource('subjects', SubjectController::class)->except('show');
        Route::resource('teachers', TeacherController::class)->except('show');
        Route::resource('guardians', GuardianController::class)->except('show');
        Route::resource('students', StudentController::class)
            ->except(['index', 'show'])
            ->whereNumber('student');
    });
});

require __DIR__.'/settings.php';
