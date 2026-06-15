<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::resource('classes', SchoolClassController::class)->except('show');
    Route::resource('teachers', TeacherController::class)->except('show');
    Route::resource('guardians', GuardianController::class)->except('show');
    Route::resource('students', StudentController::class)->except('show');
});

require __DIR__.'/settings.php';
