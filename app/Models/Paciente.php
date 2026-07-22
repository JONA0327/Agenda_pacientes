<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    protected $fillable = [
        'nombre', 'seccion', 'categoria', 'calle', 'numero',
        'telefono', 'enfermedad_cronica', 'requiere_medicamentos', 'status',
    ];

    protected $casts = [
        'requiere_medicamentos' => 'boolean',
    ];

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class)->orderBy('fecha_hora');
    }

    public function visitasPendientes(): HasMany
    {
        return $this->hasMany(Visita::class)->where('confirmed', false)->orderBy('fecha_hora');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(Visita::class)->where('confirmed', true)->orderBy('fecha_hora');
    }
}
