<?php

namespace App\Features\SystemManagements\Metadata;

class FAQMetadata
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
            'question',
            'answer',
        ];
    }

    public static function getFilters(): array
    {
        return [
            [
                'column' => 'question',
                'label' => 'Question',
                'type' => 'text',
                'operators' => ['=', 'like', '!='],
                'searchable' => true
            ],
            [
                'column' => 'answer',
                'label' => 'Answer',
                'type' => 'text',
                'operators' => ['=', '!=', 'in'],
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
                'label' => 'Text',
                'component' => 'TextInput',
                'validation' => 'string|max:255'
            ],
            'date' => [
                'label' => 'Date',
                'component' => 'DateInput',
                'validation' => 'date'
            ],
            'boolean' => [
                'label' => 'Yes/No',
                'component' => 'BooleanInput',
                'validation' => 'boolean'
            ]
        ];
    }


}
