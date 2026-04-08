<?php

namespace App\Http\Requests\Auth;

use App\Rules\AllowedEmailDomain;
use App\Rules\AlphaSpace;
use App\Rules\PhoneNumberByPrefix;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $country = strtolower(trim((string) $this->input('phone_country', '')));
        $phone = trim((string) $this->input('phone', ''));
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        $startsWithPlus = str_starts_with($phone, '+');
        $dialCode = (string) data_get(config('phone.country_lengths'), "{$country}.dial_code", '');

        $normalizedPhone = '';

        if ($digits !== '') {
            if ($startsWithPlus || ($dialCode !== '' && str_starts_with($digits, $dialCode))) {
                $normalizedPhone = '+' . $digits;
            } elseif ($dialCode !== '') {
                $normalizedPhone = '+' . $dialCode . $digits;
            } else {
                $normalizedPhone = $digits;
            }
        }

        $this->merge([
            'phone' => $normalizedPhone,
            'phone_country' => $country ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', new AlphaSpace, 'max:255'],
            'last_name' => ['required', new AlphaSpace, 'max:255'],
            'phone' => ['required', 'string', new PhoneNumberByPrefix($this->input('phone_country'))],
            'phone_country' => ['nullable', 'string', 'size:2'],
            'email' => ['required', 'string', 'email', 'max:255', new AllowedEmailDomain(), 'unique:users'],
            'password' => [
                'required',
                Password::min(12)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed',
            ],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ingresa tu nombre.',
            'last_name.required' => 'Ingresa tus apellidos.',
            'phone.required' => 'Ingresa tu número de teléfono.',
            'phone_country.size' => 'Selecciona un país válido para el teléfono.',
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email' => 'Escribe un correo válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.required' => 'Ingresa una contraseña segura.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password_confirmation.required' => 'Repite la contraseña para confirmar.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'last_name' => 'apellidos',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
        ];
    }
}
