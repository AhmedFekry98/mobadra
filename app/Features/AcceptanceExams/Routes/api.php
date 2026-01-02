<?php

use App\Features\AcceptanceExams\Controllers\AcceptanceExamController;
use Illuminate\Support\Facades\Route;

Route::prefix('acceptance-exams')->group(function () {
    Route::get('/', [AcceptanceExamController::class, 'index']);
    Route::post('/', [AcceptanceExamController::class, 'store']);
    Route::get('/my-exam', [AcceptanceExamController::class, 'getMyExam']);
    Route::get('/my-attempts', [AcceptanceExamController::class, 'getMyAttempts']);

    // Students by acceptance status
    Route::get('/students', [AcceptanceExamController::class, 'getStudentsByAcceptanceStatus']);
    Route::put('/students/{userId}/status', [AcceptanceExamController::class, 'updateStudentAcceptanceStatus']);

    Route::get('/{id}', [AcceptanceExamController::class, 'show']);
    Route::put('/{id}', [AcceptanceExamController::class, 'update']);
    Route::delete('/{id}', [AcceptanceExamController::class, 'destroy']);

    // Questions
    Route::post('/{examId}/questions', [AcceptanceExamController::class, 'storeQuestion']);
    Route::put('/questions/{questionId}', [AcceptanceExamController::class, 'updateQuestion']);
    Route::delete('/questions/{questionId}', [AcceptanceExamController::class, 'destroyQuestion']);

    // Attempts
    Route::get('/{examId}/attempts', [AcceptanceExamController::class, 'getExamAttempts']);
    Route::post('/{examId}/start', [AcceptanceExamController::class, 'startAttempt']);
    Route::post('/attempts/{attemptId}/questions/{questionId}/answer', [AcceptanceExamController::class, 'submitAnswer']);
    Route::post('/attempts/{attemptId}/complete', [AcceptanceExamController::class, 'completeAttempt']);
    Route::get('/attempts/{attemptId}/result', [AcceptanceExamController::class, 'getAttemptResult']);
});
