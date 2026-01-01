<?php

namespace App\Features\AcceptanceExams;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AcceptanceExamsFeatureProvider extends ServiceProvider
{
    public $featureName = "AcceptanceExams";

    public $featureNameLower = "acceptance_exams";

    /**
     * Register services.
     */
    public function register(): void
    {
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
        $configPath = __DIR__ . '/Config';
        if (!File::exists($configPath)) {
            return;
        }

        $featureConfigFiles = File::files($configPath);
        foreach ($featureConfigFiles as $splFile) {
            list($name, $extenssion) = explode('.', $splFile->getFilename());
            $path = $splFile->getRealPath();
            $this->mergeConfigFrom($path, 'features.' . $name);
        }
    }
}
