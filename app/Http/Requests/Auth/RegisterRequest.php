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
            'nombre' => 'required|string|min:3|max:120',
            'correo' => [
                'required',
                'email',
                'max:180',
                'unique:usuarios,correo',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/',
                'regex:/^\S*$/'
            ],
            'telefono' => 'required|string|size:10|regex:/^[0-9]{10}$/',
            'genero' => 'required|in:Masculino,Femenino,prefiero_no_decirlo',
            'departamento' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'direccion' => 'nullable|string|max:200',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:15',
                'regex:/^\S*$/',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'docUsuario.required' => 'el documento de usuario es obligatorio.',
            'docUsuario.unique' => 'El documento de usuario ya está registrado.',
            'docUsuario.regex' => 'El documento de usuario no puede contener espacios.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'correo.required' => 'El campo correo es obligatorio.',
            'correo.unique' => 'El correo ya está registrado.',
            'correo.regex' => 'El correo debe terminar en .com y no contener espacios.',
            'telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
            'telefono.regex' => 'El teléfono solo puede contener 10 números.',
            'genero.required' => 'El género es obligatorio.',
            'genero.in' => 'El género seleccionado no es válido (Debe ser: Masculino, Femenino o prefiero_no_decirlo).',
            'departamento.required' => 'El departamento es obligatorio.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña debe tener máximo 15 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número, un carácter especial y no tener espacios.',
        ];
    }
}
