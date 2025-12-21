<?php

namespace Graphicode\Features\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class FeaturesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->commands([
            \Graphicode\Features\Commands\FeatureMakeCommand::class,
            \Graphicode\Features\Commands\MigrateMakeCommand::class,
            \Graphicode\Features\Commands\ModelMakeCommand::class,
            \Graphicode\Features\Commands\FactoryMakeCommand::class,
            \Graphicode\Features\Commands\SeederMakeCommand::class,
            \Graphicode\Features\Commands\ControllerMakeCommand::class,
            \Graphicode\Features\Commands\RequestMakeCommand::class,
            \Graphicode\Features\Commands\ResourceMakeCommand::class,
            \Graphicode\Features\Commands\ServiceMakeCommand::class,
            \Graphicode\Features\Commands\RepositoryMakeCommand::class,
            \Graphicode\Features\Commands\MiddlewareMakeCommand::class,
            \Graphicode\Features\Commands\PolicyMakeCommand::class,
        ]);

        $featuresNamespace = "App\\Features\\";
        $featuresPath      = app_path('Features');

        if (File::exists($featuresPath)) {
            $pathes = File::directories($featuresPath);
            foreach ($pathes as $path) {
                $featureName = File::basename($path);
                $class = $featuresNamespace . "$featureName\\$featureName"  . 'FeatureProvider';
                if (! class_exists($class) ) continue;
                $this->app->register($class);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
