<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_hora'     => 'nullable|date',
            'proxima_visita' => 'nullable|date',
            'notas'          => 'nullable|string|max:1000',
            'status' => 'nullable|in:completado,cancelado,pospuesto,en_curso',
        ];
    }
}
