<?php

namespace App\Features\SupportTickets\Controllers;

use App\Features\SupportTickets\Models\SupportTicket;
use App\Features\SupportTickets\Requests\SupportTicketReplyRequest;
use App\Features\SupportTickets\Services\SupportTicketReplyService;
use App\Features\SupportTickets\Services\SupportTicketService;
use App\Features\SupportTickets\Transformers\SupportTicketReplyResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class SupportTicketReplyController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected SupportTicketReplyService $service,
        protected SupportTicketService $ticketService
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(string $ticketId)
    {
        return $this->executeService(function () use ($ticketId) {
            $ticket = $this->ticketService->getTicketById($ticketId);
            $this->authorize('view', $ticket);

            // Staff can see internal notes
            $includeInternalNotes = auth()->user()->hasPermission('support_tickets.view');

            $replies = $this->service->getRepliesByTicket($ticketId, $includeInternalNotes);

            return $this->okResponse(
                SupportTicketReplyResource::collection($replies),
                "Replies retrieved successfully"
            );
        }, 'SupportTicketReplyController@index');
    }

    public function store(SupportTicketReplyRequest $request, string $ticketId)
    {
        return $this->executeService(function () use ($request, $ticketId) {
            $ticket = $this->ticketService->getTicketById($ticketId);
            $this->authorize('view', $ticket);

            // Check if ticket is closed
            if ($ticket->isClosed()) {
                return $this->errorResponse('Cannot reply to a closed ticket', 422);
            }

            $data = $request->validated();
            $data['ticket_id'] = $ticketId;
            $data['user_id'] = auth()->user()->id;

            // Determine if this is a staff reply
            $data['is_staff_reply'] = auth()->user()->hasPermission('support_tickets.update');

            // Only staff can create internal notes
            if (($data['is_internal_note'] ?? false) && !$data['is_staff_reply']) {
                $data['is_internal_note'] = false;
            }

            $reply = $this->service->storeReply($data);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $reply->addMedia($file)->toMediaCollection('attachments');
                }
            }

            return $this->okResponse(
                SupportTicketReplyResource::make($reply->load('user')),
                "Reply added successfully"
            );
        }, 'SupportTicketReplyController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $reply = $this->service->getReplyById($id);
            $ticket = $this->ticketService->getTicketById($reply->ticket_id);
            $this->authorize('view', $ticket);

            // Check if user can view internal notes
            if ($reply->is_internal_note && !auth()->user()->hasPermission('support_tickets.view')) {
                return $this->errorResponse('Unauthorized', 403);
            }

            return $this->okResponse(
                SupportTicketReplyResource::make($reply->load('user')),
                "Reply retrieved successfully"
            );
        }, 'SupportTicketReplyController@show');
    }

    public function update(SupportTicketReplyRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $reply = $this->service->getReplyById($id);

            // Only the author or staff can update
            if ($reply->user_id !== auth()->user()->id && !auth()->user()->hasPermission('support_tickets.update')) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $reply = $this->service->updateReply($id, $request->validated());

            return $this->okResponse(
                SupportTicketReplyResource::make($reply->load('user')),
                "Reply updated successfully"
            );
        }, 'SupportTicketReplyController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $reply = $this->service->getReplyById($id);

            // Only staff can delete replies
            if (!auth()->user()->hasPermission('support_tickets.delete')) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $this->service->deleteReply($id);

            return $this->okResponse(
                null,
                "Reply deleted successfully"
            );
        }, 'SupportTicketReplyController@destroy');
    }
}
