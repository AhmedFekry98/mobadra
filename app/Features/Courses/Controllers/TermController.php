<?php

namespace App\Features\Courses\Controllers;

use App\Features\Courses\Models\Term;
use App\Features\Courses\Requests\TermRequest;
use App\Features\Courses\Services\TermService;
use App\Features\Courses\Transformers\TermCollection;
use App\Features\Courses\Transformers\TermResource;
use App\Features\Courses\Metadata\TermMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class TermController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected TermService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Term::class);

            $result = $this->service->getTerms(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new TermCollection($result)
                    : TermResource::collection($result),
                "Success"
            );
        }, 'TermController@index');
    }

    public function store(TermRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', Term::class);

            $term = $this->service->storeTerm($request->validated());

            return $this->okResponse(
                TermResource::make($term),
                "Term created successfully"
            );
        }, 'TermController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $term = $this->service->getTermById($id);
            $this->authorize('view', $term);

            return $this->okResponse(
                TermResource::make($term),
                "Term retrieved successfully"
            );
        }, 'TermController@show');
    }

    public function update(TermRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $term = $this->service->getTermById($id);
            $this->authorize('update', $term);

            $term = $this->service->updateTerm($id, $request->validated());

            return $this->okResponse(
                TermResource::make($term),
                "Term updated successfully"
            );
        }, 'TermController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $term = $this->service->getTermById($id);
            $this->authorize('delete', $term);

            $this->service->deleteTerm($id);

            return $this->okResponse(
                null,
                "Term deleted successfully"
            );
        }, 'TermController@destroy');
    }

    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Term::class);

            return $this->okResponse(
                TermMetadata::get(),
                "Term metadata retrieved successfully"
            );
        }, 'TermController@metadata');
    }
}
