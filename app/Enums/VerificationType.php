<?php

namespace App\Enums;

enum VerificationType: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';

    /**
     * Get all verification type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human readable label
     */
    public function label(): string
    {
        return match($this) {
            self::EMAIL => 'Email',
            self::PHONE => 'Phone',
        };
    }
}
