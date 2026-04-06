<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'docUsuario' => 'required|string|max:20|unique:usuarios,docUsuario|regex:/^\S*$/',
            'nombre' => 'required|string|max:120',
            'correo' => 'required|email:rfc,dns|max:180|unique:usuarios,correo|regex:/^\S*$/',
            'telefono' => 'required|string|size:10|regex:/^[0-9]+$/',
            'direccion' => 'nullable|string|max:200',
            'password' => [
                'required',
                'string',
                'min:6',
                'max:15',
                'regex:/^\S*$/', // No internal spaces
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/', // Uppercase, lowercase, number, special char
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'docUsuario.required' => 'el documento de usuario es obligatorio.',
            'docUsuario.unique' => 'El documento de usuario ya está registrado.',
            'docUsuario.regex' => 'El documento de usuario no puede contener espacios.',
            'correo.required' => 'El campo correo es obligatorio.',
            'correo.unique' => 'El correo ya está registrado.',
            'correo.regex' => 'El correo no puede contener espacios.',
            'telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
            'telefono.regex' => 'El teléfono solo puede contener números.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.max' => 'La contraseña debe tener máximo 15 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número, un carácter especial y no tener espacios.',
        ];
    }
}
