<?php 

namespace Modules\Training\Enums;

enum CategoryTypeEnum: string
{
    case EXERCISES = 'exercises';
    case FOODS = 'foods';

    /**
     * Get all enum values as an array
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
