<?php

use App\Features\Community\Controllers\PostController;
use App\Features\Community\Controllers\CommentController;
use App\Features\Community\Controllers\ChannelController;
use Illuminate\Support\Facades\Route;


Route::prefix('community')->name('community.')->group(function () {
    // Channels
    Route::get('channels/{id}/posts', [ChannelController::class, 'getPosts'])->name('channels.posts');
    Route::apiResource('channels', ChannelController::class);

    // Posts
    Route::post('posts/{id}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::get('posts/{postId}/comments', [CommentController::class, 'index'])->name('posts.comments.index');
    Route::post('posts/{postId}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::apiResource('posts', PostController::class);
});




