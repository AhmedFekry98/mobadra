<?php

namespace App\Features\AuthManagement\Controllers;

use App\Features\AuthManagement\Models\VerificationToken;
use App\Features\AuthManagement\Requests\SendOtpRequest;
use App\Features\AuthManagement\Requests\VerifyOtpRequest;
use App\Features\SystemManagements\Models\User;
use App\Mail\VerifyEmailMail;
use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    use ApiResponses;

    /**
     * Send OTP to email for verification
     */
    public function sendEmailOtp(SendOtpRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->badResponse(message: 'User not found');
        }

        if ($user->email_verified_at) {
            return $this->badResponse(message: 'Email already verified');
        }

        // Check rate limit (2 minutes)
        if (Cache::has('email_otp_' . $request->email)) {
            return $this->badResponse(message: 'Please wait 2 minutes before requesting a new code');
        }

        // Generate OTP
        $otp = config('app.env') !== 'production' ? '00000' : rand(10000, 99999);

        // Store OTP
        VerificationToken::updateOrCreate(
            ['email' => $request->email, 'type' => 'email'],
            [
                'token' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
            ]
        );

        // Send email
        if (!Mail::to($request->email)->send(new VerifyEmailMail($otp))) {
            return $this->badResponse(message: 'Failed to send verification code');
        }

        // Set rate limit
        Cache::put('email_otp_' . $request->email, true, now()->addMinutes(2));

        return $this->okResponse(message: 'Verification code sent to email');
    }

    /**
     * Send OTP to phone for verification
     */
    public function sendPhoneOtp(SendOtpRequest $request)
    {
        $fullPhone = $request->getFullPhone();

        $userInfo = \App\Features\SystemManagements\Models\UserInformation::where('phone_code', $request->phone_code)
            ->where('phone_number', $request->phone_number)
            ->first();

        if (!$userInfo) {
            return $this->badResponse(message: 'User not found');
        }

        $user = $userInfo->user;

        if ($user->phone_verified_at) {
            return $this->badResponse(message: 'Phone already verified');
        }

        // Check rate limit (2 minutes)
        if (Cache::has('phone_otp_' . $fullPhone)) {
            return $this->badResponse(message: 'Please wait 2 minutes before requesting a new code');
        }

        // Generate OTP
        $otp = config('app.env') !== 'production' ? '00000' : rand(10000, 99999);

        // Store OTP
        VerificationToken::updateOrCreate(
            ['phone' => $fullPhone, 'type' => 'phone'],
            [
                'token' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
            ]
        );

        // TODO: Send SMS via WhatsApp or SMS gateway
        // WhatsApp::send($fullPhone, "Your verification code is: $otp");

        // Set rate limit
        Cache::put('phone_otp_' . $fullPhone, true, now()->addMinutes(2));

        return $this->okResponse(message: 'Verification code sent to phone');
    }

    /**
     * Verify email with OTP
     */
    public function verifyEmail(VerifyOtpRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->badResponse(message: 'User not found');
        }

        if ($user->email_verified_at) {
            return $this->badResponse(message: 'Email already verified');
        }

        $verification = VerificationToken::where('email', $request->email)
            ->where('type', 'email')
            ->first();

        if (!$verification) {
            return $this->badResponse(message: 'Verification code not found');
        }

        if (!Hash::check($request->code, $verification->token)) {
            return $this->badResponse(message: 'Invalid verification code');
        }

        if ($verification->expires_at < now()) {
            return $this->badResponse(message: 'Verification code expired');
        }

        // Mark email as verified
        $user->email_verified_at = now();
        $user->save();

        // Delete verification token
        $verification->delete();

        return $this->okResponse(message: 'Email verified successfully');
    }

    /**
     * Verify phone with OTP
     */
    public function verifyPhone(VerifyOtpRequest $request)
    {
        $fullPhone = $request->getFullPhone();

        $userInfo = \App\Features\SystemManagements\Models\UserInformation::where('phone_code', $request->phone_code)
            ->where('phone_number', $request->phone_number)
            ->first();

        if (!$userInfo) {
            return $this->badResponse(message: 'User not found');
        }

        $user = $userInfo->user;

        if ($user->phone_verified_at) {
            return $this->badResponse(message: 'Phone already verified');
        }

        $verification = VerificationToken::where('phone', $fullPhone)
            ->where('type', 'phone')
            ->first();

        if (!$verification) {
            return $this->badResponse(message: 'Verification code not found');
        }

        if (!Hash::check($request->code, $verification->token)) {
            return $this->badResponse(message: 'Invalid verification code');
        }

        if ($verification->expires_at < now()) {
            return $this->badResponse(message: 'Verification code expired');
        }

        // Mark phone as verified
        $user->phone_verified_at = now();
        $user->save();

        // Delete verification token
        $verification->delete();

        return $this->okResponse(message: 'Phone verified successfully');
    }
}
