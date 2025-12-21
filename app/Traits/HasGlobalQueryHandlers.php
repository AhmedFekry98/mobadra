<?php

namespace App\Traits;

use App\Handlers\GlobalFilterHandler;
use App\Handlers\GlobalSearchHandler;
use App\Handlers\GlobalSortHandler;
use App\Traits\HasQueryHandlers;
use Illuminate\Database\Eloquent\Builder;

trait HasGlobalQueryHandlers
{
    use HasQueryHandlers;

    /**
     * Apply global query handlers with metadata configuration
     */
    protected function applyGlobalHandlers(Builder $query, array $data, array $config): Builder
    {
        $handlers = [
            new GlobalSearchHandler($config['searchable_columns'] ?? []),
            new GlobalFilterHandler($config['filters'] ?? [], $config['operators'] ?? []),
            new GlobalSortHandler($config['sortable_columns'] ?? []),
        ];

        return $this->applyHandlers($query, $handlers, $data);
    }

    /**
     * Get query results with global handlers applied
     */
    protected function getWithGlobalHandlers(
        Builder $query,
        array $data,
        array $config,
        bool $paginate = false
    ) {
        $this->applyGlobalHandlers($query, $data, $config);

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }
}
