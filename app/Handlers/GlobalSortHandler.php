<?php

namespace App\Handlers;

use App\Contract\QueryHandlerInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class GlobalSortHandler implements QueryHandlerInterface
{
    protected array $allowedColumns;

    public function __construct(array $allowedColumns = [])
    {
        $this->allowedColumns = $allowedColumns;
    }

    public function apply(Builder $query, array $data): Builder
    {
        $sort = $data['sort'] ?? null;

        if (!$sort) {
            return $query;
        }

        // Handle format: sort[column]=created_at&sort[direction]=asc
        if (isset($sort['column']) && isset($sort['direction'])) {
            $column = $sort['column'];
            $direction = strtolower($sort['direction']);

            // Validate column is allowed
            if (!empty($this->allowedColumns) && !in_array($column, $this->allowedColumns)) {
                return $query;
            }

            // Validate direction
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'asc';
            }

            // Apply sorting (relationship or direct)
            if (str_contains($column, '.')) {
                $this->applyRelationshipSort($query, $column, $direction);
            } else {
                $query->orderBy($column, $direction);
            }
            
            return $query;
        }

        // Handle format: sort[created_at]=asc&sort[name]=desc
        foreach ($sort as $column => $direction) {
            // Skip if this is the column/direction format
            if (in_array($column, ['column', 'direction'])) {
                continue;
            }

            // Validate column is allowed
            if (!empty($this->allowedColumns) && !in_array($column, $this->allowedColumns)) {
                continue;
            }

            $direction = strtolower($direction ?? 'asc');

            // Validate direction
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'asc';
            }

            // Apply sorting (relationship or direct)
            if (str_contains($column, '.')) {
                $this->applyRelationshipSort($query, $column, $direction);
            } else {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    /**
     * Apply sorting on relationship columns
     */
    protected function applyRelationshipSort($query, string $column, string $direction): void
    {
        // Split the relationship path (e.g., 'badge.name' -> ['badge', 'name'])
        $parts = explode('.', $column);
        $relationColumn = array_pop($parts); // Last part is the column
        $relationPath = implode('.', $parts); // Everything else is the relation path

        // Use join for relationship sorting
        $relationTable = $this->getRelationTable($relationPath);
        $currentTable = $query->getModel()->getTable();
        
        // Create a unique alias for the join
        $alias = $relationPath . '_sort';
        
        $query->leftJoin("{$relationTable} as {$alias}", function ($join) use ($currentTable, $alias, $relationPath) {
            $foreignKey = $this->getForeignKey($relationPath);
            $join->on("{$currentTable}.{$foreignKey}", '=', "{$alias}.id");
        })->orderBy("{$alias}.{$relationColumn}", $direction);
    }

    /**
     * Get the table name for a relation
     */
    protected function getRelationTable(string $relationPath): string
    {
        // Simple implementation - you might want to make this more sophisticated
        return Str::plural($relationPath);
    }

    /**
     * Get the foreign key for a relation
     */
    protected function getForeignKey(string $relationPath): string
    {
        // Simple implementation - you might want to make this more sophisticated
        return $relationPath . '_id';
    }

    /**
     * Set allowed columns dynamically
     */
    public function setAllowedColumns(array $columns): self
    {
        $this->allowedColumns = $columns;
        return $this;
    }
}
