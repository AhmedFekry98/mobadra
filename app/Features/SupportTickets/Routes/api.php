<?php

use App\Features\SupportTickets\Controllers\SupportTicketController;
use App\Features\SupportTickets\Controllers\SupportTicketReplyController;
use Illuminate\Support\Facades\Route;

Route::prefix('support-tickets')->name('support_tickets.')->group(function () {
    // Metadata
    Route::get('metadata', [SupportTicketController::class, 'metadata'])->name('metadata');

    // // My tickets (for customers)
    // Route::get('my-tickets', [SupportTicketController::class, 'myTickets'])->name('my_tickets');

    // // Assigned to me (for staff)
    // Route::get('assigned-to-me', [SupportTicketController::class, 'assignedToMe'])->name('assigned_to_me');

    // Ticket actions
    Route::post('{id}/assign', [SupportTicketController::class, 'assign'])->name('assign');
    Route::patch('{id}/status', [SupportTicketController::class, 'updateStatus'])->name('update_status');
    // Route::post('{id}/close', [SupportTicketController::class, 'close'])->name('close');
    // Route::post('{id}/resolve', [SupportTicketController::class, 'resolve'])->name('resolve');

    // Get all tickets for user who created a specific ticket
    Route::get('{ticketId}/user-tickets', [SupportTicketController::class, 'getUserTicketsByTicketId'])->name('user_tickets');

    // Standard CRUD
    Route::apiResource('', SupportTicketController::class)->parameters(['' => 'ticket']);

    // Ticket Replies
    Route::prefix('{ticketId}/replies')->name('replies.')->group(function () {
        Route::get('', [SupportTicketReplyController::class, 'index'])->name('index');
        Route::post('', [SupportTicketReplyController::class, 'store'])->name('store');
    });
});

// // Standalone reply routes
// Route::prefix('support-ticket-replies')->name('support_ticket_replies.')->group(function () {
//     Route::get('{id}', [SupportTicketReplyController::class, 'show'])->name('show');
//     Route::put('{id}', [SupportTicketReplyController::class, 'update'])->name('update');
//     Route::patch('{id}', [SupportTicketReplyController::class, 'update']);
//     Route::delete('{id}', [SupportTicketReplyController::class, 'destroy'])->name('destroy');
// });
