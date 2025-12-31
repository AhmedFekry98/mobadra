<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleFromHeader
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('Accept-Language', 'en');
        
        // Only allow 'en' or 'ar', default to 'en' for any other value
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }
        
        App::setLocale($lang);
        return $next($request);
    }
}
