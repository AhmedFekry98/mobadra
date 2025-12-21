<?php

namespace App\Features\AuthManagement\Controllers;

use App\Features\AuthManagement\Requests\ProfileRequest;
use App\Features\AuthManagement\Services\ProfileService;
use App\Features\AuthManagement\Transformers\ProfileResource;
use App\Features\SystemManagements\Models\User;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use ApiResponses, HandleServiceExceptions;

    /**
     * Inject profile service in constructor
     */
    public function __construct(
        protected ProfileService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function show()
    {
        return $this->executeService(function () {
            /** @var User $user */
            $user = Auth::user();

            return $this->okResponse(
                ProfileResource::make($user),
                "Profile retrieved successfully"
            );
        }, 'ProfileController@show');
    }

    public function update(ProfileRequest $request)
    {
        return $this->executeService(function () use ($request) {
            /** @var User $user */
            $user = Auth::user();

            $updatedUser = $this->service->updateProfile(
                $user,
                $request->validated()
            );

            return $this->okResponse(
                ProfileResource::make($updatedUser),
                "Profile updated successfully"
            );
        }, 'ProfileController@update');
    }

    public function becomeProvider()
    {
        return $this->executeService(function () {
            /** @var User $user */
            $user = Auth::user();

            $updatedUser = $this->service->becomeProvider($user);

            return $this->okResponse(
                ProfileResource::make($updatedUser),
                "Profile updated successfully"
            );
        }, 'ProfileController@becomeProvider');
    }
}
