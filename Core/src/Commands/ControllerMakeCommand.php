<?php

namespace Graphicode\Features\Commands;

use PHPUnit\Runner\ParameterDoesNotExistException;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends BaseCommand
{

    protected $name = "feature:make-controller";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }
    }

    protected function getStub(): string
    {
        if ($this->option('invokable')) {
            return 'controller.invokable.stub';
        }

        return 'controller.api.stub';
    }

    protected function qualifyName(string $name): string
    {
        return '/Controllers/' . parent::qualifyName($name) . '.php';
    }

    protected function getReplacments(): array
    {
        $class = parent::qualifyName($this->getNameInput());
        
        // Extract model name from controller name (remove 'Controller' suffix)
        $modelName = str_replace('Controller', '', $class);
        $modelVariable = \Illuminate\Support\Str::camel($modelName);

        return [
            'namespace'     => $this->getRootNamespace() . 'Controllers',
            'rootNamespace' => $this->getRootNamespace(),
            'class'         => $class,
            'model'         => $modelName,
            'models'        => \Illuminate\Support\Str::plural($modelName),
            'modelVariable' => $modelVariable
        ];
    }

    public function getOptions(): array
    {
        return [
            ['--force'       ],
            ['--invokable'   ]
        ];
    }
}
