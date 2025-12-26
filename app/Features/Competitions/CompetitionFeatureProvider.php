<?php

namespace App\Features\Competitions;

use Core\Providers\FeatureServiceProvider;

class CompetitionFeatureProvider extends FeatureServiceProvider
{
    public string $name = 'Competitions';

    public function boot(): void
    {
        $this->loadMigrations();
        $this->loadRoutes();
    }
}
