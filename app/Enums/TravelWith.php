<?php

namespace App\Enums;

enum TravelWith: string
{
    case FRIENDS = 'FRIENDS';
    case COUPLE = 'COUPLE';
    case WITH_CHILDREN_UNDER_5 = 'WITH_CHILDREN_UNDER_5';
    case WITH_CHILDREN_5_TO_12 = 'WITH_CHILDREN_5_TO_12';
    case FROM_12 = 'FROM_12';

    /**
     * Get all values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get a user-friendly label for the travel with option
     */
    public function label(): string
    {
        return match($this) {
            self::FRIENDS => 'Amigos',
            self::COUPLE => 'Casal',
            self::WITH_CHILDREN_UNDER_5 => 'Com crianças de até 05 anos',
            self::WITH_CHILDREN_5_TO_12 => 'Crianças de 05 a 12 anos',
            self::FROM_12 => 'A partir de 12 anos',
        };
    }
}