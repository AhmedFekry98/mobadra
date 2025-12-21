<?php

namespace Graphicode\Features\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class PolicyMakeCommand extends BaseCommand
{
    protected $name = "feature:make-policy";

    protected $description = "Create a new policy class for a feature";

    public function handle()
    {
        if (parent::handle() == false) {
            return false;
        }
    }

    protected function getStub(): string
    {
        if ($this->option('model')) {
            return 'policy.model.stub';
        }

        return 'policy.stub';
    }

    protected function qualifyName(string $name): string
    {
        $name = parent::qualifyName($name);
        
        if (!Str::endsWith($name, 'Policy')) {
            $name .= 'Policy';
        }

        return '/Policies/' . $name . '.php';
    }

    protected function getReplacments(): array
    {
        $policyName = parent::qualifyName($this->getNameInput());
        
        if (!Str::endsWith($policyName, 'Policy')) {
            $policyName .= 'Policy';
        }

        $replacements = [
            'namespace'     => $this->getRootNamespace() . 'Policies',
            'rootNamespace' => $this->getRootNamespace(),
            'class'         => $policyName
        ];

        // If model option is provided, add model-specific replacements
        if ($this->option('model')) {
            $model = Str::studly($this->option('model'));
            $modelVariable = Str::camel($model);

            $replacements['model'] = $model;
            $replacements['modelVariable'] = $modelVariable;
        }

        return $replacements;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the policy applies to'],
        ]);
    }
}
