<?php

namespace App\Features\Courses\Metadata;

class TermMetadata
{
    public static function get(): array
    {
        return [
            'filters' => self::getFilters(),
            'operators' => self::getOperators(),
            'field_types' => self::getFieldTypes(),
        ];
    }

    public static function getSearchableColumns(): array
    {
        return [
            'name',
            'description',
        ];
    }

    public static function getFilters(): array
    {
        return [
            [
                'column' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'operators' => ['=', 'like', '!='],
                'searchable' => true
            ],
            [
                'column' => 'description',
                'label' => 'Description',
                'type' => 'text',
                'operators' => ['like', '=', '!='],
                'searchable' => true
            ],
            [
                'column' => 'is_active',
                'label' => 'Active',
                'type' => 'boolean',
                'operators' => ['='],
                'searchable' => false
            ],
            [
                'column' => 'created_at',
                'label' => 'Created Date',
                'type' => 'date',
                'operators' => ['=', '>', '<', '>=', '<=', 'between'],
                'searchable' => false
            ],
        ];
    }

    public static function getOperators(): array
    {
        return [
            '=' => 'Equals',
            '!=' => 'Not Equals',
            '>' => 'Greater Than',
            '<' => 'Less Than',
            '>=' => 'Greater Than or Equal',
            '<=' => 'Less Than or Equal',
            'like' => 'Contains',
            'in' => 'In List',
            'between' => 'Between',
        ];
    }

    public static function getFieldTypes(): array
    {
        return [
            'text' => [
                'label' => 'Text',
                'component' => 'TextInput',
                'validation' => 'string|max:255'
            ],
            'boolean' => [
                'label' => 'Yes/No',
                'component' => 'BooleanInput',
                'validation' => 'boolean'
            ],
            'date' => [
                'label' => 'Date',
                'component' => 'DateInput',
                'validation' => 'date'
            ],
        ];
    }
}
