<?php

namespace App\Features\Badges\Metadata;

class BadgeConditionMetadata
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
            'field',
            'value',
            'badge.name',        // Search in related badge's name
        ];
    }

    public static function getFilters(): array
    {
        return [
            [
                'column' => 'field',
                'label' => 'Field',
                'type' => 'text',
                'operators' => ['=', 'like', '!='],
                'searchable' => true
            ],
            [
                'column' => 'value',
                'label' => 'Value',
                'type' => 'text',
                'operators' => ['=', 'like', '!='],
                'searchable' => true
            ],
            [
                'column' => 'badge_id',
                'label' => 'Badge ID',
                'type' => 'select',
                'operators' => ['=', '!=', 'in'],
                'searchable' => false
            ],
            // Relationship filters
            [
                'column' => 'badge.name',
                'label' => 'Badge Name',
                'type' => 'text',
                'operators' => ['=', 'like', '!='],
                'searchable' => true
            ],
            [
                'column' => 'badge.type',
                'label' => 'Badge Type',
                'type' => 'select',
                'options' => ['gold', 'silver', 'bronze'],
                'operators' => ['=', '!=', 'in'],
                'searchable' => false
            ],
            [
                'column' => 'badge.description',
                'label' => 'Badge Description',
                'type' => 'text',
                'operators' => ['like', '=', '!='],
                'searchable' => true
            ],
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
