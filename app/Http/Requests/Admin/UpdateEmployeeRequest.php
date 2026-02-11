<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $empleadoId = $this->route('empleado')->id ?? null;

        return [
            'name'     => ['required','string','max:120'],
            'email'    => [
                'required','email','max:190',
                Rule::unique('users','email')->ignore($empleadoId),
            ],
            'password' => ['nullable','string','min:8'],
            'roles'    => ['required','array','min:1'],
            'roles.*'  => ['integer','exists:roles,id'],
        ];
    }
}
