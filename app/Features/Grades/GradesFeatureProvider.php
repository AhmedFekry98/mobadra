<?php

namespace App\Features\Grades;

use App\Features\Grades\Models\Grade;
use App\Features\Grades\Policies\GradePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class GradesFeatureProvider extends ServiceProvider
{
    public $featureName = "Grades";

    public $featureNameLower = "grades";

    public function register(): void
    {
        \Graphicode\Features\FeaturesHelpers::loadMiddlewareFrom($this->featureName);
    }

    public function boot(): void
    {
        $this->mapRoutes();
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->registerPolicies();
    }

    public function mapRoutes(): void
    {
        Route::prefix('api/')
            ->group(__DIR__ . '/Routes/api.php');
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Grade::class, GradePolicy::class);
    }
}
