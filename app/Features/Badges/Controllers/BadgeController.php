<?php

namespace App\Features\Badges\Controllers;

use App\Features\Badges\Models\Badge;
use App\Features\Badges\Requests\BadgeRequest;
use App\Features\Badges\Services\BadgeService;
use App\Features\Badges\Transformers\BadgeCollection;
use App\Features\Badges\Transformers\BadgeResource;
use App\Features\Badges\Metadata\BadgeMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

/**
 * Class BadgeController
 * @package App\Features\Badges\Controllers
 */
class BadgeController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    /**
     * Inject your service in constructor
     */
    public function __construct(
        protected BadgeService $service
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

            $this->authorize('viewAny', \App\Features\Badges\Models\Badge::class);

            $result = $this->service->getBadges(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new BadgeCollection($result)
                    : BadgeResource::collection($result),
                "Success"
            );
        }, 'BadgeController@index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BadgeRequest $request)
    {


        return $this->executeService(function () use ($request) {
            $this->authorize('create', \App\Features\Badges\Models\Badge::class);
            $badge = $this->service->storeBadge(
                $request->validated(),
                $request->file('image')
            );

            return $this->okResponse(
                BadgeResource::make($badge),
                "Badge created successfully"
            );
        }, 'BadgeController@store');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $badge = $this->service->getBadgeById($id);
            $this->authorize('view', $badge);

            return $this->okResponse(
                BadgeResource::make($badge),
                "Badge retrieved successfully"
            );
        }, 'BadgeController@show');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BadgeRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $badge = $this->service->getBadgeById($id);
            $this->authorize('update', $badge);

            $badge = $this->service->updateBadge(
                $id,
                $request->validated(),
                $request->file('image')
            );

            return $this->okResponse(
                BadgeResource::make($badge),
                "Badge updated successfully"
            );
        }, 'BadgeController@update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $badge = $this->service->getBadgeById($id);
            $this->authorize('delete', $badge);

            $this->service->deleteBadge($id);

            return $this->okResponse(
                null,
                "Badge deleted successfully"
            );
        }, 'BadgeController@destroy');
    }

    /**
     * Get metadata for badges (filters, searches, etc.)
     */
    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Badge::class);

            return $this->okResponse(
                BadgeMetadata::get(),
                "Badge metadata retrieved successfully"
            );
        }, 'BadgeController@metadata');
    }


}
