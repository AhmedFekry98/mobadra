<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\Route;
use App\Features\SystemManagements\Checks\CheckPermission;
use App\Http\Middleware\SetLocaleFromHeader;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Load feature routes
            $featuresPath = app_path('Features');
            foreach (glob($featuresPath . '/*/Routes/api.php') as $routeFile) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group($routeFile);
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SetLocaleFromHeader::class);
        $middleware->use([
            HandleCors::class,
        ]);
        $middleware->alias([
            'check.permission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
