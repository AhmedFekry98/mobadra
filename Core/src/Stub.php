<?php


namespace Graphicode\Features;

use FFI;
use Illuminate\Support\Facades\File;

class Stub
{
    /**
     * The dynmic stubs path
     *
     * @var string
     */
    protected static $stubsPath = null;


    private function __construct() {}

    /**
     * Set stubs path.
     *
     * @param string $path
     * @return void
     */
    public static function setStubsPath(string $path): void
    {
        if (! File::exists($path)) throw new \Exception("Directory [$path] not exist");
        static::$stubsPath = $path;
    }

    /**
     * Generate file from stub template.
     *
     * @param string $stub
     * @param string $filePath
     * @param array $variables
     * @return void
     */
    public static function make(string $stub, string $filePath, array $variables = [])
    {

        $stubPath = File::exists(base_path("stubs/$stub.stub"))
            ? base_path("stubs/$stub.stub")
            : __DIR__ . "/../stubs/$stub.stub";

        if (! File::exists($stubPath)) throw new \Exception("Stub [$stubPath] not exist");

        $filePath = static::injectVariablesInString($filePath, $variables);

        if (File::exists($filePath)) throw new \Exception("Stub [$filePath] Already exist");

        $fileContents = File::get($stubPath);
        $fileContents = static::injectVariablesInString($fileContents, $variables);



        $directory = File::dirname($filePath);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, recursive: true);
        }


        File::put($filePath, $fileContents);
        return $filePath;
    }

    public static function injectVariablesInString(string $str, array $variables = []): string
    {
        foreach ($variables as $name => $value) {
            $pattern = "/\{\{\s*" . preg_quote($name, '/') . "\s*\}\}/";
            $str = preg_replace($pattern, $value, $str);
        }

        return $str;
    }
}
