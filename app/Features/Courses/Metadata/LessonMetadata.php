<?php

namespace App\Features\Courses\Metadata;

class LessonMetadata
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
            'title',
            'description',
        ];
    }

    public static function getFilters(): array
    {
        return [
            [
                'column' => 'title',
                'label' => 'Title',
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
                'column' => 'chapter_id',
                'label' => 'Chapter',
                'type' => 'select',
                'operators' => ['=', 'in'],
                'searchable' => false
            ],
            [
                'column' => 'order',
                'label' => 'Order',
                'type' => 'number',
                'operators' => ['=', '>', '<', '>=', '<='],
                'searchable' => false
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
            'select' => [
                'label' => 'Select',
                'component' => 'SelectInput',
                'validation' => 'string'
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
            ],
            'date' => [
                'label' => 'Date',
                'component' => 'DateInput',
                'validation' => 'date'
            ],
        ];
    }
}
