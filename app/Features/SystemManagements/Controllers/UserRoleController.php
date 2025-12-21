<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Requests\AssignRoleRequest;
use App\Features\SystemManagements\Transformers\UserRoleResource;
use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;

class UserRoleController extends Controller
{
    use ApiResponses;
    private static $model = User::class;

    public function store(AssignRoleRequest $request)
    {
        try {
            $user = self::$model::findOrFail($request->user_id);
            $user->update([
                "role_id" => $request->role_id,
            ]);
            return $this->okResponse(UserRoleResource::make($user), "Success api call");
        } catch (\Exception $th) {
            return $this->badResponse($th->getMessage());
        }
    }
}
