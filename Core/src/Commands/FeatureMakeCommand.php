<?php

namespace Graphicode\Features\Commands;

use Graphicode\Features\Stub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class FeatureMakeCommand extends Command

{
    protected $name = "feature:make";

    protected $description = "Create New Feature In Your Application";

    public function handle()
    {
        $features = $this->argument('name');
        foreach ($features as $feature) {
            if ($this->generateFeature($feature) == 1) {
                return 1;
            }
        }

        return 0;
    }

    public function generateFeature(string $name): int
    {
        $nameStudly = Str::studly($name);
        $featurePath = app_path("Features/$nameStudly");

        if (File::exists($featurePath)) {
            $this->error("Feature [$nameStudly] Already exists");
            return 1;
        }

        $rootNamespace = "App\\Features\\$nameStudly";

        $providerClass =  "{$nameStudly}FeatureProvider";
        $path = Stub::make('provider.feature', "{$featurePath}/$providerClass.php", [
            'namespace'     => $rootNamespace,
            'class'         => $providerClass,
            'name'          => $nameStudly,
            'nameLower'     => Str::lower($name)
        ]);
        $this->info("Generate [$featurePath] - DONE");

        $configFile = Str::snake($nameStudly);
        $path = Stub::make('config', "{$featurePath}/Config/$configFile.php", [
            'name'  => $nameStudly
        ]);
        $this->info("Generate [$featurePath] - DONE");

        $path = Stub::make('routes', "{$featurePath}/Routes/api.php", [
            'pluralPrefix' => Str::plural(  Str::kebab($nameStudly) )
        ]);
        $this->info("Generate [$featurePath] - DONE");


        return 0;
    }


    public function getArguments(): array
    {
        return [
            ['name', InputArgument::IS_ARRAY, 'List Of Features To Create'],
        ];
    }
}
