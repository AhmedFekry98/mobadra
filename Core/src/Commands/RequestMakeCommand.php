<?php

namespace Graphicode\Features\Commands;

use PHPUnit\Runner\ParameterDoesNotExistException;

class RequestMakeCommand extends BaseCommand
{

    protected $name = "feature:make-request";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }
    }

    protected function getStub(): string
    {
        return 'request.stub';
    }

    protected function qualifyName(string $name): string
    {
        return '/Requests/' . parent::qualifyName($name) . '.php';
    }

    protected function getReplacments(): array
    {
        $class = parent::qualifyName($this->getNameInput());

        return [
            'namespace' => $this->getRootNamespace() . 'Requests',
            'class'     => $class
        ];
    }
}
