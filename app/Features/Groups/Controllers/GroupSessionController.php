<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Models\GroupSession;
use App\Features\Groups\Requests\GroupSessionRequest;
use App\Features\Groups\Services\GroupSessionService;
use App\Features\Groups\Transformers\GroupSessionResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class GroupSessionController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected GroupSessionService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {

        return $this->executeService(function () {
            $this->authorize('viewAny', Group::class);

            // Clean type parameter (handle malformed query strings)
            $type = request('type');
            if ($type && str_contains($type, '?')) {
                $type = explode('?', $type)[0];
            }

            $sessions = $this->service->getAllSessions(

                user: auth()->user(),
                paginate: request()->has('page'),
                type: $type
            );

            return $this->okResponse(
                request()->has('page')
                    ? GroupSessionResource::collection($sessions)
                    : GroupSessionResource::collection($sessions),
                "All sessions retrieved successfully"
            );
        }, 'GroupSessionController@index');
    }

    public function store(GroupSessionRequest $request, string $groupId)
    {

        return $this->executeService(function () use ($request, $groupId) {
            $this->authorize('create', Group::class);

            $data = $request->validated();
            $data['group_id'] = $groupId;

            $session = $this->service->storeSession($data);

            return $this->okResponse(
                GroupSessionResource::make($session),
                "Session created successfully"
            );
        }, 'GroupSessionController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $session = $this->service->getSessionById($id);
            $this->authorize('view', Group::find($session->group_id));

            return $this->okResponse(
                GroupSessionResource::make($session),
                "Session retrieved successfully"
            );
        }, 'GroupSessionController@show');
    }

    public function update(GroupSessionRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $session = $this->service->getSessionById($id);
            $this->authorize('update', Group::find($session->group_id));

            $session = $this->service->updateSession($id, $request->validated());

            return $this->okResponse(
                GroupSessionResource::make($session),
                "Session updated successfully"
            );
        }, 'GroupSessionController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $session = $this->service->getSessionById($id);
            $this->authorize('delete', Group::find($session->group_id));

            $this->service->deleteSession($id);

            return $this->okResponse(
                null,
                "Session deleted successfully"
            );
        }, 'GroupSessionController@destroy');
    }

    public function indexByGroup(string $groupId)
    {
        return $this->executeService(function () use ($groupId) {
            $this->authorize('viewAny', Group::class);
            $type = request('type');
            $sessions = $this->service->getSessionsByGroup($groupId, $type);

            return $this->okResponse(
                GroupSessionResource::collection($sessions),
                "Sessions retrieved successfully"
            );
        }, 'GroupSessionController@indexByGroup');
    }

    public function getRecordings(string $id)
    {
        return $this->executeService(function () use ($id) {
            $session = $this->service->getSessionById($id);
            $this->authorize('view', Group::find($session->group_id));

            $recordings = $this->service->getSessionRecordings($id);

            return $this->okResponse(
                $recordings,
                $recordings['has_recordings']
                    ? "Recordings retrieved successfully"
                    : "No recordings available yet"
            );
        }, 'GroupSessionController@getRecordings');
    }

    public function deleteRecordings(string $id)
    {
        return $this->executeService(function () use ($id) {
            $session = $this->service->getSessionById($id);
            $this->authorize('delete', Group::find($session->group_id));

            $this->service->deleteSessionRecordings($id);

            return $this->okResponse(
                null,
                "Recordings deleted successfully"
            );
        }, 'GroupSessionController@deleteRecordings');
    }
}
