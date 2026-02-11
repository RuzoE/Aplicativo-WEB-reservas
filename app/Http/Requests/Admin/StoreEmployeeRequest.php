<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Solo el admin puede crear empleados.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('admin');
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
            'email'                 => ['required','email','max:255','unique:users,email'],
            'phone'                 => ['nullable','string','max:30'],

            // Si tu modelo User tiene cast 'password' => 'hashed',
            // no necesitas Hash::make en el controlador.
            'password'              => ['required','string','min:8','confirmed'],

            // Rol por nombre (ej: admin, recepcionista, minibar)
            'role'                  => ['required','string','exists:roles,name'],
        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role.exists'        => 'El rol seleccionado no existe.',
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
            'role'         => 'rol',
        ];
    }
}
