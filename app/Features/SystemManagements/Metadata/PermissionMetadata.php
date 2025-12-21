<?php

namespace App\Features\SystemManagements\Metadata;

class PermissionMetadata
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
            'caption',
            'group'
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
                'column' => 'caption',
                'label' => 'Caption',
                'type' => 'text',
                'operators' => ['=', '!=', 'like', 'not like'],
                'searchable' => true
            ],
            [
                'column' => 'group',
                'label' => 'Group',
                'type' => 'select',
                'operators' => ['=', '!=', 'in'],
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

    public static function getPermissionGroups(): array
    {
        return [
            'Users' => 'Users',
            'Roles' => 'Roles',
            'Permissions' => 'Permissions',
            'Badges' => 'Badges',
            'Questions' => 'Questions',
            'Leads' => 'Leads',
            'Subscriptions' => 'Subscriptions',
            'Plans' => 'Plans',
            'User Credits' => 'User Credits',
            'Credit Transactions' => 'Credit Transactions',
            'Conversations' => 'Conversations',
            'Messages' => 'Messages',
            'Quick Messages' => 'Quick Messages'
        ];
    }
}
