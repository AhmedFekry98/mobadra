<?php

use App\Features\Groups\Controllers\GroupController;
use App\Features\Groups\Controllers\GroupStudentController;
use App\Features\Groups\Controllers\GroupTeacherController;
use App\Features\Groups\Controllers\GroupSessionController;
use App\Features\Groups\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

// Groups
Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('metadata', [GroupController::class, 'metadata'])->name('metadata');
    Route::get('course/{courseId}', [GroupController::class, 'getByCourse'])->name('by_course');
    Route::apiResource('', GroupController::class)->parameters(['' => 'group']);

    // Group Students
    Route::prefix('{groupId}/students')->name('students.')->group(function () {
        Route::get('', [GroupStudentController::class, 'index'])->name('index');
        Route::post('', [GroupStudentController::class, 'store'])->name('store');
        Route::patch('{studentId}/status', [GroupStudentController::class, 'updateStatus'])->name('update_status');
        Route::delete('{studentId}', [GroupStudentController::class, 'destroy'])->name('destroy');
    });

    // Group Teachers
    Route::prefix('{groupId}/teachers')->name('teachers.')->group(function () {
        Route::get('', [GroupTeacherController::class, 'index'])->name('index');
        Route::post('', [GroupTeacherController::class, 'store'])->name('store');
        Route::patch('{teacherId}/primary', [GroupTeacherController::class, 'setPrimary'])->name('set_primary');
        Route::delete('{teacherId}', [GroupTeacherController::class, 'destroy'])->name('destroy');
    });

    // Group Sessions
    Route::prefix('{groupId}/sessions')->name('sessions.')->group(function () {
        Route::get('', [GroupSessionController::class, 'index'])->name('index');
        Route::post('', [GroupSessionController::class, 'store'])->name('store');
    });

    // Group Attendance Report
    Route::get('{groupId}/attendance-report', [AttendanceController::class, 'getGroupReport'])->name('attendance_report');
});

// Group Sessions (standalone routes)
Route::prefix('group-sessions')->name('group_sessions.')->group(function () {
    Route::get('{id}', [GroupSessionController::class, 'show'])->name('show');
    Route::put('{id}', [GroupSessionController::class, 'update'])->name('update');
    Route::patch('{id}', [GroupSessionController::class, 'update']);
    Route::delete('{id}', [GroupSessionController::class, 'destroy'])->name('destroy');
    Route::post('{id}/cancel', [GroupSessionController::class, 'cancel'])->name('cancel');
    Route::get('{id}/join-link', [GroupSessionController::class, 'getJoinLink'])->name('join_link');

    // Session Attendance
    Route::get('{sessionId}/attendance', [AttendanceController::class, 'getBySession'])->name('attendance');
    Route::post('{sessionId}/attendance', [AttendanceController::class, 'recordAttendance'])->name('record_attendance');
    Route::post('{sessionId}/attendance/bulk', [AttendanceController::class, 'bulkRecordAttendance'])->name('bulk_attendance');
    Route::post('{sessionId}/attendance/initialize', [AttendanceController::class, 'initializeSession'])->name('initialize_attendance');
    Route::get('{sessionId}/attendance/stats', [AttendanceController::class, 'getSessionStats'])->name('attendance_stats');
});

// Attendance (standalone routes)
Route::prefix('attendances')->name('attendances.')->group(function () {
    Route::put('{id}', [AttendanceController::class, 'update'])->name('update');
    Route::patch('{id}', [AttendanceController::class, 'update']);
});
