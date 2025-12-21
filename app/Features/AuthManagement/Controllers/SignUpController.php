<?php

namespace App\Features\AuthManagement\Controllers;

use App\Features\AuthManagement\Requests\SignUpRequest;
use App\Features\SystemManagements\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SignUpController extends Controller
{
    use ApiResponses;

    public function signUp(SignUpRequest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // create token
        $abilities = $user->allPermissions()->pluck('name')->toArray();

        $user->token = $user->createToken('token', $abilities)->plainTextToken;

        return $this->okResponse(
            data: $user,
            message: "User created successfully"
        );
    }
}
