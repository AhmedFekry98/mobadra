<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\SystemManagements\Models\TrainingCenter;
use App\Features\SystemManagements\Requests\TrainingCenterRequest;
use App\Features\SystemManagements\Transformers\TrainingCenterCollection;
use App\Features\SystemManagements\Transformers\TrainingCenterResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TrainingCenterController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $query = TrainingCenter::with('governorate');

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            }

            if ($request->has('governorate_id')) {
                $query->where('governorate_id', $request->get('governorate_id'));
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('page')) {
                $result = $query->orderBy('name')->paginate($request->get('per_page', 15));
                return $this->okResponse(
                    TrainingCenterCollection::make($result),
                    "Training centers retrieved successfully"
                );
            }

            return $this->okResponse(
                TrainingCenterResource::collection($query->orderBy('name')->get()),
                "Training centers retrieved successfully"
            );
        }, 'TrainingCenterController@index');
    }

    public function store(TrainingCenterRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $center = TrainingCenter::create($request->validated());
            $center->load('governorate');

            return $this->okResponse(
                TrainingCenterResource::make($center),
                "Training center created successfully"
            );
        }, 'TrainingCenterController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $center = TrainingCenter::with('governorate')->findOrFail($id);

            return $this->okResponse(
                TrainingCenterResource::make($center),
                "Training center retrieved successfully"
            );
        }, 'TrainingCenterController@show');
    }

    public function update(TrainingCenterRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $center = TrainingCenter::findOrFail($id);
            $center->update($request->validated());
            $center->load('governorate');

            return $this->okResponse(
                TrainingCenterResource::make($center),
                "Training center updated successfully"
            );
        }, 'TrainingCenterController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $center = TrainingCenter::findOrFail($id);
            $center->delete();

            return $this->okResponse(
                null,
                "Training center deleted successfully"
            );
        }, 'TrainingCenterController@destroy');
    }
}
