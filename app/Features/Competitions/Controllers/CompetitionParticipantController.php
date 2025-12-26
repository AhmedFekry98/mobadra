<?php

namespace App\Features\Competitions\Controllers;

use App\Features\Competitions\Services\CompetitionParticipantService;
use App\Features\Competitions\Transformers\CompetitionParticipantResource;
use App\Features\Competitions\Transformers\LeaderboardResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompetitionParticipantController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected CompetitionParticipantService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request, string $competitionId)
    {
        return $this->executeService(function () use ($request, $competitionId) {
            $paginate = $request->boolean('paginate', true);
            $perPage = $request->integer('per_page', 15);

            $participants = $this->service->getParticipantsByCompetition($competitionId, $paginate, $perPage);

            if ($paginate) {
                return $this->paginatedResponse($participants, CompetitionParticipantResource::class, "Participants retrieved successfully");
            }

            return $this->okResponse(
                CompetitionParticipantResource::collection($participants),
                "Participants retrieved successfully"
            );
        }, 'CompetitionParticipantController@index');
    }

    public function show(string $competitionId, string $participantId)
    {
        return $this->executeService(function () use ($participantId) {
            $participant = $this->service->getParticipantById($participantId);

            return $this->okResponse(
                CompetitionParticipantResource::make($participant),
                "Participant retrieved successfully"
            );
        }, 'CompetitionParticipantController@show');
    }

    public function updateStatus(Request $request, string $competitionId, string $participantId)
    {
        return $this->executeService(function () use ($request, $participantId) {
            $request->validate([
                'status' => ['required', 'in:registered,qualified,eliminated,pending'],
            ]);

            $participant = $this->service->updateParticipantStatus($participantId, $request->status);

            return $this->okResponse(
                CompetitionParticipantResource::make($participant),
                "Participant status updated successfully"
            );
        }, 'CompetitionParticipantController@updateStatus');
    }

    public function leaderboard(Request $request, string $competitionId)
    {
        return $this->executeService(function () use ($request, $competitionId) {
            $governorate = $request->get('governorate');
            $limit = $request->integer('limit', 100);

            $leaderboard = $this->service->getLeaderboard($competitionId, $governorate, $limit);

            return $this->okResponse(
                LeaderboardResource::collection($leaderboard),
                "Leaderboard retrieved successfully"
            );
        }, 'CompetitionParticipantController@leaderboard');
    }
}
