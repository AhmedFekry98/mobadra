<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\SystemManagements\Models\Governorate;
use App\Features\SystemManagements\Requests\GovernorateRequest;
use App\Features\SystemManagements\Transformers\GovernorateCollection;
use App\Features\SystemManagements\Transformers\GovernorateResource;
use App\Features\SystemManagements\Transformers\TrainingCenterResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GovernorateController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $query = Governorate::query();

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('page')) {
                $result = $query->orderBy('name')->paginate($request->get('per_page', 15));
                return $this->okResponse(
                    GovernorateCollection::make($result),
                    "Governorates retrieved successfully"
                );
            }

            return $this->okResponse(
                GovernorateResource::collection($query->orderBy('name')->get()),
                "Governorates retrieved successfully"
            );
        }, 'GovernorateController@index');
    }

    public function store(GovernorateRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $governorate = Governorate::create($request->validated());

            return $this->okResponse(
                GovernorateResource::make($governorate),
                "Governorate created successfully"
            );
        }, 'GovernorateController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $governorate = Governorate::with('trainingCenters')->findOrFail($id);

            return $this->okResponse(
                GovernorateResource::make($governorate),
                "Governorate retrieved successfully"
            );
        }, 'GovernorateController@show');
    }

    public function update(GovernorateRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $governorate = Governorate::findOrFail($id);
            $governorate->update($request->validated());

            return $this->okResponse(
                GovernorateResource::make($governorate),
                "Governorate updated successfully"
            );
        }, 'GovernorateController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $governorate = Governorate::findOrFail($id);
            $governorate->delete();

            return $this->okResponse(
                null,
                "Governorate deleted successfully"
            );
        }, 'GovernorateController@destroy');
    }

    public function trainingCenters(string $id)
    {
        return $this->executeService(function () use ($id) {
            $governorate = Governorate::findOrFail($id);
            $trainingCenters = $governorate->trainingCenters()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return $this->okResponse(
                TrainingCenterResource::collection($trainingCenters),
                "Training centers retrieved successfully"
            );
        }, 'GovernorateController@trainingCenters');
    }
}
