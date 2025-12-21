<?php

namespace Graphicode\Features;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class FeaturesHelpers
{
    public static function loadMiddlewareFrom(string $feature, $directory = 'Checks')
    {

        $checksNamespace = "App\\Features\\$feature\\$directory\\";
        $checksPath      = app_path("Features/$feature/$directory");

        if (File::exists($checksPath)) {

            $pathes = File::files($checksPath);

            foreach ($pathes as $path) {

                $checkName = explode('.', File::basename($path))[0];

                $class = $checksNamespace . $checkName;
                if (!class_exists($class) || !property_exists($class, 'alias')) continue;

                App::alias($class, $class::$alias);
            }
        }
    }
}
