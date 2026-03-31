<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberByPrefix implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || trim($value) === '') {
            $fail('El campo :attribute es inválido.');
            return;
        }

        $raw = trim($value);
        $normalized = preg_replace('/[\s\-()]/', '', $raw) ?? '';

        if ($normalized === '') {
            $fail('El campo :attribute es inválido.');
            return;
        }

        // Colombia local: 3XXXXXXXXX (10 dígitos)
        if (preg_match('/^3\d{9}$/', $normalized) === 1) {
            return;
        }

        // Colombia con código país: +573XXXXXXXXX o 573XXXXXXXXX
        if (preg_match('/^(\+57|57)3\d{9}$/', $normalized) === 1) {
            return;
        }

        // Internacional genérico: + seguido de 8-15 dígitos
        if (preg_match('/^\+\d{8,15}$/', $normalized) === 1) {
            return;
        }

        // Internacional sin +: 8-15 dígitos
        if (preg_match('/^\d{8,15}$/', $normalized) === 1) {
            return;
        }

        $fail('El campo :attribute no cumple el formato permitido. Si inicia en 3 debe tener 10 dígitos (Colombia), o usar formato internacional válido.');
    }
}
