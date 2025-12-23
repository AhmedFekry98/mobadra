<?php

use App\Features\Courses\Controllers\TermController;
use App\Features\Courses\Controllers\CourseController;
use App\Features\Courses\Controllers\ChapterController;
use App\Features\Courses\Controllers\LessonController;
use App\Features\Courses\Controllers\LessonContentController;
use App\Features\Courses\Controllers\QuizController;
use App\Features\Courses\Controllers\AssignmentController;
use Illuminate\Support\Facades\Route;

// Terms
Route::prefix('terms')->name('terms.')->group(function () {
    Route::get('metadata', [TermController::class, 'metadata'])->name('metadata');
    Route::apiResource('', TermController::class)->parameters(['' => 'term']);
});

// Courses
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('metadata', [CourseController::class, 'metadata'])->name('metadata');
    Route::apiResource('', CourseController::class)->parameters(['' => 'course']);
});

// Chapters
Route::prefix('chapters')->name('chapters.')->group(function () {
    Route::get('metadata', [ChapterController::class, 'metadata'])->name('metadata');
    Route::get('course/{courseId}', [ChapterController::class, 'getByCourse'])->name('by_course');
    Route::apiResource('', ChapterController::class)->parameters(['' => 'chapter']);
});

// Lessons
Route::prefix('lessons')->name('lessons.')->group(function () {
    Route::get('metadata', [LessonController::class, 'metadata'])->name('metadata');
    Route::get('chapter/{chapterId}', [LessonController::class, 'getByChapter'])->name('by_chapter');
    Route::apiResource('', LessonController::class)->parameters(['' => 'lesson']);
});

// Lesson Contents
Route::prefix('lesson-contents')->name('lesson_contents.')->group(function () {
    Route::get('metadata', [LessonContentController::class, 'metadata'])->name('metadata');
    Route::get('lesson/{lessonId}', [LessonContentController::class, 'getByLesson'])->name('by_lesson');
    Route::apiResource('', LessonContentController::class)->parameters(['' => 'lesson_content']);
});

// Quizzes
Route::prefix('quizzes')->name('quizzes.')->group(function () {
    Route::get('{id}', [QuizController::class, 'show'])->name('show');
    Route::get('{id}/results', [QuizController::class, 'quizResults'])->name('results');

    // Questions
    Route::post('{quizId}/questions', [QuizController::class, 'storeQuestion'])->name('questions.store');
    Route::put('questions/{questionId}', [QuizController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('questions/{questionId}', [QuizController::class, 'destroyQuestion'])->name('questions.destroy');

    // Attempts (Student)
    Route::post('{quizId}/attempts', [QuizController::class, 'startAttempt'])->name('attempts.start');
    Route::post('attempts/{attemptId}/questions/{questionId}', [QuizController::class, 'submitAnswer'])->name('attempts.answer');
    Route::post('attempts/{attemptId}/complete', [QuizController::class, 'completeAttempt'])->name('attempts.complete');
    Route::get('attempts/{attemptId}', [QuizController::class, 'attemptResult'])->name('attempts.result');
});

// Assignments
Route::prefix('assignments')->name('assignments.')->group(function () {
    Route::get('{assignmentId}/submissions', [AssignmentController::class, 'submissions'])->name('submissions.index');
    Route::get('{assignmentId}/my-submission', [AssignmentController::class, 'mySubmission'])->name('submissions.mine');
    Route::post('{assignmentId}/submissions', [AssignmentController::class, 'createSubmission'])->name('submissions.store');
    Route::post('submissions/{submissionId}/submit', [AssignmentController::class, 'submitAssignment'])->name('submissions.submit');
    Route::post('submissions/{submissionId}/grade', [AssignmentController::class, 'gradeSubmission'])->name('submissions.grade');
});

// My Assignments (Student)
Route::get('my-assignments', [AssignmentController::class, 'myAssignments'])->name('assignments.my');
