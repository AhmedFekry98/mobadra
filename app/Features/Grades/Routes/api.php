<?php

use App\Features\Grades\Controllers\GradeController;
use Illuminate\Support\Facades\Route;

// Grades
Route::prefix('grades')->name('grades.')->group(function () {
    Route::get('metadata', [GradeController::class, 'metadata'])->name('metadata');
    Route::get('active', [GradeController::class, 'active'])->name('active');
    Route::apiResource('', GradeController::class)->parameters(['' => 'grade']);
});
