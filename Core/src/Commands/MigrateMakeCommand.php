<?php

namespace Graphicode\Features\Commands;

use Graphicode\Features\Stub;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputArgument;

class MigrateMakeCommand extends Command
{
    protected $name = "feature:make-migration";


    protected $description = "Create New Migration File";

    public function handle()
    {
        $name = $this->argument('name');
        $feature = $this->argument('feature');


        $name              =    Str::snake($this->argument('name')) ;
        $featureStudly     = Str::studly($feature);
        $featurePath       = app_path("Features/$featureStudly");

        if (! File::exists($featurePath)) {
            $this->error("Feature [$feature] not found");
            return;
        }



        $pattern = '/^([a-zA-Z0-9]+)_([a-zA-Z0-9_]+)_table$/';
        preg_match($pattern, $name, $matches);
        list($name, $type, $table) = $matches;

        $filename         = now()->format('Y_m_d_hi') . '_' . $name;
        $filePath = "$featurePath/Migrations/$filename.php";

        $path = Stub::make("migration.$type", $filePath, [
            'table'     => $table
        ]);
        $this->info("Generate [$path] - DONE");
    }

    public function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of model'],
            ['feature', InputArgument::REQUIRED, 'The feature to create into'],
        ];
    }
}
