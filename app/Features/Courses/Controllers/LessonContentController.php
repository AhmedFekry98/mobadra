<?php

namespace App\Features\Courses\Controllers;

use App\Features\Courses\Models\LessonContent;
use App\Features\Courses\Requests\LessonContentRequest;
use App\Features\Courses\Services\LessonContentService;
use App\Features\Courses\Transformers\LessonContentCollection;
use App\Features\Courses\Transformers\LessonContentResource;
use App\Features\Courses\Metadata\LessonContentMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class LessonContentController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected LessonContentService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', LessonContent::class);

            $result = $this->service->getLessonContents(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new LessonContentCollection($result)
                    : LessonContentResource::collection($result),
                "Success"
            );
        }, 'LessonContentController@index');
    }

    public function store(LessonContentRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', LessonContent::class);

            $lessonContent = $this->service->storeLessonContent($request->validated());

            return $this->okResponse(
                LessonContentResource::make($lessonContent),
                "Lesson content created successfully"
            );
        }, 'LessonContentController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $lessonContent = $this->service->getLessonContentById($id);
            $this->authorize('view', $lessonContent);

            return $this->okResponse(
                LessonContentResource::make($lessonContent),
                "Lesson content retrieved successfully"
            );
        }, 'LessonContentController@show');
    }

    public function update(LessonContentRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $lessonContent = $this->service->getLessonContentById($id);
            $this->authorize('update', $lessonContent);

            $lessonContent = $this->service->updateLessonContent($id, $request->validated());

            return $this->okResponse(
                LessonContentResource::make($lessonContent),
                "Lesson content updated successfully"
            );
        }, 'LessonContentController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $lessonContent = $this->service->getLessonContentById($id);
            $this->authorize('delete', $lessonContent);

            $this->service->deleteLessonContent($id);

            return $this->okResponse(
                null,
                "Lesson content deleted successfully"
            );
        }, 'LessonContentController@destroy');
    }

    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', LessonContent::class);

            return $this->okResponse(
                LessonContentMetadata::get(),
                "Lesson content metadata retrieved successfully"
            );
        }, 'LessonContentController@metadata');
    }

    public function getByLesson(string $lessonId)
    {
        return $this->executeService(function () use ($lessonId) {
            $this->authorize('viewAny', LessonContent::class);

            $lessonContents = $this->service->getLessonContentsByLessonId($lessonId);

            return $this->okResponse(
                LessonContentResource::collection($lessonContents),
                "Lesson contents retrieved successfully"
            );
        }, 'LessonContentController@getByLesson');
    }
}
