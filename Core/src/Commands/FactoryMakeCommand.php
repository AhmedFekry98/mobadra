<?php

namespace Graphicode\Features\Commands;

use PHPUnit\Runner\ParameterDoesNotExistException;

class FactoryMakeCommand extends BaseCommand
{

    protected $name = "feature:make-factory";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }
    }

    protected function getStub(): string
    {
        return 'factory.stub';
    }

    protected function qualifyName(string $name): string
    {
        return '/Factories/' . parent::qualifyName($name) . '.php';
    }

    protected function getReplacments(): array
    {
        $class = parent::qualifyName($this->getNameInput());

        return [
            'namespace' => $this->getRootNamespace() . 'Factories',
            'class'     => $class
        ];
    }
}
