<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InstagramRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // compatível com nullable
        }

        // Verifica se começa com @
        if (! str_starts_with($value, '@')) {
            $fail("O campo {$attribute} deve começar com '@'.");
            return;
        }

        // (Opcional) validar caracteres seguintes: letras, números, ponto e underline
        if (! preg_match('/^@[A-Za-z0-9._]+$/', $value)) {
            $fail("O campo {$attribute} contém caracteres inválidos.");
        }
    }
}
