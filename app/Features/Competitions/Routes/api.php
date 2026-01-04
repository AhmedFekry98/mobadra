<?php

use App\Features\Competitions\Controllers\CompetitionController;
use App\Features\Competitions\Controllers\CompetitionLevelController;
use Illuminate\Support\Facades\Route;

// Competitions CRUD
Route::prefix('competitions')->name('competitions.')->group(function () {
    Route::get('', [CompetitionController::class, 'index'])->name('index');
    Route::post('', [CompetitionController::class, 'store'])->name('store');
    Route::get('{id}', [CompetitionController::class, 'show'])->name('show');
    Route::patch('{id}', [CompetitionController::class, 'update']);
    Route::delete('{id}', [CompetitionController::class, 'destroy'])->name('destroy');

    // Competition Levels
    Route::get('{competitionId}/levels', [CompetitionLevelController::class, 'index'])->name('levels.index');
});

// Competition Levels CRUD
Route::prefix('competition-levels')->name('competition-levels.')->group(function () {
    Route::post('', [CompetitionLevelController::class, 'store'])->name('store');
    Route::get('{id}', [CompetitionLevelController::class, 'show'])->name('show');
    Route::patch('{id}', [CompetitionLevelController::class, 'update'])->name('update');
    Route::delete('{id}', [CompetitionLevelController::class, 'destroy'])->name('destroy');
    Route::get('{id}/course', [CompetitionLevelController::class, 'course'])->name('course');
});
