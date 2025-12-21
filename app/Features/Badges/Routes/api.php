<?php

use App\Features\Badges\Controllers\BadgeConditionController;
use App\Features\Badges\Controllers\BadgeController;
use App\Features\Badges\Metadata\BadgeMetadata;
use Illuminate\Support\Facades\Route;



Route::prefix('badges')
    ->name('badges.')
    ->group(function () {

        // First: Additional routes for Badge Conditions

        // metadata for conditions
        Route::get('metadata/conditions', [BadgeConditionController::class, 'metadata'])
            ->name('conditions.metadata');

        // Get conditions for a specific badge
        Route::get('{badge}/conditions', [BadgeConditionController::class, 'getBadgeConditionsByBadgeId'])
            ->name('conditions.by_badge');

        // Get list of operators
        Route::get('operators/enum', [BadgeConditionController::class, 'getOperator'])
            ->name('conditions.operators');



        // Second: Resource for Badge Conditions (All general conditions)
        Route::apiResource('conditions', BadgeConditionController::class)
            ->names('badges.conditions');

        // Get metadata for badges (filters, searches, etc.)
        Route::get('metadata', [BadgeController::class, 'metadata'])
            ->name('metadata');

        // Third: Resource for Badges itself
        Route::apiResource('', BadgeController::class)
            ->parameters(['' => 'badge']);

    });
