<?php

namespace App\Features\Badges\Metadata;

class BadgeMetadata
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
            'type'
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
                'column' => 'type',
                'label' => 'Type',
                'type' => 'text',
                'operators' => ['=', '!=', 'in'],
                'searchable' => false
            ],
            [
                'column' => 'description',
                'label' => 'Description',
                'type' => 'text',
                'operators' => ['like', '=', '!='],
                'searchable' => true
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
                'label' => 'Text',
                'component' => 'TextInput',
                'validation' => 'string|max:255'
            ],
            'select' => [
                'label' => 'Select',
                'component' => 'SelectInput',
                'validation' => 'string'
            ],
            'date' => [
                'label' => 'Date',
                'component' => 'DateInput',
                'validation' => 'date'
            ],
            'number' => [
                'label' => 'Number',
                'component' => 'NumberInput',
                'validation' => 'numeric'
            ],
            'boolean' => [
                'label' => 'Yes/No',
                'component' => 'BooleanInput',
                'validation' => 'boolean'
            ]
        ];
    }


}
