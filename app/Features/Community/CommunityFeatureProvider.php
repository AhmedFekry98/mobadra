<?php

namespace App\Features\Community;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CommunityFeatureProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/Routes/api.php');
    }
}
