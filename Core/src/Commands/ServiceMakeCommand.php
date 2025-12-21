<?php

namespace Graphicode\Features\Commands;

use Illuminate\Support\Str;

class ServiceMakeCommand extends BaseCommand
{

    protected $name = "feature:make-service";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }
    }

    protected function getStub(): string
    {
        if ($this->option('crud')) {
            $stub = 'service-crud';
        } else {
            $stub = 'service-basic';
        }

        return "$stub.stub";
    }

    protected function qualifyName(string $name): string
    {
        return '/Services/' . parent::qualifyName($name) . 'Service.php';
    }

    protected function getReplacments(): array
    {
        $model           = parent::qualifyName($this->getNameInput());
        $ModelPlural = Str::plural(lcfirst($model));
        $ModelSingular   = Str::singular(lcfirst($model));
        $modelVariable   = Str::camel($model);
        $class           = $model . 'Service';

        return [
            'namespace'         => $this->getRootNamespace() . 'Services',
            'rootNamespace'     => $this->getRootNamespace(),
            'models'            => Str::plural($model),
            'model'             => $model,
            'modelPlural'       => $ModelPlural,
            'modelSingular'     => $ModelSingular,
            'modelVariable'     => $modelVariable,
            'class'             => $class
        ];
    }

    public function getOptions(): array
    {
        return [
            ['--force'],
            ['--crud']
        ];
    }
}
