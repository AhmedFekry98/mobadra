<?php

namespace App\Features\AuthManagement\Controllers;

use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Features\AuthManagement\Models\MagicLink;
use App\Features\SystemManagements\Models\User;
use Illuminate\Support\Str;
use App\Jobs\SendMagicLoginLink;

class MagicLoginController extends Controller
{
    use ApiResponses;

    public function requestLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->badResponse('User not found');
        }

        // remove old links
        MagicLink::where('user_id', $user->id)->delete();

        $token = Str::random(64); // generate random token

        MagicLink::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => now()->addMinutes(15),
            'used'       => false,
        ]);

        SendMagicLoginLink::dispatch($user, $token);

        return $this->okResponse(
            message: 'Magic link In The Way',
            data: true,
        );
    }

    /** @var MagicLink|null $magic */

    public function verify(string $token)
    {

        $magic = MagicLink::with('user')->where('token', $token)->first();

        if (!$magic || !$magic->isValid()) {
            return $this->badResponse('Invalid or expired link');
        }

        // mark as used
        $magic->update(['used' => true]);

        // create Sanctum token with abilities based on your permission system
        $user = $magic->user;
        $abilities = method_exists($user, 'allPermissions')
            ? $user->allPermissions()->pluck('name')->toArray()
            : ['*'];

        $user->token = $user->createToken('magic-login', $abilities)->plainTextToken;

        return $this->okResponse(
            data: $user,
            message: 'Login successful'
        );
    }
}
