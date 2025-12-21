<?php

namespace Graphicode\Features\Commands;

use Illuminate\Support\Str;

class RepositoryMakeCommand extends BaseCommand
{
    protected $name = "feature:make-repository";

    protected $description = "Create a new repository class for a feature";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }
    }

    protected function getStub(): string
    {
        return 'repository.stub';
    }

    protected function qualifyName(string $name): string
    {
        $name = parent::qualifyName($name);
        
        if (!Str::endsWith($name, 'Repository')) {
            $name .= 'Repository';
        }

        return '/Repositories/' . $name . '.php';
    }

    protected function getReplacments(): array
    {
        $repositoryName = parent::qualifyName($this->getNameInput());
        
        if (!Str::endsWith($repositoryName, 'Repository')) {
            $repositoryName .= 'Repository';
        }

        // Extract model name from repository name
        $modelName = Str::replaceLast('Repository', '', $repositoryName);
        $modelVariable = Str::camel($modelName);

        return [
            'namespace'     => $this->getRootNamespace() . 'Repositories',
            'rootNamespace' => $this->getRootNamespace(),
            'class'         => $repositoryName,
            'model'         => $modelName,
            'modelVariable' => $modelVariable
        ];
    }
}
