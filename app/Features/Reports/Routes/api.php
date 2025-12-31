<?php

use App\Features\Reports\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix("reports")->name('reports.')->group(function() {

    // Attendance Reports
    Route::get('attendance', [ReportController::class, 'attendanceReport'])->name('attendance.all');
    Route::get('attendance/student/{studentId}', [ReportController::class, 'studentAttendanceReport'])->name('attendance.student');

    // Quiz Reports (Lesson Quizzes)
    Route::get('quizzes', [ReportController::class, 'quizReport'])->name('quizzes.all');
    Route::get('quizzes/student/{studentId}', [ReportController::class, 'studentQuizReport'])->name('quizzes.student');
    Route::get('quizzes/lesson/{lessonId}', [ReportController::class, 'lessonQuizReport'])->name('quizzes.lesson');

    // Video Quiz Reports
    Route::get('video-quizzes', [ReportController::class, 'videoQuizReport'])->name('video_quizzes.all');
    Route::get('video-quizzes/student/{studentId}', [ReportController::class, 'studentVideoQuizReport'])->name('video_quizzes.student');
    Route::get('video-quizzes/lesson/{lessonId}', [ReportController::class, 'lessonVideoQuizReport'])->name('video_quizzes.lesson');

    // Combined Student Report
    Route::get('student/{studentId}', [ReportController::class, 'studentFullReport'])->name('student.full');

});

