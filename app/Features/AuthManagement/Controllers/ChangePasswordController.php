<?php

namespace App\Features\AuthManagement\Controllers;

use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;
use App\Features\AuthManagement\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    use ApiResponses;


    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->badResponse(
                message: 'Current password is not correct'
            );
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->okResponse(
            $user,
            'Password changed successfully'
        );
    }
}
