<?php

namespace App\Features\SystemManagements\Metadata;

class AuditMetadata
{
    public static function get(): array
    {
        return [
            'filters' => self::getFilters(),
            'operators' => self::getOperators(),
            'sort_options' => self::getSortOptions(),
            'field_types' => self::getFieldTypes(),
        ];
    }



    public static function getSearchableColumns(): array
    {
        return [
            'action',
            'user.name',
            'user.email',
            'ip_address',
        ];
    }

    public static function getFilters(): array
    {
        return [
            [
                'column' => 'action',
                'label' => 'Action',
                'type' => 'text',
                'operators' => ['=', 'like', '!='],
                'searchable' => true
            ],
            [
                'column' => 'user.name',
                'label' => 'User Name',
                'type' => 'text',
                'operators' => ['=', '!=', 'in'],
                'searchable' => false
            ],
            [
                'column' => 'user.email',
                'label' => 'User Email',
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
                'column' => 'ip_address',
                'label' => 'IP Address',
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

    /**
     * Get sort options
     */
    protected static function getSortOptions(): array
    {
        return [
            'latest' => [
                'label' => 'Latest First',
                'field' => 'created_at',
                'direction' => 'desc',
            ],
            'oldest' => [
                'label' => 'Oldest First',
                'field' => 'created_at',
                'direction' => 'asc',
            ],
            'action' => [
                'label' => 'Action (A-Z)',
                'field' => 'action',
                'direction' => 'asc',
            ],
            'user' => [
                'label' => 'User (A-Z)',
                'field' => 'user_id',
                'direction' => 'asc',
            ],
            'entity' => [
                'label' => 'Entity Type (A-Z)',
                'field' => 'auditable_type',
                'direction' => 'asc',
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
