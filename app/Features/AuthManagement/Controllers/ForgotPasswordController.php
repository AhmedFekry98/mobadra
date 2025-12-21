<?php

namespace App\Features\AuthManagement\Controllers;

use App\Features\AuthManagement\Models\PasswordResetToken;
use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;
use App\Features\AuthManagement\Requests\ForgotPasswordRequest;
use App\Features\SystemManagements\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Facades\WhatsApp;
use App\Features\AuthManagement\Requests\ForgotVerifyCodeRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;

class ForgotPasswordController extends Controller
{
    use ApiResponses;


    public function forgotPassword(ForgotPasswordRequest $request)
    {
        // check user
        $user = User::where('email', $request->email)
            ->first();

        if (!$user) {
            return $this->badResponse(
                message: 'User not found'
            );
        }

        // check user verification last 2 minutes
        if (Cache::has('user_verification_' . $request->email)) {
            return $this->badResponse(
                message: 'User verification last 2 minutes'
            );
        }
        // generate verification code
        $verificationCode = config('app.env') !== 'production'
            ? '00000'
            : rand(10000, 99999);
            // update or create verification code
            PasswordResetToken::updateOrCreate([
                'email' => $request->email,
            ], [
                'verify_token' => Hash::make($verificationCode),
                'expires_at' => now()->addMinutes(2),
            ]);


        // send verification code to user
        if (!Mail::to($request->email)->send(new ForgotPasswordMail($verificationCode))) {
            return $this->badResponse(
                message: 'Failed to send verification code'
            );
        }
        // save verification code to cache
        Cache::put('user_verification_' . $request->email, $verificationCode, now()->addMinutes(2));

        return $this->okResponse(
            'Verification code sent successfully'
        );
    }

    public function ForgotVerifyCode(ForgotVerifyCodeRequest $request)
    {
       // check verification code
       $verificationCode = PasswordResetToken::where('email', $request->email)
       ->first();

       if (!$verificationCode) {
           return $this->badResponse(
               message: 'Verification code not found'
           );
       }
       // check verification code
       if (!Hash::check($request->code, $verificationCode->verify_token)) {
           return $this->badResponse(
               message: 'Invalid verification code'
           );
       }

       // check verification code expiration
       if ($verificationCode->expires_at < now()) {
           return $this->badResponse(
               message: 'Verification code expired'
           );
       }

       return $this->okResponse(
           data: $verificationCode,
           message: 'Verification code verified successfully'
       );
    }
}
