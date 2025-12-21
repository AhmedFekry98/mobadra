<?php

namespace App\Enums;

enum AuditableType: string
{
    case USER = 'user';
    case ROLE = 'role';
    case PERMISSION = 'permission';
    case FAQ = 'faq';

    /**
     * Get all type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get model class name from type
     */
    public function getModelClass(): string
    {
        return match($this) {
            self::USER => \App\Features\SystemManagements\Models\User::class,
            self::ROLE => \App\Features\SystemManagements\Models\Role::class,
            self::PERMISSION => \App\Features\SystemManagements\Models\Permission::class,
            self::FAQ => \App\Features\SystemManagements\Models\Faq::class,
        };
    }

    /**
     * Get type from model class
     */
    public static function fromModelClass(string $modelClass): ?self
    {
        foreach (self::cases() as $type) {
            if ($type->getModelClass() === $modelClass) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Get human readable label
     */
    public function label(): string
    {
        return match($this) {
            self::USER => 'User',
            self::ROLE => 'Role',
            self::PERMISSION => 'Permission',
            self::FAQ => 'FAQ',
        };
    }

    /**
     * Get feature name for grouping
     */
    public function getFeature(): string
    {
        return match($this) {
            self::USER, self::ROLE, self::PERMISSION => 'System Management',
            self::FAQ => 'System Management',
        };
    }

    /**
     * Check if type is sensitive (requires special attention)
     */
    public function isSensitive(): bool
    {
        return in_array($this, [
            self::USER,
            self::ROLE,
            self::PERMISSION,
        ]);
    }
}
