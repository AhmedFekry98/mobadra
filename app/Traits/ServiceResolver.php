<?php

namespace App\Traits;

trait ServiceResolver
{
    public function resolveService(string $service)
    {
        return app($service);
    }
}
