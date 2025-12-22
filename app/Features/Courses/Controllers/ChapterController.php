<?php

namespace App\Features\Courses\Controllers;

use App\Features\Courses\Models\Chapter;
use App\Features\Courses\Requests\ChapterRequest;
use App\Features\Courses\Services\ChapterService;
use App\Features\Courses\Transformers\ChapterCollection;
use App\Features\Courses\Transformers\ChapterResource;
use App\Features\Courses\Metadata\ChapterMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class ChapterController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected ChapterService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Chapter::class);

            $result = $this->service->getChapters(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new ChapterCollection($result)
                    : ChapterResource::collection($result),
                "Success"
            );
        }, 'ChapterController@index');
    }

    public function store(ChapterRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', Chapter::class);

            $chapter = $this->service->storeChapter($request->validated());

            return $this->okResponse(
                ChapterResource::make($chapter),
                "Chapter created successfully"
            );
        }, 'ChapterController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $chapter = $this->service->getChapterById($id);
            $this->authorize('view', $chapter);

            return $this->okResponse(
                ChapterResource::make($chapter),
                "Chapter retrieved successfully"
            );
        }, 'ChapterController@show');
    }

    public function update(ChapterRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $chapter = $this->service->getChapterById($id);
            $this->authorize('update', $chapter);

            $chapter = $this->service->updateChapter($id, $request->validated());

            return $this->okResponse(
                ChapterResource::make($chapter),
                "Chapter updated successfully"
            );
        }, 'ChapterController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $chapter = $this->service->getChapterById($id);
            $this->authorize('delete', $chapter);

            $this->service->deleteChapter($id);

            return $this->okResponse(
                null,
                "Chapter deleted successfully"
            );
        }, 'ChapterController@destroy');
    }

    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Chapter::class);

            return $this->okResponse(
                ChapterMetadata::get(),
                "Chapter metadata retrieved successfully"
            );
        }, 'ChapterController@metadata');
    }

    public function getByCourse(string $courseId)
    {
        return $this->executeService(function () use ($courseId) {
            $this->authorize('viewAny', Chapter::class);

            $chapters = $this->service->getChaptersByCourseId($courseId);

            return $this->okResponse(
                ChapterResource::collection($chapters),
                "Chapters retrieved successfully"
            );
        }, 'ChapterController@getByCourse');
    }
}
