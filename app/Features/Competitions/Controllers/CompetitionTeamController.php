<?php

namespace App\Features\Competitions\Controllers;

use App\Features\Competitions\Requests\CompetitionTeamRequest;
use App\Features\Competitions\Services\CompetitionTeamService;
use App\Features\Competitions\Transformers\CompetitionTeamResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompetitionTeamController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected CompetitionTeamService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request, string $competitionId)
    {
        return $this->executeService(function () use ($request, $competitionId) {
            $paginate = $request->boolean('paginate', true);
            $perPage = $request->integer('per_page', 15);

            $teams = $this->service->getTeamsByCompetition($competitionId, $paginate, $perPage);

            if ($paginate) {
                return $this->paginatedResponse($teams, CompetitionTeamResource::class, "Teams retrieved successfully");
            }

            return $this->okResponse(
                CompetitionTeamResource::collection($teams),
                "Teams retrieved successfully"
            );
        }, 'CompetitionTeamController@index');
    }

    public function store(CompetitionTeamRequest $request, string $competitionId)
    {
        return $this->executeService(function () use ($request, $competitionId) {
            $data = $request->validated();
            $memberIds = $data['member_ids'] ?? [];
            unset($data['member_ids']);

            $team = $this->service->createTeam($competitionId, $data, $memberIds);

            return $this->createdResponse(
                CompetitionTeamResource::make($team),
                "Team created successfully"
            );
        }, 'CompetitionTeamController@store');
    }

    public function show(string $competitionId, string $teamId)
    {
        return $this->executeService(function () use ($teamId) {
            $team = $this->service->getTeamById($teamId);

            return $this->okResponse(
                CompetitionTeamResource::make($team),
                "Team retrieved successfully"
            );
        }, 'CompetitionTeamController@show');
    }

    public function update(CompetitionTeamRequest $request, string $competitionId, string $teamId)
    {
        return $this->executeService(function () use ($request, $teamId) {
            $team = $this->service->updateTeam($teamId, $request->validated());

            return $this->okResponse(
                CompetitionTeamResource::make($team),
                "Team updated successfully"
            );
        }, 'CompetitionTeamController@update');
    }

    public function destroy(string $competitionId, string $teamId)
    {
        return $this->executeService(function () use ($teamId) {
            $this->service->deleteTeam($teamId);

            return $this->okResponse(null, "Team deleted successfully");
        }, 'CompetitionTeamController@destroy');
    }

    public function autoForm(Request $request, string $competitionId)
    {
        return $this->executeService(function () use ($request, $competitionId) {
            $request->validate([
                'governorate' => ['required', 'string', 'max:100'],
            ]);

            $teams = $this->service->autoFormTeams($competitionId, $request->governorate);

            return $this->okResponse(
                CompetitionTeamResource::collection($teams),
                count($teams) . " teams formed successfully"
            );
        }, 'CompetitionTeamController@autoForm');
    }
}
