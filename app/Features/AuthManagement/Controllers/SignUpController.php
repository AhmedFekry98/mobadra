<?php

namespace App\Features\AuthManagement\Controllers;

use App\Features\AuthManagement\Requests\SignUpRequest;
use App\Features\SystemManagements\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SignUpController extends Controller
{
    use ApiResponses;

    public function signUp(SignUpRequest $request, $role = 'student')
    {

    try {
        DB::beginTransaction();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // assign default role
        $user->assignRole($role);

        // create token
        $abilities = $user->allPermissions()->pluck('name')->toArray();

        $user->token = $user->createToken('token', $abilities)->plainTextToken;


        DB::commit();

        return $this->okResponse(
            data: $user,
            message: "User created successfully"
        );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(message: "Failed to create user: " . $e->getMessage());
        }
    }
}
