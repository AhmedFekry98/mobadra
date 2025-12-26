<?php

namespace App\Features\Competitions\Controllers;

use App\Features\Competitions\Models\CompetitionPhase;
use App\Features\Competitions\Requests\CompetitionPhaseRequest;
use App\Features\Competitions\Transformers\CompetitionPhaseResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompetitionPhaseController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(string $competitionId)
    {
        return $this->executeService(function () use ($competitionId) {
            $phases = CompetitionPhase::where('competition_id', $competitionId)
                ->orderBy('phase_number')
                ->get();

            return $this->okResponse(
                CompetitionPhaseResource::collection($phases),
                "Phases retrieved successfully"
            );
        }, 'CompetitionPhaseController@index');
    }

    public function store(CompetitionPhaseRequest $request, string $competitionId)
    {
        return $this->executeService(function () use ($request, $competitionId) {
            $data = $request->validated();
            $data['competition_id'] = $competitionId;

            $phase = CompetitionPhase::create($data);

            return $this->createdResponse(
                CompetitionPhaseResource::make($phase),
                "Phase created successfully"
            );
        }, 'CompetitionPhaseController@store');
    }

    public function update(CompetitionPhaseRequest $request, string $competitionId, string $phaseId)
    {
        return $this->executeService(function () use ($request, $phaseId) {
            $phase = CompetitionPhase::findOrFail($phaseId);
            $phase->update($request->validated());

            return $this->okResponse(
                CompetitionPhaseResource::make($phase->fresh()),
                "Phase updated successfully"
            );
        }, 'CompetitionPhaseController@update');
    }

    public function destroy(string $competitionId, string $phaseId)
    {
        return $this->executeService(function () use ($phaseId) {
            CompetitionPhase::destroy($phaseId);

            return $this->okResponse(null, "Phase deleted successfully");
        }, 'CompetitionPhaseController@destroy');
    }
}
