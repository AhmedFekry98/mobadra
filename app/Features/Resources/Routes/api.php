<?php

use App\Features\Resources\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::prefix("resources")->group(function() {
    Route::get('/', [ResourceController::class, 'index']);
    Route::get('metadata', [ResourceController::class, 'metadata']);
    Route::post('/', [ResourceController::class, 'store']);
    Route::get('/{id}', [ResourceController::class, 'show']);
    Route::get('/{id}/download', [ResourceController::class, 'download']);
    Route::put('/{id}', [ResourceController::class, 'update']);
    Route::post('/{id}', [ResourceController::class, 'update']); // For file upload with PUT
    Route::delete('/{id}', [ResourceController::class, 'destroy']);
});
