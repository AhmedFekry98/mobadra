<?php

namespace App\Features\SystemManagements\Metadata;

class RoleMetadata
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
            'description'
        ];
    }

    public static function getFilters(): array
    {
        return [
            [
                'column' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'operators' => ['=', '!=', 'like', 'not like'],
                'searchable' => true
            ],
            [
                'column' => 'description',
                'label' => 'Description',
                'type' => 'text',
                'operators' => ['=', '!=', 'like', 'not like'],
                'searchable' => true
            ],
            [
                'column' => 'is_active',
                'label' => 'Is Active',
                'type' => 'boolean',
                'operators' => ['=', '!='],
                'searchable' => false
            ],
            [
                'column' => 'created_at',
                'label' => 'Created Date',
                'type' => 'date',
                'operators' => ['=', '>', '<', '>=', '<=', 'between'],
                'searchable' => false
            ],
            [
                'column' => 'updated_at',
                'label' => 'Updated Date',
                'type' => 'date',
                'operators' => ['=', '>', '<', '>=', '<=', 'between'],
                'searchable' => false
            ]
        ];
    }

    public static function getOperators(): array
    {
        return [
            '=' => 'Equals',
            '!=' => 'Not Equals',
            '<>' => 'Not Equals',
            '>' => 'Greater Than',
            '<' => 'Less Than',
            '>=' => 'Greater Than or Equal',
            '<=' => 'Less Than or Equal',
            'like' => 'Contains',
            'not like' => 'Does Not Contain',
            'in' => 'In List',
            'not in' => 'Not In List',
            'between' => 'Between',
            'not between' => 'Not Between',
            'is null' => 'Is Empty',
            'is not null' => 'Is Not Empty'
        ];
    }

    public static function getFieldTypes(): array
    {
        return [
            'text' => [
                'operators' => ['=', '!=', 'like', 'not like', 'is null', 'is not null'],
                'input_type' => 'text'
            ],
            'number' => [
                'operators' => ['=', '!=', '>', '<', '>=', '<=', 'between', 'not between', 'is null', 'is not null'],
                'input_type' => 'number'
            ],
            'date' => [
                'operators' => ['=', '!=', '>', '<', '>=', '<=', 'between', 'not between', 'is null', 'is not null'],
                'input_type' => 'date'
            ],
            'select' => [
                'operators' => ['=', '!=', 'in', 'not in', 'is null', 'is not null'],
                'input_type' => 'select'
            ],
            'boolean' => [
                'operators' => ['=', '!=', 'is null', 'is not null'],
                'input_type' => 'checkbox'
            ]
        ];
    }
}
