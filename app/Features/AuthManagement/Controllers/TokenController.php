<?php

namespace App\Features\AuthManagement\Controllers;

use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Features\AuthManagement\Requests\LoginRequest;
use App\Features\SystemManagements\Models\User;
use Illuminate\Support\Facades\Hash;

class TokenController extends Controller
{
    use ApiResponses;


    public function login(LoginRequest $request)
    {
        // check credentials
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->badResponse(
                message: 'User not found'
            );
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->badResponse(
                message: 'Invalid credentials'
            );
        }

        $abilities = $user->allPermissions()->pluck('name')->toArray();

        $user->token = $user->createToken('token', $abilities)->plainTextToken;

        return $this->okResponse(
            data: $user,
            message: 'Login successful'
        );
    }

    public function logout(Request $request)
    {
        return $this->okResponse(
            data: $request->user()->currentAccessToken()->delete(),
            message: 'Logout successful'
        );
    }
}
