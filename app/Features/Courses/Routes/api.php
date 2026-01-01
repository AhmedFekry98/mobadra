<?php

use App\Features\Courses\Controllers\TermController;
use App\Features\Courses\Controllers\CourseController;
use App\Features\Courses\Controllers\LessonController;
use App\Features\Courses\Controllers\LessonContentController;
use App\Features\Courses\Controllers\QuizController;
use App\Features\Courses\Controllers\AssignmentController;
use App\Features\Courses\Controllers\VideoQuizController;
use Illuminate\Support\Facades\Route;

// Terms
Route::prefix('terms')->name('terms.')->group(function () {
    Route::get('metadata', [TermController::class, 'metadata'])->name('metadata');
    Route::apiResource('', TermController::class)->parameters(['' => 'term']);
});

// Courses
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('metadata', [CourseController::class, 'metadata'])->name('metadata');
    Route::get('{courseId}/lessons', [CourseController::class, 'lessons'])->name('lessons');
    Route::apiResource('', CourseController::class)->parameters(['' => 'course']);
});

// Lessons
Route::prefix('lessons')->name('lessons.')->group(function () {
    Route::get('metadata', [LessonController::class, 'metadata'])->name('metadata');
    Route::get('course/{courseId}', [LessonController::class, 'getByCourse'])->name('by_course');
    Route::apiResource('', LessonController::class)->parameters(['' => 'lesson']);
});

// Lesson Contents
Route::prefix('lesson-contents')->name('lesson_contents.')->group(function () {
    Route::get('metadata', [LessonContentController::class, 'metadata'])->name('metadata');
    Route::get('lesson/{lessonId}', [LessonContentController::class, 'getByLesson'])->name('by_lesson');
    Route::get('lesson/{lessonId}/videos', [LessonContentController::class, 'getVideosByLesson'])->name('videos_by_lesson');
    Route::get('lesson/{lessonId}/quizzes', [LessonContentController::class, 'getQuizzesByLesson'])->name('quizzes_by_lesson');
    Route::get('lesson/{lessonId}/assignments', [LessonContentController::class, 'getAssignmentsByLesson'])->name('assignments_by_lesson');
    Route::get('lesson/{lessonId}/materials', [LessonContentController::class, 'getMaterialsByLesson'])->name('materials_by_lesson');
    Route::get('materials/{materialId}/files', [LessonContentController::class, 'getMaterialFiles'])->name('material_files');
    Route::apiResource('', LessonContentController::class)->parameters(['' => 'lesson_content']);
});

// Quizzes
Route::prefix('quizzes')->name('quizzes.')->group(function () {
    Route::get('{id}', [QuizController::class, 'show'])->name('show');
    // Route::get('{id}/results', [QuizController::class, 'quizResults'])->name('results');

    // Questions
    Route::post('{quizId}/questions', [QuizController::class, 'storeQuestion'])->name('questions.store');
    Route::put('questions/{questionId}', [QuizController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('questions/{questionId}', [QuizController::class, 'destroyQuestion'])->name('questions.destroy');

    // start attempt
    Route::post('{quizId}/attempts', [QuizController::class, 'startAttempt'])->name('attempts.start');
    // submit answer
    Route::post('attempts/{attemptId}/questions/{questionId}', [QuizController::class, 'submitAnswer'])->name('attempts.answer');
    // complete attempt
    Route::post('attempts/{attemptId}/complete', [QuizController::class, 'completeAttempt'])->name('attempts.complete');
    // get attempt result
    Route::get('attempts/{attemptId}', [QuizController::class, 'attemptResult'])->name('attempts.result');

    // get all attempts by quiz id
    Route::get('{quizId}/attempts', [QuizController::class, 'attemptsByQuiz'])->name('attempts.byQuiz');

    // Final Quizzes (by Course ID)
    Route::get('course/{courseId}/finals', [QuizController::class, 'indexFinalQuizzes'])->name('finals.index');
    Route::post('course/{courseId}/finals', [QuizController::class, 'storeFinalQuiz'])->name('finals.store');
    Route::get('course/{courseId}/finals/{quizId}', [QuizController::class, 'showFinalQuiz'])->name('finals.show');
    Route::put('course/{courseId}/finals/{quizId}', [QuizController::class, 'updateFinalQuiz'])->name('finals.update');
    Route::delete('course/{courseId}/finals/{quizId}', [QuizController::class, 'destroyFinalQuiz'])->name('finals.destroy');
    Route::post('course/{courseId}/finals/{quizId}/submit', [QuizController::class, 'submitFinalQuiz'])->name('finals.submit');
});

// Assignments
Route::prefix('assignments')->name('assignments.')->group(function () {
    // files
    Route::get('{assignmentId}/files', [AssignmentController::class, 'getFiles'])->name('files.index');
    // add files
    Route::post('{assignmentId}/files', [AssignmentController::class, 'addFiles'])->name('files.add');
    // remove file
    Route::delete('{assignmentId}/files/{mediaId}', [AssignmentController::class, 'removeFile'])->name('files.remove');

    // return submissions
    Route::get('{assignmentId}/submissions', [AssignmentController::class, 'submissions'])->name('submissions.index');
    // create submission
    Route::post('{assignmentId}/submissions', [AssignmentController::class, 'createSubmission'])->name('submissions.store');
    // submit assignment
    Route::post('submissions/{submissionId}/submit', [AssignmentController::class, 'submitAssignment'])->name('submissions.submit');

});

// Video Quizzes (اختبارات الفيديو)
Route::prefix('video-quizzes')->name('video_quizzes.')->group(function () {
    // Get quiz by video content ID
    Route::get('video/{videoContentId}', [VideoQuizController::class, 'show'])->name('show');
    // Create/Update quiz for video
    Route::post('video/{videoContentId}', [VideoQuizController::class, 'store'])->name('store');

    // Questions
    Route::post('{quizId}/questions', [VideoQuizController::class, 'addQuestion'])->name('questions.store');
    Route::put('questions/{questionId}', [VideoQuizController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('questions/{questionId}', [VideoQuizController::class, 'deleteQuestion'])->name('questions.destroy');

    // Student Attempts
    Route::post('{quizId}/attempts', [VideoQuizController::class, 'startAttempt'])->name('attempts.start');
    Route::post('attempts/{attemptId}/answer', [VideoQuizController::class, 'submitAnswer'])->name('attempts.answer');
    Route::post('attempts/{attemptId}/complete', [VideoQuizController::class, 'completeAttempt'])->name('attempts.complete');
    Route::get('attempts/{attemptId}', [VideoQuizController::class, 'attemptResult'])->name('attempts.result');
});


