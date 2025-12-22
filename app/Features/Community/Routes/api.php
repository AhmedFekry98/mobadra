<?php

use App\Features\Community\Controllers\PostController;
use App\Features\Community\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

// Posts
Route::prefix('community/posts')->name('community.posts.')->group(function () {
    Route::get('', [PostController::class, 'index'])->name('index');
    Route::post('', [PostController::class, 'store'])->name('store');
    Route::get('{id}', [PostController::class, 'show'])->name('show');
    Route::put('{id}', [PostController::class, 'update'])->name('update');
    Route::delete('{id}', [PostController::class, 'destroy'])->name('destroy');
    Route::post('{id}/like', [PostController::class, 'toggleLike'])->name('like');

    // Comments within post
    Route::get('{postId}/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('{postId}/comments', [CommentController::class, 'store'])->name('comments.store');
});

// // Comments (standalone routes)
// Route::prefix('community/comments')->name('community.comments.')->group(function () {
//     Route::put('{id}', [CommentController::class, 'update'])->name('update');
//     Route::delete('{id}', [CommentController::class, 'destroy'])->name('destroy');
//     Route::post('{id}/like', [CommentController::class, 'toggleLike'])->name('like');
//     Route::get('{id}/replies', [CommentController::class, 'replies'])->name('replies');
// });
