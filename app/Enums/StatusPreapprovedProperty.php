<?php

namespace App\Enums;

enum StatusPreapprovedProperty: string
{
    case APPROVED = 'approved';
    case PENDING = 'pending';

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
            self::APPROVED => 'Aprovado',
            self::PENDING => 'Aguardando Aprovação',
        };
    }

    /**
     * Get a user-friendly label for the age range
     */
    public function badge(): string
    {
        return match($this) {
            self::APPROVED => 'success',
            self::PENDING => 'warning text-black',
        };
    }
}
