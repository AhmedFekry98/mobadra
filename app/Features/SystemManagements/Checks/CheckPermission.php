<?php

namespace App\Features\SystemManagements\Checks;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Thi alias to register by it
     *
     * @var string
     * @static
     */
    public static $alias = 'check.permission';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        // check permission
        if (!$user->tokenCan($permission)) {
            return response()->json(['message' => 'Forbidden - You don\'t have this permission'], 403);
        }

        return $next($request);
    }
}
