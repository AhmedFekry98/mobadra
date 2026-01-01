<?php

namespace App\Features\Resources;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ResourcesFeatureProvider extends ServiceProvider
{
    public $featureName = "Resources";

    public $featureNameLower = "resources";

    /**
     * Register services.
     */
    public function register(): void
    {
        \Graphicode\Features\FeaturesHelpers::loadMiddlewareFrom($this->featureName);
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
        $configPath = __DIR__ . '/Config';
        if (File::isDirectory($configPath)) {
            $featureConfigFiles = File::files($configPath);
            foreach ($featureConfigFiles as $splFile) {
                list($name, $extenssion) = explode('.', $splFile->getFilename());
                $path = $splFile->getRealPath();
                $this->mergeConfigFrom($path, 'features.' . $name);
            }
        }
    }
}
