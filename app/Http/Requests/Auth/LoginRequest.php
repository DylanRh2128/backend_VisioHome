<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'correo' => 'required|email|regex:/^\S*$/',
            'password' => 'required|string|regex:/^\S*$/',
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El formato del correo es inválido.',
            'correo.regex' => 'El correo no puede contener espacios.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.regex' => 'La contraseña no puede contener espacios.',
        ];
    }
}
