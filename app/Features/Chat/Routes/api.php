<?php

use App\Features\Chat\Controllers\ChatParticipantController;
use App\Features\Chat\Controllers\ConversationController;
use App\Features\Chat\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// Conversations
Route::prefix('conversations')->name('conversations.')->group(function () {
    Route::get('', [ConversationController::class, 'index'])->name('index');
    Route::post('', [ConversationController::class, 'store'])->name('store');
    Route::get('{id}', [ConversationController::class, 'show'])->name('show');
    Route::post('{id}/read', [ConversationController::class, 'markAsRead'])->name('read');
    Route::post('{id}/mute', [ConversationController::class, 'mute'])->name('mute');
    Route::post('{id}/unmute', [ConversationController::class, 'unmute'])->name('unmute');
    Route::post('{id}/participants', [ConversationController::class, 'addParticipant'])->name('add_participant');
    Route::delete('{id}/participants/{userId}', [ConversationController::class, 'removeParticipant'])->name('remove_participant');

    // Messages within conversation
    Route::get('{conversationId}/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('{conversationId}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('{conversationId}/messages/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::post('{conversationId}/typing', [MessageController::class, 'typing'])->name('typing');
});

// Messages (standalone routes)
Route::prefix('messages')->name('messages.')->group(function () {
    Route::put('{id}', [MessageController::class, 'update'])->name('update');
    Route::patch('{id}', [MessageController::class, 'update']);
    Route::delete('{id}', [MessageController::class, 'destroy'])->name('destroy');
});

// Chat Participants - Get available users for chat based on groups
Route::get('chat-participants', [ChatParticipantController::class, 'index'])->name('chat-participants.index');
