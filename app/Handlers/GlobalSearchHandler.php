<?php

namespace App\Handlers;

use App\Contract\QueryHandlerInterface;
use Illuminate\Database\Eloquent\Builder;

class GlobalSearchHandler implements QueryHandlerInterface
{
    protected array $searchableColumns;

    public function __construct(array $searchableColumns = [])
    {
        $this->searchableColumns = $searchableColumns;
    }

    public function apply(Builder $query, array $data): Builder
    {
        $term = $data['search'] ?? '';

        if (!$term || empty($this->searchableColumns)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            foreach ($this->searchableColumns as $column) {
                // Check if column contains relationship (has dot notation)
                if (str_contains($column, '.')) {

                    $this->applyRelationshipSearch($q, $column, $term);
                } else {
                    // Regular column search
                    $q->orWhere($column, 'like', "%{$term}%");
                }
            }
        });
    }

    /**
     * Apply search on relationship columns
     */
    protected function applyRelationshipSearch($query, string $column, string $term): void
    {
        // Split the relationship path (e.g., 'badge.name' -> ['badge', 'name'])
        $parts = explode('.', $column);
        $relationColumn = array_pop($parts); // Last part is the column
        $relationPath = implode('.', $parts); // Everything else is the relation path

        $query->orWhereHas($relationPath, function ($relationQuery) use ($relationColumn, $term) {
            $relationQuery->where($relationColumn, 'like', "%{$term}%");
        });
    }

    /**
     * Set searchable columns dynamically
     */
    public function setSearchableColumns(array $columns): self
    {
        $this->searchableColumns = $columns;
        return $this;
    }
}
