<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberByPrefix implements ValidationRule
{
    public function __construct(private readonly ?string $country = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || trim($value) === '') {
            $fail('Ingresa un número de teléfono válido.');
            return;
        }

        $normalized = $this->normalize((string) $value);

        if ($normalized === '' || preg_match('/^\+?[1-9]\d{7,14}$/', $normalized) !== 1) {
            $fail('Ingresa un número de teléfono válido en formato internacional.');
            return;
        }

        $country = $this->resolveCountry();
        $countryRules = config('phone.country_lengths', []);

        if ($country !== '' && isset($countryRules[$country])) {
            $rule = $countryRules[$country];
            $nationalNumber = $this->extractNationalNumber($normalized, (string) ($rule['dial_code'] ?? ''));
            $expectedLengths = array_map('intval', $rule['lengths'] ?? []);

            if ($nationalNumber === '' || !in_array(strlen($nationalNumber), $expectedLengths, true)) {
                $countryName = (string) ($rule['name'] ?? 'el país seleccionado');
                $expected = $this->formatExpectedLengths($expectedLengths);

                $fail("El número de teléfono para {$countryName} debe tener exactamente {$expected} dígitos.");
                return;
            }

            $startsWith = (array) ($rule['starts_with'] ?? []);
            if (!empty($startsWith)) {
                $validPrefix = false;
                foreach ($startsWith as $prefix) {
                    if (str_starts_with($nationalNumber, $prefix)) {
                        $validPrefix = true;
                        break;
                    }
                }

                if (!$validPrefix) {
                    $countryName = (string) ($rule['name'] ?? 'el país seleccionado');
                    $expectedPrefixes = implode(' o ', $startsWith);
                    $fail("El número de teléfono para {$countryName} debe comenzar con {$expectedPrefixes}.");
                }
            }

            return;
        }

        $digitsOnly = ltrim($normalized, '+');
        $length = strlen($digitsOnly);
        $min = (int) config('phone.default_min_length', 8);
        $max = (int) config('phone.default_max_length', 15);

        if ($length < $min || $length > $max) {
            $fail("El número de teléfono debe tener entre {$min} y {$max} dígitos.");
        }
    }

    private function resolveCountry(): string
    {
        $country = $this->country ?? (string) request()->input('phone_country', '');

        return strtolower(trim($country));
    }

    private function normalize(string $value): string
    {
        $value = trim($value);
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return '';
        }

        return str_starts_with($value, '+') ? '+' . $digits : $digits;
    }

    private function extractNationalNumber(string $normalized, string $dialCode): string
    {
        $digitsOnly = ltrim($normalized, '+');
        $dialCode = ltrim($dialCode, '+');

        if ($dialCode !== '' && str_starts_with($digitsOnly, $dialCode)) {
            return substr($digitsOnly, strlen($dialCode));
        }

        return $digitsOnly;
    }

    private function formatExpectedLengths(array $lengths): string
    {
        $lengths = array_values(array_unique(array_filter($lengths)));

        if (count($lengths) === 0) {
            return 'la cantidad requerida de';
        }

        if (count($lengths) === 1) {
            return (string) $lengths[0];
        }

        $last = array_pop($lengths);

        return implode(', ', $lengths) . ' o ' . $last;
    }
}
