<?php

namespace App\Features\Badges\Controllers;

use App\Enums\Operator;
use App\Features\Badges\Metadata\BadgeConditionMetadata;
use App\Features\Badges\Models\BadgeCondition;
use App\Features\Badges\Requests\BadgeConditionRequest;
use App\Features\Badges\Services\BadgeConditionService;
use App\Features\Badges\Transformers\BadgeConditionCollection;
use App\Features\Badges\Transformers\BadgeConditionResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

/**
 * Class BadgeConditionController
 * @package App\Features\Badges\Controllers
 */
class BadgeConditionController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    /**
     * Inject your service in constructor
     */
    public function __construct(
        protected BadgeConditionService $service
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
            $this->authorize('viewAny', BadgeCondition::class);

            $result = $this->service->getBadgeConditions(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new BadgeConditionCollection($result)
                    : BadgeConditionResource::collection($result),
                "Success"
            );
        }, 'BadgeConditionController@index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BadgeConditionRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', BadgeCondition::class);

            $badgeCondition = $this->service->storeBadgeCondition(
                $request->validated()
            );

            return $this->okResponse(
                BadgeConditionResource::make($badgeCondition),
                "BadgeCondition created successfully"
            );
        }, 'BadgeConditionController@store');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $badgeCondition = $this->service->getBadgeConditionById($id);
            $this->authorize('view', $badgeCondition);

            return $this->okResponse(
                BadgeConditionResource::make($badgeCondition),
                "BadgeCondition retrieved successfully"
            );
        }, 'BadgeConditionController@show');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BadgeConditionRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $badgeCondition = $this->service->getBadgeConditionById($id);
            $this->authorize('update', $badgeCondition);

            $badgeCondition = $this->service->updateBadgeConditionById(
                $id,
                $request->validated()
            );

            return $this->okResponse(
                BadgeConditionResource::make($badgeCondition),
                "BadgeCondition updated successfully"
            );
        }, 'BadgeConditionController@update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $badgeCondition = $this->service->getBadgeConditionById($id);
            $this->authorize('delete', $badgeCondition);

            $this->service->deleteBadgeConditionById($id);

            return $this->okResponse(
                null,
                "BadgeCondition deleted successfully"
            );
        }, 'BadgeConditionController@destroy');
    }

    /**
     * Get badge conditions by badge ID
     */
    public function getBadgeConditionsByBadgeId(string $badgeId)
    {
        return $this->executeService(function () use ($badgeId) {
            $this->authorize('viewAny', BadgeCondition::class);

            $result = $this->service->getBadgeConditionsByBadgeId(
                $badgeId,
                request('search'),
                request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new BadgeConditionCollection($result)
                    : BadgeConditionResource::collection($result),
                "Badge conditions retrieved successfully"
            );
        }, 'BadgeConditionController@getBadgeConditionsByBadgeId');
    }

    /**
     * Get available operators
     */
    public function getOperator()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', BadgeCondition::class);

            return $this->okResponse(
                Operator::cases(),
                "Operators retrieved successfully"
            );
        }, 'BadgeConditionController@getOperator');
    }

    /**
     * Get metadata for conditions
     */
    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', BadgeCondition::class);

            return $this->okResponse(
                BadgeConditionMetadata::get(),
                "Metadata retrieved successfully"
            );
        }, 'BadgeConditionController@metadata');
    }
}
