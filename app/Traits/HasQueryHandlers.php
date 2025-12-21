<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Contract\QueryHandlerInterface;

trait HasQueryHandlers
{
    /**
     * Apply all registered query handlers (filters, search, sorting)
     */
    protected function applyHandlers(Builder $query, array $handlers, array $data): Builder
    {
        foreach ($handlers as $handler) {
            if ($handler instanceof QueryHandlerInterface) {
                $query = $handler->apply($query, $data);
            }
        }

        return $query;
    }
}
