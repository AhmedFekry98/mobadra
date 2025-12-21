<?php

namespace Graphicode\Features\Commands;

use Illuminate\Support\Str;

class MiddlewareMakeCommand extends BaseCommand
{

    protected $name = "feature:make-middleware";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }
    }

    protected function getStub(): string
    {
        return "middleware.stub";
    }

    protected function qualifyName(string $name): string
    {
        return '/Checks/' . parent::qualifyName($name) . '.php';
    }

    protected function getReplacments(): array
    {
        $class = parent::qualifyName($this->getNameInput());
        $alias = Str::snake($class);

        return [
            'namespace' => $this->getRootNamespace() . 'Checks',
            'class'     => $class,
            'alias'     => $alias,
        ];
    }

    public function getOptions(): array
    {
        return [
            ['--force'],
        ];
    }
}
