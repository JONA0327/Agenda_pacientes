<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visita extends Model
{
    protected $fillable = [
        'paciente_id', 'fecha_hora', 'proxima_visita',
        'notas', 'status', 'source', 'confirmed',
    ];

    protected $casts = [
        'fecha_hora'     => 'datetime',
        'proxima_visita' => 'date',
        'confirmed'      => 'boolean',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Status real para mostrar en la UI.
     * - completado / cancelado / pospuesto → manual (el usuario lo fijó)
     * - cualquier otro (en_curso, pendiente en DB)  → se calcula por tiempo:
     *     fecha_hora > ahora → programada
     *     fecha_hora ≤ ahora → en_curso
     */
    public function effectiveStatus(): string
    {
        if (in_array($this->status, ['completado', 'cancelado', 'pospuesto'])) {
            return $this->status;
        }

        if (!$this->confirmed || !$this->fecha_hora) {
            return 'pendiente';
        }

        return $this->fecha_hora->gt(now()) ? 'programada' : 'en_curso';
    }

    public function statusLabel(?string $status = null): string
    {
        return match($status ?? $this->effectiveStatus()) {
            'programada' => 'Programada',
            'en_curso'   => 'En Curso',
            'completado' => 'Completado',
            'pospuesto'  => 'Pospuesto',
            'cancelado'  => 'Cancelado',
            'pendiente'  => 'Pendiente',
            default      => ucfirst($status ?? $this->status),
        };
    }
}
