<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedEmailDomain implements ValidationRule
{
    /**
     * @var array<int, string>
     */
    protected array $allowedDomains;

    /**
     * @param array<int, string> $allowedDomains
     */
    public function __construct(array $allowedDomains = ['gmail.com', 'hotmail.com'])
    {
        $this->allowedDomains = array_map('strtolower', $allowedDomains);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !str_contains($value, '@')) {
            $fail('El campo :attribute debe ser un correo válido.');
            return;
        }

        $domain = strtolower((string) substr(strrchr($value, '@'), 1));

        if (!in_array($domain, $this->allowedDomains, true)) {
            $fail('El campo :attribute solo permite dominios gmail.com o hotmail.com.');
        }
    }
}
