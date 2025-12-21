<?php

namespace Graphicode\Features\Commands;

use PHPUnit\Runner\ParameterDoesNotExistException;

class ResourceMakeCommand extends BaseCommand
{

    protected $name = "feature:make-resource";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }
    }

    protected function getStub(): string
    {
        if ($this->option('collection')) {
            $stub = 'resource-collection';
        } else {
            $stub = 'resource';
        }

        return "$stub.stub";
    }

    protected function qualifyName(string $name): string
    {
        return '/Transformers/' . parent::qualifyName($name) . '.php';
    }

    protected function getReplacments(): array
    {
        $class = parent::qualifyName($this->getNameInput());

        return [
            'namespace' => $this->getRootNamespace() . 'Transformers',
            'class'     => $class
        ];
    }

    public function getOptions(): array
    {
        return [
            ['--force'],
            ['--collection']
        ];
    }
}
