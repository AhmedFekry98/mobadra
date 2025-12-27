<?php

namespace App\Features\SupportTickets\Controllers;

use App\Features\SupportTickets\Models\SupportTicket;
use App\Features\SupportTickets\Requests\SupportTicketRequest;
use App\Features\SupportTickets\Services\SupportTicketService;
use App\Features\SupportTickets\Transformers\SupportTicketCollection;
use App\Features\SupportTickets\Transformers\SupportTicketResource;
use App\Features\SupportTickets\Metadata\SupportTicketMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class SupportTicketController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected SupportTicketService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', SupportTicket::class);

            $result = $this->service->getAllTickets(
                user: auth()->user(),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new SupportTicketCollection($result)
                    : SupportTicketResource::collection($result),
                "Success"
            );
        }, 'SupportTicketController@index');
    }

    public function store(SupportTicketRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', SupportTicket::class);

            $data = $request->validated();
            $data['user_id'] = auth()->user()->id;

            $ticket = $this->service->storeTicket($data);

            return $this->okResponse(
                SupportTicketResource::make($ticket),
                "Support ticket created successfully"
            );
        }, 'SupportTicketController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $ticket = $this->service->getTicketById($id);
            $this->authorize('view', $ticket);

            return $this->okResponse(
                SupportTicketResource::make($ticket->load(['user', 'assignedTo', 'replies.user'])),
                "Support ticket retrieved successfully"
            );
        }, 'SupportTicketController@show');
    }

    public function update(SupportTicketRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $ticket = $this->service->getTicketById($id);
            $this->authorize('update', $ticket);

            $ticket = $this->service->updateTicket($id, $request->validated());

            return $this->okResponse(
                SupportTicketResource::make($ticket),
                "Support ticket updated successfully"
            );
        }, 'SupportTicketController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $ticket = $this->service->getTicketById($id);
            $this->authorize('delete', $ticket);

            $this->service->deleteTicket($id);

            return $this->okResponse(
                null,
                "Support ticket deleted successfully"
            );
        }, 'SupportTicketController@destroy');
    }

    public function myTickets()
    {
        return $this->executeService(function () {
            $tickets = $this->service->getTicketsByUser(auth()->user()->id);

            return $this->okResponse(
                SupportTicketResource::collection($tickets),
                "My tickets retrieved successfully"
            );
        }, 'SupportTicketController@myTickets');
    }

    public function assignedToMe()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', SupportTicket::class);

            $tickets = $this->service->getAssignedTickets(auth()->user()->id);

            return $this->okResponse(
                SupportTicketResource::collection($tickets),
                "Assigned tickets retrieved successfully"
            );
        }, 'SupportTicketController@assignedToMe');
    }

    public function assign(string $id)
    {
        return $this->executeService(function () use ($id) {
            $ticket = $this->service->getTicketById($id);
            $this->authorize('update', $ticket);

            $assigneeId = request('assigned_to');
            $ticket = $this->service->assignTicket($id, $assigneeId);

            return $this->okResponse(
                SupportTicketResource::make($ticket->load('assignedTo')),
                "Ticket assigned successfully"
            );
        }, 'SupportTicketController@assign');
    }

    public function updateStatus(string $id)
    {
        return $this->executeService(function () use ($id) {
            $ticket = $this->service->getTicketById($id);
            $this->authorize('update', $ticket);

            $status = request('status');
            $ticket = $this->service->updateStatus($id, $status);

            return $this->okResponse(
                SupportTicketResource::make($ticket),
                "Ticket status updated successfully"
            );
        }, 'SupportTicketController@updateStatus');
    }

    public function close(string $id)
    {
        return $this->executeService(function () use ($id) {
            $ticket = $this->service->getTicketById($id);
            $this->authorize('update', $ticket);

            $ticket = $this->service->closeTicket($id);

            return $this->okResponse(
                SupportTicketResource::make($ticket),
                "Ticket closed successfully"
            );
        }, 'SupportTicketController@close');
    }

    public function resolve(string $id)
    {
        return $this->executeService(function () use ($id) {
            $ticket = $this->service->getTicketById($id);
            $this->authorize('update', $ticket);

            $ticket = $this->service->resolveTicket($id);

            return $this->okResponse(
                SupportTicketResource::make($ticket),
                "Ticket resolved successfully"
            );
        }, 'SupportTicketController@resolve');
    }

    public function metadata()
    {
        return $this->executeService(function () {
            return $this->okResponse(
                SupportTicketMetadata::get(),
                "Support ticket metadata retrieved successfully"
            );
        }, 'SupportTicketController@metadata');
    }

    /**
     * Get all tickets for the user who created a specific ticket
     * Useful for support staff to see user's ticket history
     */
    public function getUserTicketsByTicketId(string $ticketId)
    {
        return $this->executeService(function () use ($ticketId) {
            $ticket = $this->service->getTicketById($ticketId);
            $this->authorize('view', $ticket);

            $userTickets = $this->service->getTicketsByUser($ticket->user_id);

            return $this->okResponse(
                SupportTicketResource::collection($userTickets),
                "User tickets retrieved successfully"
            );
        }, 'SupportTicketController@getUserTicketsByTicketId');
    }
}
