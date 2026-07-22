<?php

namespace App\Http\Controllers;

use App\Http\Requests\PacienteRequest;
use App\Models\Paciente;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlDate;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'efectivo');
        $status = in_array($tab, ['efectivo', 'rechazo', 'no_encontrado']) ? $tab : 'efectivo';

        $pacientes = Paciente::where('status', $status)
            ->orderBy('nombre')
            ->get();

        $counts = [
            'efectivo'      => Paciente::where('status', 'efectivo')->count(),
            'rechazo'       => Paciente::where('status', 'rechazo')->count(),
            'no_encontrado' => Paciente::where('status', 'no_encontrado')->count(),
        ];

        return view('pacientes.index', compact('pacientes', 'tab', 'counts'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(PacienteRequest $request)
    {
        Paciente::create([
            ...$request->validated(),
            'requiere_medicamentos' => $request->boolean('requiere_medicamentos'),
            'status'                => $request->input('status', 'efectivo'),
        ]);

        return redirect()->route('pacientes.index')->with('success', 'Paciente registrado correctamente.');
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(PacienteRequest $request, Paciente $paciente)
    {
        $paciente->update([
            ...$request->validated(),
            'requiere_medicamentos' => $request->boolean('requiere_medicamentos'),
        ]);

        return redirect()->route('pacientes.index', ['tab' => $paciente->status])
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();
        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado.');
    }

    /** Importar desde Excel (hoja EFECTIVOS) */
    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|max:10240',
        ]);

        $path = $request->file('archivo')->getRealPath();

        try {
            $spreadsheet = IOFactory::load($path);
        } catch (\Exception $e) {
            return back()->withErrors(['archivo' => 'El archivo no es un Excel válido.']);
        }

        // Buscar la hoja EFECTIVOS
        $sheet = null;
        foreach ($spreadsheet->getSheetNames() as $i => $name) {
            if (stripos($name, 'EFECTIVO') !== false) {
                $sheet = $spreadsheet->getSheet($i);
                break;
            }
        }

        if (!$sheet) {
            return back()->withErrors(['archivo' => 'No se encontró la hoja EFECTIVOS en el archivo.']);
        }

        $imported = 0;
        $skipped  = 0;

        foreach ($sheet->getRowIterator(3) as $row) { // filas desde 3 (skip 2 headers)
            $cells = $row->getCellIterator('A', 'P');
            $cells->setIterateOnlyExistingCells(false);
            $data = [];
            foreach ($cells as $cell) {
                $data[] = $cell->getValue();
            }

            // Col B=1 (index 1): nombre
            $nombre = trim((string)($data[1] ?? ''));
            if (empty($nombre) || strtoupper($nombre) === 'PACIENTE') {
                continue;
            }

            // Medicamentos: col I=8 tiene "SI", col J=9 tiene "NO"
            $medSi = !empty(trim((string)($data[8] ?? '')));

            $paciente = Paciente::firstOrCreate(
                ['nombre' => $nombre],
                [
                    'seccion'               => trim((string)($data[2] ?? '')) ?: null,
                    'categoria'             => trim((string)($data[3] ?? '')) ?: null,
                    'calle'                 => trim((string)($data[4] ?? '')) ?: null,
                    'numero'                => trim((string)($data[5] ?? '')) ?: null,
                    'telefono'              => trim((string)($data[6] ?? '')) ?: null,
                    'enfermedad_cronica'    => trim((string)($data[7] ?? '')) ?: null,
                    'requiere_medicamentos' => $medSi,
                    'status'                => 'efectivo',
                ]
            );

            if ($paciente->wasRecentlyCreated) {
                $imported++;
            } else {
                $skipped++;
            }

            // Visitas: cols K-P (índices 10-15)
            $cells2 = $row->getCellIterator('K', 'P');
            $cells2->setIterateOnlyExistingCells(false);
            foreach ($cells2 as $cell) {
                $val = $cell->getCalculatedValue();
                if (empty($val)) continue;

                $fecha = null;
                if (is_numeric($val)) {
                    try {
                        $fecha = XlDate::excelToDateTimeObject($val);
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                if (!$fecha) continue;

                // Solo crear si no existe ya una visita en esa fecha para este paciente
                $fechaStr = $fecha->format('Y-m-d H:i:s');
                $exists = $paciente->visitas()
                    ->whereDate('fecha_hora', $fecha->format('Y-m-d'))
                    ->exists();

                if (!$exists) {
                    $paciente->visitas()->create([
                        'fecha_hora'     => $fechaStr,
                        'proxima_visita' => (clone $fecha)->modify('+2 months')->format('Y-m-d'),
                        'source'         => 'excel',
                        'status'         => 'pendiente',
                        'confirmed'      => false,
                    ]);
                }
            }
        }

        $msg = "Importación completa: {$imported} pacientes nuevos, {$skipped} ya existían.";
        return redirect()->route('pacientes.index')->with('success', $msg);
    }
}
