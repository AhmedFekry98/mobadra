<?php

namespace App\Handlers;

use App\Contract\QueryHandlerInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GlobalFilterHandler implements QueryHandlerInterface
{
    protected Collection $allowedFilters;
    protected array $allowedOperators;

    public function __construct(array $allowedFilters = [], array $allowedOperators = [])
    {
        $this->allowedFilters = collect($allowedFilters)->keyBy('column');
        $this->allowedOperators = $allowedOperators;
    }

    public function apply(Builder $query, array $data): Builder
    {
        $filters = $data['filter'] ?? [];

        if (empty($filters) || $this->allowedFilters->isEmpty()) {
            return $query;
        }

        foreach ($filters as $column => $filter) {
            // Check if column is allowed
            if (!$this->allowedFilters->has($column)) {
                continue;
            }

            $operator = strtolower($filter['operator'] ?? '=');
            $value = $filter['value'] ?? null;

            // Check if operator is allowed for this column
            $columnMeta = $this->allowedFilters->get($column);
            if (!in_array($operator, array_map('strtolower', $columnMeta['operators'] ?? []))) {
                continue;
            }

            // Apply the filter based on operator
            if (str_contains($column, '.')) {
                // Handle relationship filtering
                $this->applyRelationshipFilter($query, $column, $operator, $value);
            } else {
                // Handle direct column filtering
                match ($operator) {
                    'in'        => $query->whereIn($column, (array) $value),
                    'not in'    => $query->whereNotIn($column, (array) $value),
                    'between'   => $query->whereBetween($column, (array) $value),
                    'not between' => $query->whereNotBetween($column, (array) $value),
                    'like'      => $query->where($column, 'like', "%{$value}%"),
                    'not like'  => $query->where($column, 'not like', "%{$value}%"),
                    'is null'   => $query->whereNull($column),
                    'is not null' => $query->whereNotNull($column),
                    default     => $query->where($column, $operator, $value),
                };
            }
        }

        return $query;
    }

    /**
     * Apply filter on relationship columns
     */
    protected function applyRelationshipFilter($query, string $column, string $operator, $value): void
    {
        // Split the relationship path (e.g., 'badge.name' -> ['badge', 'name'])
        $parts = explode('.', $column);
        $relationColumn = array_pop($parts); // Last part is the column
        $relationPath = implode('.', $parts); // Everything else is the relation path

        $query->whereHas($relationPath, function ($relationQuery) use ($relationColumn, $operator, $value) {
            match ($operator) {
                'in'        => $relationQuery->whereIn($relationColumn, (array) $value),
                'not in'    => $relationQuery->whereNotIn($relationColumn, (array) $value),
                'between'   => $relationQuery->whereBetween($relationColumn, (array) $value),
                'not between' => $relationQuery->whereNotBetween($relationColumn, (array) $value),
                'like'      => $relationQuery->where($relationColumn, 'like', "%{$value}%"),
                'not like'  => $relationQuery->where($relationColumn, 'not like', "%{$value}%"),
                'is null'   => $relationQuery->whereNull($relationColumn),
                'is not null' => $relationQuery->whereNotNull($relationColumn),
                default     => $relationQuery->where($relationColumn, $operator, $value),
            };
        });
    }

    /**
     * Set allowed filters dynamically
     */
    public function setAllowedFilters(array $filters): self
    {
        $this->allowedFilters = collect($filters)->keyBy('column');
        return $this;
    }

    /**
     * Set allowed operators dynamically
     */
    public function setAllowedOperators(array $operators): self
    {
        $this->allowedOperators = $operators;
        return $this;
    }
}
