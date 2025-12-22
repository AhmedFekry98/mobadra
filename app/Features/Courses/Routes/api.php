<?php

use App\Features\Courses\Controllers\TermController;
use App\Features\Courses\Controllers\CourseController;
use App\Features\Courses\Controllers\ChapterController;
use App\Features\Courses\Controllers\LessonController;
use App\Features\Courses\Controllers\LessonContentController;
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

