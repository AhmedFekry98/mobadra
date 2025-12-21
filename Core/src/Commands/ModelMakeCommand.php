<?php

namespace Graphicode\Features\Commands;

use Illuminate\Support\Str;

class ModelMakeCommand extends BaseCommand
{

    protected $name = "feature:make-model";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }

        if ($this->option('m')) {
            $this->createMigration();
        }
    }

    public function createMigration()
    {
        $name = 'create_' . Str::plural( Str::snake($this->getNameInput()) ) . '_table';
        $this->call('feature:make-migration', [
            'name'      => $name,
            'feature'   => $this->getFeatureInput()
        ]);
    }

    protected function getStub(): string
    {
        return 'model.stub';
    }

    protected function qualifyName(string $name): string
    {
        return '/Models/' . parent::qualifyName($name) . '.php';
    }

    protected function getReplacments(): array
    {
        $class = parent::qualifyName($this->getNameInput());

        return [
            'namespace' => $this->getRootNamespace() . 'Models',
            'class'     => $class
        ];
    }

    public function getOptions(): array
    {
        return [
            ['--force'],
            ['--m'],
            ['-f'],
            ['-s']
        ];
    }
}
