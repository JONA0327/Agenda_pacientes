<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'               => 'required|string|max:255',
            'seccion'              => 'nullable|string|max:50',
            'categoria'            => 'nullable|string|max:100',
            'calle'                => 'nullable|string|max:255',
            'numero'               => 'nullable|string|max:20',
            'telefono'             => 'nullable|string|max:100',
            'enfermedad_cronica'   => 'nullable|string|max:500',
            'requiere_medicamentos'=> 'nullable|boolean',
            'status'               => 'nullable|in:efectivo,rechazo,no_encontrado',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre completo es obligatorio.',
        ];
    }
}
