<?php

namespace App\Features\AuthManagement\Controllers;

use App\Features\AuthManagement\Models\PasswordResetToken;
use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Features\AuthManagement\Requests\ResetPasswordRequest;
use App\Features\SystemManagements\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    use ApiResponses;

    public function resetPassword(ResetPasswordRequest $request)
    {
        // get user token verify
        $tokenVerify = PasswordResetToken::where('verify_token', $request->verify_token)->first();
        // check token verify
        if (!$tokenVerify) {
            return $this->badResponse(
                message: 'Token verify not found'
            );
        }
        // check token not expired
        if ($tokenVerify->expires_at < now()) {
            return $this->badResponse(
                message: 'Token verify expired'
            );
        }

        // check token verify
        if ($request->verify_token !== $tokenVerify->verify_token) {
            return $this->badResponse(
                message: 'Invalid token verify'
            );
        }

        // update user password
        $user = User::where('email', $tokenVerify->email)
            ->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // delete token verify
        $tokenVerify->delete();

        return $this->okResponse(
            'Password reset successfully'
        );
    }
}
