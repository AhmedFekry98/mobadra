<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\SystemManagements\Models\FAQ;
use App\Features\SystemManagements\Requests\FAQRequest;
use App\Features\SystemManagements\Services\FAQService;
use App\Features\SystemManagements\Transformers\FAQCollection;
use App\Features\SystemManagements\Transformers\FAQResource;
use App\Features\SystemManagements\Metadata\FAQMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

/**
 * Class FAQController
 * @package App\Features\SystemManagements\Controllers
 */
class FAQController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    /**
     * Inject your service in constructor
     */
    public function __construct(
        protected FAQService $service
    )
    {
          $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->executeService(function () {

            $this->authorize('viewAny', \App\Features\SystemManagements\Models\FAQ::class);

            $result = $this->service->getFAQs(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new FAQCollection($result)
                    : FAQResource::collection($result),
                "Success"
            );
        }, 'BadgeController@index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FAQRequest $request)
    {


        return $this->executeService(function () use ($request) {
            $this->authorize('create', \App\Features\SystemManagements\Models\FAQ::class);
            $faq = $this->service->storeFAQ(
                $request->validated(),
            );

            return $this->okResponse(
                FAQResource::make($faq),
                "FAQ created successfully"
            );
        }, 'FAQController@store');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $faq = $this->service->getFAQById($id);
            $this->authorize('view', $faq);

            return $this->okResponse(
                FAQResource::make($faq),
                "FAQ retrieved successfully"
            );
        }, 'FAQController@show');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FAQRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $faq = $this->service->getFAQById($id);
            $this->authorize('update', $faq);

            $faq = $this->service->updateFAQById(
                $id,
                $request->validated(),
            );

            return $this->okResponse(
                FAQResource::make($faq),
                "FAQ updated successfully"
            );
        }, 'FAQController@update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $faq = $this->service->getFAQById($id);
            $this->authorize('delete', $faq);

            $this->service->deleteFAQById($id);

            return $this->okResponse(
                null,
                "FAQ deleted successfully"
            );
        }, 'FAQController@destroy');
    }

    /**
     * Toggle the status of the specified resource from storage.
     */
    public function toggleStatus(string $id)
    {
        return $this->executeService(function () use ($id) {
            $faq = $this->service->getFAQById($id);
            $this->authorize('update', $faq);

            $faq = $this->service->toggleStatus($id);

            return $this->okResponse(
                FAQResource::make($faq),
                "FAQ status toggled successfully"
            );
        }, 'FAQController@toggleStatus');
    }

    /**
     * Get metadata for badges (filters, searches, etc.)
     */
    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', \App\Features\SystemManagements\Models\FAQ::class);

            return $this->okResponse(
                FAQMetadata::get(),
                "FAQ metadata retrieved successfully"
            );
        }, 'FAQController@metadata');
    }


}
