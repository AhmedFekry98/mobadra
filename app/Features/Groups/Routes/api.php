<?php

use App\Features\Groups\Controllers\GroupController;
use App\Features\Groups\Controllers\GroupStudentController;
use App\Features\Groups\Controllers\GroupTeacherController;
use App\Features\Groups\Controllers\GroupSessionController;
use App\Features\Groups\Controllers\AttendanceController;
use App\Features\Groups\Controllers\ContentProgressController;
use App\Features\Groups\Controllers\BunnyWebhookController;
use App\Features\Groups\Controllers\ZoomWebhookController;
use Illuminate\Support\Facades\Route;

// Groups
Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('metadata', [GroupController::class, 'metadata'])->name('metadata');
    Route::apiResource('', GroupController::class)->parameters(['' => 'group']);

    // Group Students
    Route::prefix('{groupId}/students')->name('students.')->group(function () {
        // return all student in group
        Route::get('', [GroupStudentController::class, 'index'])->name('index');
        // enroll student to group
        Route::post('', [GroupStudentController::class, 'store'])->name('store');
        // update student status in group
        Route::patch('{studentId}/status', [GroupStudentController::class, 'updateStatus'])->name('update_status');
        // remove student from group
        Route::delete('{studentId}', [GroupStudentController::class, 'destroy'])->name('destroy');
    });

    // Group Teachers
    Route::prefix('{groupId}/teachers')->name('teachers.')->group(function () {
        // return all teachers in group
        Route::get('', [GroupTeacherController::class, 'index'])->name('index');
        // assign teacher to group
        Route::post('', [GroupTeacherController::class, 'store'])->name('store');
        // set primary teacher for group
        Route::patch('{teacherId}/primary', [GroupTeacherController::class, 'setPrimary'])->name('set_primary');
        // remove teacher from group
        Route::delete('{teacherId}', [GroupTeacherController::class, 'destroy'])->name('destroy');
    });

    // Group Sessions
    Route::prefix('{groupId}/sessions')->name('sessions.')->group(function () {
        // return all sessions for group
        Route::get('', [GroupSessionController::class, 'indexByGroup'])->name('index');
        // create new session for group
        Route::post('', [GroupSessionController::class, 'store'])->name('store');
    });

    // // Group Attendance Report
    // Route::get('{groupId}/attendance-report', [AttendanceController::class, 'getGroupReport'])->name('attendance_report');
});

// Group Sessions (standalone routes)
Route::prefix('group-sessions')->name('group_sessions.')->group(function () {
    // return all group sessions
    Route::get('', [GroupSessionController::class, 'index'])->name('index');
    // get session details
    Route::get('{id}', [GroupSessionController::class, 'show'])->name('show');
    // update session details
    Route::patch('{id}', [GroupSessionController::class, 'update'])->name('update');
    // delete session
    Route::delete('{id}', [GroupSessionController::class, 'destroy'])->name('destroy');

    // // Session Attendance
    // Route::get('{sessionId}/attendance', [AttendanceController::class, 'getBySession'])->name('attendance');
    // Route::post('{sessionId}/attendance', [AttendanceController::class, 'recordAttendance'])->name('record_attendance');
    // Route::post('{sessionId}/attendance/bulk', [AttendanceController::class, 'bulkRecordAttendance'])->name('bulk_attendance');
    // Route::post('{sessionId}/attendance/initialize', [AttendanceController::class, 'initializeSession'])->name('initialize_attendance');
    // Route::get('{sessionId}/attendance/stats', [AttendanceController::class, 'getSessionStats'])->name('attendance_stats');
});

// // Attendance (standalone routes)
// Route::prefix('attendances')->name('attendances.')->group(function () {
//     Route::put('{id}', [AttendanceController::class, 'update'])->name('update');
//     Route::patch('{id}', [AttendanceController::class, 'update']);
// });

// // Content Progress (تتبع مشاهدة الفيديوهات)
// Route::prefix('content-progress')->name('content_progress.')->group(function () {
//     // Student routes
//     Route::post('update', [ContentProgressController::class, 'updateProgress'])->name('update');
//     Route::get('content/{lessonContentId}', [ContentProgressController::class, 'getProgress'])->name('get');
//     Route::get('group/{groupId}', [ContentProgressController::class, 'getGroupProgress'])->name('group');
//     Route::post('content/{lessonContentId}/complete', [ContentProgressController::class, 'markCompleted'])->name('complete');

//     // Teacher routes
//     Route::get('group/{groupId}/students', [ContentProgressController::class, 'getStudentsProgress'])->name('students');
//     Route::get('group/{groupId}/course/{courseId}/summary', [ContentProgressController::class, 'getProgressSummary'])->name('summary');
// });

// // Bunny Webhook (بدون authentication)
// Route::post('webhooks/bunny', [BunnyWebhookController::class, 'handleWebhook'])
//     ->name('webhooks.bunny')
//     ->withoutMiddleware(['auth:sanctum']);

// // Zoom Webhook (بدون authentication)
// Route::post('webhooks/zoom', [ZoomWebhookController::class, 'handleWebhook'])
//     ->name('webhooks.zoom')
//     ->withoutMiddleware(['auth:sanctum']);
