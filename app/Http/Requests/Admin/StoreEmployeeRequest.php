<?php

namespace App\Http\Requests\Admin;

use App\Rules\AllowedEmailDomain;
use App\Rules\PhoneNumberByPrefix;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Solo el admin puede crear empleados.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('administrador');
    }

    /**
     * Reglas de validación para crear empleado.
     * - Usamos un único 'role' por nombre (Spatie) y guard web.
     * - La contraseña requiere confirmación (password_confirmation).
     */
    public function rules(): array
    {
        return [
            'name'                  => ['required','string','max:100'],
            'last_name'             => ['nullable','string','max:100'],
            'email'                 => ['required','email','max:255', new AllowedEmailDomain(), 'unique:users,email'],
            'phone'                 => ['nullable', new PhoneNumberByPrefix()],

            // Si tu modelo User tiene cast 'password' => 'hashed',
            // no necesitas Hash::make en el controlador.
            'password'              => [
                'required',
                'confirmed',
                Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised(),
            ],

            // Rol por id (ej: 1, 2, 3, 4)
            'role_id'               => ['required','integer','exists:roles,id'],
        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role_id.exists'      => 'El rol seleccionado no existe.',
        ];
    }

    /**
     * Alias legibles para errores.
     */
    public function attributes(): array
    {
        return [
            'name'         => 'nombre',
            'last_name'    => 'apellido',
            'email'        => 'correo electrónico',
            'phone'        => 'teléfono',
            'password'     => 'contraseña',
            'role_id'      => 'rol',
        ];
    }
}
