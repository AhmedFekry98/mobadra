<?php

namespace Graphicode\Features\Commands;

use PHPUnit\Runner\ParameterDoesNotExistException;

class SeederMakeCommand extends BaseCommand
{

    protected $name = "feature:make-seeder";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }
    }

    protected function getStub(): string
    {
        return 'seeder.stub';
    }

    protected function qualifyName(string $name): string
    {
        return '/Seeders/' . parent::qualifyName($name) . 'TableSeeder.php';
    }

    protected function getReplacments(): array
    {
        $class = parent::qualifyName($this->getNameInput()) . 'TableSeeder';

        return [
            'namespace' => $this->getRootNamespace() . 'Seeders',
            'class'     => $class
        ];
    }
}
