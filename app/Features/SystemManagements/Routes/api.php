<?php

use App\Features\SystemManagements\Controllers\AuditController;
use App\Features\SystemManagements\Controllers\FAQController;
use App\Features\SystemManagements\Controllers\GeneralSettingController;
use App\Features\SystemManagements\Controllers\PermissionController;
use App\Features\SystemManagements\Controllers\RoleController;
use App\Features\SystemManagements\Controllers\RolePermissionController;
use App\Features\SystemManagements\Controllers\StaffController;
use App\Features\SystemManagements\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix("system-managements")->group(function() {

    Route::prefix("roles")->group(function() {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('metadata', [RoleController::class, 'metadata']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
    });

    Route::prefix("permissions")->group(function() {
        Route::get('/', [PermissionController::class, 'index']);
        Route::get('/{id}', [PermissionController::class, 'show']);

    });

    Route::prefix("staffs")->group(function() {
        Route::get('/', [StaffController::class, 'index']);
        Route::post('/', [StaffController::class, 'store']);
        Route::get('metadata', [StaffController::class, 'metadata']);
        Route::get('/{id}', [StaffController::class, 'show']);
        Route::put('/{id}', [StaffController::class, 'update']);
        Route::delete('/{id}', [StaffController::class, 'destroy']);
    });

    // Route::prefix("user-roles")->group(function() {
    //     Route::post('/', [UserRoleController::class, 'store']);
    // });

    Route::prefix("role-permissions")->group(function() {
        // return permissions by role id
        Route::get('/role/{id}', [RolePermissionController::class, 'getPermissionsByRoleId']);
    });

    Route::prefix("general-settings")->group(function() {
        Route::get('/', [GeneralSettingController::class, 'index']);
        Route::post('/', [GeneralSettingController::class, 'store']);
        Route::get('/{id}', [GeneralSettingController::class, 'show']);
        Route::put('/{id}', [GeneralSettingController::class, 'update']);
        Route::delete('/{id}', [GeneralSettingController::class, 'destroy']);

        // Additional routes for key-based operations
        Route::get('/key/{key}', [GeneralSettingController::class, 'getByKey']);
        Route::put('/key/{key}', [GeneralSettingController::class, 'updateByKey']);
    });

    Route::prefix("audits")->group(function() {
        // Get audit metadata
        Route::get('/metadata', [AuditController::class, 'metadata']);
        Route::delete('/cleanup', [AuditController::class, 'cleanup']);
        Route::get('/{id}', [AuditController::class, 'show']);
        Route::get('/', [AuditController::class, 'index']);
    });

    Route::prefix("faqs")->group(function() {
        // Get FAQ metadata
        Route::get('/metadata', [FAQController::class, 'metadata']);
        // FAQ management
        Route::put('/{id}/toggle-status', [FAQController::class, 'toggleStatus']);

        // Standard CRUD operations
        Route::get('/', [FAQController::class, 'index']);
        Route::post('/', [FAQController::class, 'store']);
        Route::get('/{id}', [FAQController::class, 'show']);
        Route::put('/{id}', [FAQController::class, 'update']);
        Route::delete('/{id}', [FAQController::class, 'destroy']);
    });

});

