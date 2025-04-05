<?php

namespace Modules\Auth\Enums;

enum GuardEnum: string
{
    case WEB = 'web';
    case API = 'api';
    
    /**
     * Get all available guard names
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    /**
     * Check if a guard name is valid
     */
    public static function isValid(string $guard): bool
    {
        return in_array($guard, self::values());
    }
}