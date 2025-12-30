<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Broadcasting auth for Pusher private channels (API auth with sanctum)
Broadcast::routes(['prefix' => 'api', 'middleware' => ['auth:sanctum']]);
