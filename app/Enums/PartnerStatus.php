<?php

namespace App\Enums;

enum PartnerStatus: int
{
    case ATIVO = 1;
    case INATIVO = 0;

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
            self::ATIVO => 'Ativo',
            self::INATIVO => 'Inativo',
        };
    }
}
