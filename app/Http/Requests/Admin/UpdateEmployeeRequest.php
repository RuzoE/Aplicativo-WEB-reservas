<?php

namespace App\Http\Requests\Admin;

use App\Rules\AllowedEmailDomain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('administrador') ?? false;
    }

    public function rules(): array
    {
        $empleadoId = $this->route('empleado')->id ?? null;

        return [
            'name'     => ['required','string','max:120'],
            'email'    => [
                'required','email','max:190',
                new AllowedEmailDomain(),
                Rule::unique('users','email')->ignore($empleadoId),
            ],
            'password' => ['nullable', Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            'roles'    => ['required','array','min:1'],
            'roles.*'  => ['integer','exists:roles,id'],
        ];
    }
}
