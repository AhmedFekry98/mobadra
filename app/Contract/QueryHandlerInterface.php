<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Builder;

interface QueryHandlerInterface
{
    public function apply(Builder $query, array $data): Builder;
}
