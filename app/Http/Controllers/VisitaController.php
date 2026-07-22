<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitaRequest;
use App\Models\Paciente;
use App\Models\Visita;

class VisitaController extends Controller
{
    public function index(Paciente $paciente)
    {
        $pendientes = $paciente->visitasPendientes()->get();
        $historial  = $paciente->historial()->get();

        return view('pacientes.visitas', compact('paciente', 'pendientes', 'historial'));
    }

    public function store(VisitaRequest $request, Paciente $paciente)
    {
        $data = $request->validated();

        $data['source']    = 'manual';
        $data['confirmed'] = true;
        // Status vacío/null = "en_curso" neutro; effectiveStatus() calculará programada/en_curso
        $data['status'] = $data['status'] ?: 'en_curso';

        if (empty($data['proxima_visita']) && !empty($data['fecha_hora'])) {
            $data['proxima_visita'] = date('Y-m-d', strtotime($data['fecha_hora'] . ' +2 months'));
        }

        $paciente->visitas()->create($data);
        return back()->with('success', 'Visita agregada al historial.');
    }

    public function update(VisitaRequest $request, Visita $visita)
    {
        $data = $request->validated();

        if (!$visita->confirmed) {
            $data['confirmed'] = true;
            $data['status']    = $data['status'] ?: 'en_curso';

            if (empty($data['proxima_visita']) && !empty($data['fecha_hora'])) {
                $data['proxima_visita'] = date('Y-m-d', strtotime($data['fecha_hora'] . ' +2 months'));
            }
        } else {
            // Visita ya confirmada: solo actualizar status si viene (puede ser vacío = volver a automático)
            if (array_key_exists('status', $data) && $data['status'] === '') {
                $data['status'] = 'en_curso'; // vacío = automático (neutral en DB)
            }
        }

        $visita->update($data);
        return back()->with('success', 'Visita actualizada.');
    }

    public function destroy(Visita $visita)
    {
        $visita->delete();
        return back()->with('success', 'Visita eliminada.');
    }
}
