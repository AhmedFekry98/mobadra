<?php


namespace Graphicode\Features\Commands;

use Graphicode\Features\Stub;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseCommand extends  Command implements PromptsForMissingInput
{
    /**
     * Reserved names that cannot be used for generation.
     *
     * @var string[]
     */
    protected $reservedNames = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'enum',
        'eval',
        'exit',
        'extends',
        'false',
        'final',
        'finally',
        'fn',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'match',
        'namespace',
        'new',
        'or',
        'parent',
        'print',
        'private',
        'protected',
        'public',
        'readonly',
        'require',
        'require_once',
        'return',
        'self',
        'static',
        'switch',
        'throw',
        'trait',
        'true',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'yield',
        '__CLASS__',
        '__DIR__',
        '__FILE__',
        '__FUNCTION__',
        '__LINE__',
        '__METHOD__',
        '__NAMESPACE__',
        '__TRAIT__',
    ];



    public function handle()
    {

        if ($this->isReservedName($this->getNameInput())) {
            $this->components->error('The name "' . $this->getNameInput() . '" is reserved by PHP.');
            return false;
        }

        if ($this->isReservedName($this->getFeatureInput())) {
            $this->components->error('The name "' . $this->getFeatureInput() . '" is reserved by PHP.');
            return false;
        }

        $name    = $this->qualifyName($this->getNameInput());


        $path = $this->getPath($name);

        if ( ! $this->option('force') && File::exists($path) ) {
            $this->components->error("File [$path] Already exists.");
            return false;
        }

        $stub = $this->getStubPath($this->getStub());


        $this->generateFile($stub, $path);
        $this->components->info("Generate File [$path] created successfuly.");
        return true;
    }

    protected function generateFile(string $stub, string $path)
    {
        $contents = File::get($stub);

        $replacments = $this->getReplacments();

        foreach ($replacments as $key => $variables) {
            $pattern  = '/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/';
            $contents = preg_replace($pattern, $variables, $contents);
        }

        $directory = File::dirname($path);
        if (! File::exists($directory) ) File::makeDirectory($directory, recursive: true);
        File::put($path, $contents);
    }

    protected function getPath(string $name = ''): string
    {
        $feature = Str::studly($this->getFeatureInput());

        return app_path("Features/$feature/$name");
    }

    protected function getStubPath(string $stub): string
    {
        return File::exists(base_path("stubs/$stub"))
            ? base_path("stubs/$stub")
            : __DIR__ . "/../../stubs/$stub";
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    abstract protected function getStub(): string;

    protected function qualifyName(string $name): string
    {
        return Str::studly($name);
    }

    protected function getReplacments(): array
    {
        return [];
    }

    protected function getRootNamespace(): string
    {
        $feature = STr::studly($this->getFeatureInput());
        return "App\\Features\\$feature\\";
    }

    /**
     * Get the name input
     *
     * @return string
     */
    protected function getNameInput()
    {
        $name = trim($this->argument('name'));

        if (Str::endsWith($name, '.php')) {
            return Str::substr($name, 0, -4);
        }

        return $name;
    }

    /**
     * Get the feature input
     *
     * @return string
     */
    protected function getFeatureInput()
    {
        $feature = trim($this->argument('feature'));

        if (Str::endsWith($feature, '.php')) {
            return Str::substr($feature, 0, -4);
        }

        return $feature;
    }

    /**
     * Checks whether the given name is reserved.
     *
     * @param  string  $name
     * @return bool
     */
    protected function isReservedName($name)
    {
        return in_array(
            strtolower($name),
            collect($this->reservedNames)
                ->transform(fn($name) => strtolower($name))
                ->all()
        );
    }

    public function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name to create'],
            ['feature', InputArgument::REQUIRED, 'Feature to create into'],
        ];
    }

    public function getOptions(): array
    {
        return [
            ['force'   , InputOption::VALUE_NONE],

        ] ;
    }

}
