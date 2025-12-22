<?php

namespace App\Features\Chat;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ChatFeatureProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/chat.php', 'chat');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->mapRoutes();
    }

    protected function mapRoutes(): void
    {
        Route::middleware(['api'])
            ->prefix('api')
            ->group(__DIR__ . '/Routes/api.php');
    }
}
