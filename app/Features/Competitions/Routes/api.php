<?php

use App\Features\Competitions\Controllers\CompetitionController;
use Illuminate\Support\Facades\Route;

// Competitions CRUD
Route::prefix('competitions')->name('competitions.')->group(function () {
    Route::get('', [CompetitionController::class, 'index'])->name('index');
    Route::post('', [CompetitionController::class, 'store'])->name('store');
    Route::get('{id}', [CompetitionController::class, 'show'])->name('show');
    Route::patch('{id}', [CompetitionController::class, 'update']);
    Route::delete('{id}', [CompetitionController::class, 'destroy'])->name('destroy');
});
