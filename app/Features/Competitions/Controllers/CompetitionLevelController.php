<?php

namespace App\Features\Competitions\Controllers;

use App\Features\Competitions\Requests\CompetitionLevelRequest;
use App\Features\Competitions\Services\CompetitionLevelService;
use App\Features\Competitions\Transformers\CompetitionLevelResource;
use App\Features\Courses\Transformers\CourseResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CompetitionLevelController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected CompetitionLevelService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request, int $competitionId)
    {
        return $this->executeService(function () use ($request, $competitionId) {
            $paginate = $request->boolean('paginate', true);
            $perPage = $request->integer('per_page', 15);

            $levels = $this->service->getAllLevels($competitionId, $paginate, $perPage);

            return $this->okResponse(
                CompetitionLevelResource::collection($levels),
                "Competition levels retrieved successfully"
            );
        }, 'CompetitionLevelController@index');
    }

    public function store(CompetitionLevelRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $level = $this->service->createLevel($request->validated());

            return $this->createdResponse(
                CompetitionLevelResource::make($level),
                "Competition level created successfully"
            );
        }, 'CompetitionLevelController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $level = $this->service->getLevelById($id);

            return $this->okResponse(
                CompetitionLevelResource::make($level),
                "Competition level retrieved successfully"
            );
        }, 'CompetitionLevelController@show');
    }

    public function update(CompetitionLevelRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $level = $this->service->updateLevel($id, $request->validated());

            return $this->okResponse(
                CompetitionLevelResource::make($level),
                "Competition level updated successfully"
            );
        }, 'CompetitionLevelController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $this->service->deleteLevel($id);

            return $this->okResponse(null, "Competition level deleted successfully");
        }, 'CompetitionLevelController@destroy');
    }

    public function course(string $id)
    {
        return $this->executeService(function () use ($id) {
            $course = $this->service->getLevelCourse($id);

            if (!$course) {
                return $this->notFoundResponse("No course found for this level");
            }

            return $this->okResponse(
                CourseResource::make($course->load(['lessons', 'term', 'grade'])),
                "Level course retrieved successfully"
            );
        }, 'CompetitionLevelController@course');
    }
}
