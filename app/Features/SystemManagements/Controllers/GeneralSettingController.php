<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\SystemManagements\Models\GeneralSetting;
use App\Features\SystemManagements\Requests\GeneralSettingRequest;
use App\Features\SystemManagements\Services\GeneralSettingService;
use App\Features\SystemManagements\Transformers\GeneralSettingCollection;
use App\Features\SystemManagements\Transformers\GeneralSettingResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GeneralSettingController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    /**
     * Inject your service in constructor
     */
    public function __construct(
        protected GeneralSettingService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $result = $this->service->getAllSettings(
                search: $request->get('search'),
                paginate: $request->boolean('page')
            );

            return $this->okResponse(
                $request->boolean('page')
                    ? new GeneralSettingCollection($result)
                    : GeneralSettingResource::collection($result),
                "Settings retrieved successfully"
            );
        }, 'GeneralSettingController@index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GeneralSettingRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $setting = $this->service->createSetting($request->validated());

            return $this->okResponse(
                GeneralSettingResource::make($setting),
                "Setting created successfully"
            );
        }, 'GeneralSettingController@store');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $setting = $this->service->getSettingById($id);

            if (!$setting) {
                return $this->notFoundResponse('Setting not found');
            }

            return $this->okResponse(
                GeneralSettingResource::make($setting),
                "Setting retrieved successfully"
            );
        }, 'GeneralSettingController@show');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GeneralSettingRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $setting = $this->service->updateSettingById($id, $request->validated());

            return $this->okResponse(
                GeneralSettingResource::make($setting),
                "Setting updated successfully"
            );
        }, 'GeneralSettingController@update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $setting = $this->service->deleteSettingById($id);

            return $this->okResponse(
                GeneralSettingResource::make($setting),
                "Setting deleted successfully"
            );
        }, 'GeneralSettingController@destroy');
    }

    /**
     * Get setting by key
     */
    public function getByKey(string $key)
    {
        return $this->executeService(function () use ($key) {
            $setting = $this->service->getSettingByKey($key);

            if (!$setting) {
                return $this->notFoundResponse('Setting not found');
            }

            return $this->okResponse(
                GeneralSettingResource::make($setting),
                "Setting retrieved successfully"
            );
        }, 'GeneralSettingController@getByKey');
    }

    /**
     * Update or create setting by key
     */
    public function updateByKey(Request $request, string $key)
    {
        return $this->executeService(function () use ($request, $key) {
            $request->validate([
                'value' => 'required|string'
            ]);

            $setting = $this->service->updateOrCreateSetting($key, $request->value);

            return $this->okResponse(
                GeneralSettingResource::make($setting),
                "Setting updated successfully"
            );
        }, 'GeneralSettingController@updateByKey');
    }
}
