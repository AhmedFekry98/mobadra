<?php

namespace App\Features\Competitions\Controllers;

use App\Features\Competitions\Models\Competition;
use App\Features\Competitions\Requests\CompetitionRequest;
use App\Features\Competitions\Services\CompetitionService;
use App\Features\Competitions\Transformers\CompetitionResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompetitionController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected CompetitionService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $paginate = $request->boolean('paginate', true);
            $perPage = $request->integer('per_page', 15);

            $competitions = $this->service->getAllCompetitions($paginate, $perPage);

            return $this->okResponse(
                CompetitionResource::collection($competitions),
                "Competitions retrieved successfully"
            );
        }, 'CompetitionController@index');
    }

    public function store(CompetitionRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $competition = $this->service->createCompetition($request->validated());

            return $this->createdResponse(
                CompetitionResource::make($competition),
                "Competition created successfully"
            );
        }, 'CompetitionController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $competition = $this->service->getCompetitionById($id);

            return $this->okResponse(
                CompetitionResource::make($competition->load(['levels'])),
                "Competition retrieved successfully"
            );
        }, 'CompetitionController@show');
    }

    public function update(CompetitionRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $competition = $this->service->updateCompetition($id, $request->validated());

            return $this->okResponse(
                CompetitionResource::make($competition),
                "Competition updated successfully"
            );
        }, 'CompetitionController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $this->service->deleteCompetition($id);

            return $this->okResponse(null, "Competition deleted successfully");
        }, 'CompetitionController@destroy');
    }
}
