<?php

namespace App\Enums;

enum PreapprovedPartnerStatus: int
{
    case APPROVED = 1;
    case PENDING = 0;

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
}
