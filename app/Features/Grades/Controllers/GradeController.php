<?php

namespace App\Features\Grades\Controllers;

use App\Features\Grades\Models\Grade;
use App\Features\Grades\Requests\GradeRequest;
use App\Features\Grades\Services\GradeService;
use App\Features\Grades\Transformers\GradeCollection;
use App\Features\Grades\Transformers\GradeResource;
use App\Features\Grades\Metadata\GradeMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class GradeController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected GradeService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Grade::class);

            $result = $this->service->getGrades(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new GradeCollection($result)
                    : GradeResource::collection($result),
                "Success"
            );
        }, 'GradeController@index');
    }

    public function store(GradeRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', Grade::class);

            $grade = $this->service->storeGrade($request->validated());

            return $this->okResponse(
                GradeResource::make($grade),
                "Grade created successfully"
            );
        }, 'GradeController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $grade = $this->service->getGradeById($id);
            $this->authorize('view', $grade);

            return $this->okResponse(
                GradeResource::make($grade),
                "Grade retrieved successfully"
            );
        }, 'GradeController@show');
    }

    public function update(GradeRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $grade = $this->service->getGradeById($id);
            $this->authorize('update', $grade);

            $grade = $this->service->updateGrade($id, $request->validated());

            return $this->okResponse(
                GradeResource::make($grade),
                "Grade updated successfully"
            );
        }, 'GradeController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $grade = $this->service->getGradeById($id);
            $this->authorize('delete', $grade);

            $this->service->deleteGrade($id);

            return $this->okResponse(
                null,
                "Grade deleted successfully"
            );
        }, 'GradeController@destroy');
    }

    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Grade::class);

            return $this->okResponse(
                GradeMetadata::get(),
                "Grade metadata retrieved successfully"
            );
        }, 'GradeController@metadata');
    }

    public function active()
    {
        return $this->executeService(function () {
            $grades = $this->service->getActiveGrades();

            return $this->okResponse(
                GradeResource::collection($grades),
                "Active grades retrieved successfully"
            );
        }, 'GradeController@active');
    }
}
