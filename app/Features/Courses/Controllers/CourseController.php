<?php

namespace App\Features\Courses\Controllers;

use App\Features\Courses\Models\Course;
use App\Features\Courses\Requests\CourseRequest;
use App\Features\Courses\Services\CourseService;
use App\Features\Courses\Transformers\CourseCollection;
use App\Features\Courses\Transformers\CourseResource;
use App\Features\Courses\Transformers\QuizResource;
use App\Features\Courses\Transformers\AssignmentResource;
use App\Features\Courses\Transformers\MaterialResource;
use App\Features\Courses\Metadata\CourseMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class CourseController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected CourseService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Course::class);

            $result = $this->service->getCourses(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new CourseCollection($result)
                    : CourseResource::collection($result),
                "Success"
            );
        }, 'CourseController@index');
    }

    public function store(CourseRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', Course::class);

            $course = $this->service->storeCourse(
                $request->validated(),
                $request->file('image')
            );

            return $this->okResponse(
                CourseResource::make($course),
                "Course created successfully"
            );
        }, 'CourseController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $course = $this->service->getCourseById($id);
            $this->authorize('view', $course);

            return $this->okResponse(
                CourseResource::make($course),
                "Course retrieved successfully"
            );
        }, 'CourseController@show');
    }

    public function update(CourseRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $course = $this->service->getCourseById($id);
            $this->authorize('update', $course);

            $course = $this->service->updateCourse(
                $id,
                $request->validated(),
                $request->file('image')
            );

            return $this->okResponse(
                CourseResource::make($course),
                "Course updated successfully"
            );
        }, 'CourseController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $course = $this->service->getCourseById($id);
            $this->authorize('delete', $course);

            $this->service->deleteCourse($id);

            return $this->okResponse(
                null,
                "Course deleted successfully"
            );
        }, 'CourseController@destroy');
    }

    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Course::class);

            return $this->okResponse(
                CourseMetadata::get(),
                "Course metadata retrieved successfully"
            );
        }, 'CourseController@metadata');
    }

    public function quizzes(string $courseId)
    {
        return $this->executeService(function () use ($courseId) {
            $course = $this->service->getCourseById($courseId);
            $this->authorize('view', $course);

            $quizzes = $this->service->getQuizzesByCourseId($courseId);

            return $this->okResponse(
                QuizResource::collection($quizzes),
                "Quizzes retrieved successfully"
            );
        }, 'CourseController@quizzes');
    }

    public function assignments(string $courseId)
    {
        return $this->executeService(function () use ($courseId) {
            $course = $this->service->getCourseById($courseId);
            $this->authorize('view', $course);

            $assignments = $this->service->getAssignmentsByCourseId($courseId);

            return $this->okResponse(
                AssignmentResource::collection($assignments),
                "Assignments retrieved successfully"
            );
        }, 'CourseController@assignments');
    }

    public function materials(string $courseId)
    {
        return $this->executeService(function () use ($courseId) {
            $course = $this->service->getCourseById($courseId);
            $this->authorize('view', $course);

            $materials = $this->service->getMaterialsByCourseId($courseId);

            return $this->okResponse(
                MaterialResource::collection($materials),
                "Materials retrieved successfully"
            );
        }, 'CourseController@materials');
    }

}
