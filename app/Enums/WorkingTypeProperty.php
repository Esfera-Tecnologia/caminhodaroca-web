<?php

namespace App\Enums;

enum WorkingTypeProperty: string
{
    case TODOS = 'todos';
    case FINS = 'fins';
    case AGENDAMENTO = 'agendamento';
    case PERSONALIZADO = 'personalizado';


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
            self::TODOS => 'Todos os Dias',
            self::FINS => 'Finais de Semana',
            self::AGENDAMENTO => 'Com Agendamento',
            self::PERSONALIZADO => 'Personalizado',
        };
    }
}
