<?php

namespace App\Features\Courses\Controllers;

use App\Features\Courses\Models\Lesson;
use App\Features\Courses\Requests\LessonRequest;
use App\Features\Courses\Services\LessonService;
use App\Features\Courses\Transformers\LessonCollection;
use App\Features\Courses\Transformers\LessonResource;
use App\Features\Courses\Metadata\LessonMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class LessonController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected LessonService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Lesson::class);

            $result = $this->service->getLessons(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new LessonCollection($result)
                    : LessonResource::collection($result),
                "Success"
            );
        }, 'LessonController@index');
    }

    public function store(LessonRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', Lesson::class);

            $lesson = $this->service->storeLesson($request->validated());

            return $this->okResponse(
                LessonResource::make($lesson),
                "Lesson created successfully"
            );
        }, 'LessonController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $lesson = $this->service->getLessonById($id);
            $this->authorize('view', $lesson);

            return $this->okResponse(
                LessonResource::make($lesson),
                "Lesson retrieved successfully"
            );
        }, 'LessonController@show');
    }

    public function update(LessonRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $lesson = $this->service->getLessonById($id);
            $this->authorize('update', $lesson);

            $lesson = $this->service->updateLesson($id, $request->validated());

            return $this->okResponse(
                LessonResource::make($lesson),
                "Lesson updated successfully"
            );
        }, 'LessonController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $lesson = $this->service->getLessonById($id);
            $this->authorize('delete', $lesson);

            $this->service->deleteLesson($id);

            return $this->okResponse(
                null,
                "Lesson deleted successfully"
            );
        }, 'LessonController@destroy');
    }

    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Lesson::class);

            return $this->okResponse(
                LessonMetadata::get(),
                "Lesson metadata retrieved successfully"
            );
        }, 'LessonController@metadata');
    }

    public function getByChapter(string $chapterId)
    {
        return $this->executeService(function () use ($chapterId) {
            $this->authorize('viewAny', Lesson::class);

            $lessons = $this->service->getLessonsByChapterId($chapterId);

            return $this->okResponse(
                LessonResource::collection($lessons),
                "Lessons retrieved successfully"
            );
        }, 'LessonController@getByChapter');
    }
}
