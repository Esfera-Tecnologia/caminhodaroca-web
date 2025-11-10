<?php

namespace App\Enums;

enum StatusProperty: string
{
    case ATIVO = 'ativo';
    case INATIVO = 'inativo';

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

    /**
     * Get a user-friendly label for the age range
     */
    public function badge(): string
    {
        return match($this) {
            self::ATIVO => 'success',
            self::INATIVO => 'danger',
        };
    }
}
