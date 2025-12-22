<?php

namespace App\Features\Courses;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class CoursesFeatureProvider extends ServiceProvider
{
    public $featureName = "Courses";

    public $featureNameLower = "courses";

    /**
     * Register services.
     */
    public function register(): void
    {
        \Graphicode\Features\FeaturesHelpers::loadMiddlewareFrom($this->featureName);

        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mapRoutes();
        $this->loadConfigurations();
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }


    /**
     * map routes
     */
    public function mapRoutes(): void
    {

        Route::prefix('api/')
            ->group(__DIR__ . '/Routes/api.php');
    }

    /**
     * Load feature configurations.
     */
    public function loadConfigurations(): void
    {
        $featureConfigFiles = File::files(__DIR__ . '/Config');
        foreach ($featureConfigFiles as $splFile) {
            list($name, $extenssion) = explode('.', $splFile->getFilename());
            $path = $splFile->getRealPath();
            $this->mergeConfigFrom($path, 'features.' . $name);
        }
    }
}
