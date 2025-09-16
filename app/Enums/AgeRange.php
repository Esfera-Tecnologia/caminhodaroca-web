<?php

namespace App\Enums;

enum AgeRange: string
{
    case UNDER_18 = 'UNDER_18';
    case FROM_18_TO_29 = 'FROM_18_TO_29';
    case FROM_30_TO_44 = 'FROM_30_TO_44';
    case FROM_45_TO_59 = 'FROM_45_TO_59';
    case ABOVE_60 = 'ABOVE_60';

    /**
     * Get all values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get a user-friendly label for the age range
     */
    public function label(): string
    {
        return match($this) {
            self::UNDER_18 => 'Menos de 18 anos',
            self::FROM_18_TO_29 => '18 a 29 anos',
            self::FROM_30_TO_44 => '30 a 44 anos',
            self::FROM_45_TO_59 => '45 a 59 anos',
            self::ABOVE_60 => '60 anos ou mais',
        };
    }
}