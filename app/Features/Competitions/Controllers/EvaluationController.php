<?php

namespace App\Features\Competitions\Controllers;

use App\Features\Competitions\Requests\Phase2EvaluationRequest;
use App\Features\Competitions\Requests\TeamEvaluationRequest;
use App\Features\Competitions\Services\EvaluationService;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class EvaluationController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected EvaluationService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function evaluatePhase2(Phase2EvaluationRequest $request, string $competitionId, string $submissionId)
    {
        return $this->executeService(function () use ($request, $submissionId) {
            $submission = $this->service->submitPhase2Evaluation(
                $submissionId,
                $request->validated(),
                auth()->id()
            );

            return $this->okResponse($submission, "Phase 2 evaluation submitted successfully");
        }, 'EvaluationController@evaluatePhase2');
    }

    public function evaluateTeam(TeamEvaluationRequest $request, string $competitionId, string $teamId)
    {
        return $this->executeService(function () use ($request, $teamId) {
            $evaluation = $this->service->submitTeamEvaluation(
                $teamId,
                $request->validated(),
                auth()->id()
            );

            return $this->okResponse($evaluation, "Team evaluation submitted successfully");
        }, 'EvaluationController@evaluateTeam');
    }

    public function getTeamEvaluations(string $competitionId, string $teamId)
    {
        return $this->executeService(function () use ($teamId) {
            $evaluations = $this->service->getTeamEvaluations($teamId);

            return $this->okResponse($evaluations, "Team evaluations retrieved successfully");
        }, 'EvaluationController@getTeamEvaluations');
    }
}
