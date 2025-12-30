<?php

use App\Http\Controllers\ZoomWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Zoom Webhook (no auth required)
Route::post('/webhooks/zoom', [ZoomWebhookController::class, 'handle']);
