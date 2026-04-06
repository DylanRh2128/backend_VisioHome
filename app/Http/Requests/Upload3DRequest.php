<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Upload3DRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware handles this
    }

    public function rules(): array
    {
        return [
            'modelo_3d' => [
                'required',
                'file',
                'max:20480', // 20MB
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    if (!in_array($extension, ['glb', 'gltf'])) {
                        $fail('Solo se admiten modelos en formato .glb o .gltf. Se recomienda usar .glb por ser un formato binario autocontenido.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'modelo_3d.required' => 'Debes seleccionar un archivo.',
            'modelo_3d.file' => 'Debes subir un archivo válido.',
            'modelo_3d.max' => 'El tamaño máximo permitido es de 20MB.',
        ];
    }
}
