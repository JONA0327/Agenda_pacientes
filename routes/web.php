<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\VisitaController;
use App\Models\Paciente;
use App\Models\Visita;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $today = now();

    // Stats reales
    $citasHoy       = Visita::whereDate('fecha_hora', $today)->where('confirmed', true)->count();
    $totalPacientes = Paciente::count();
    $pendientes     = Visita::where('confirmed', false)->count();

    // Fechas con visitas confirmadas (mes anterior + 3 meses adelante para navegar el calendario)
    $visitDates = Visita::where('confirmed', true)
        ->whereNotNull('fecha_hora')
        ->whereBetween('fecha_hora', [
            $today->copy()->subMonth()->startOfMonth(),
            $today->copy()->addMonths(3)->endOfMonth(),
        ])
        ->pluck('fecha_hora')
        ->map(fn($d) => $d->format('Y-m-d'))
        ->unique()->values()->toArray();

    // Visitas para la agenda: rango amplio (±2 meses) con fecha absoluta
    // para que al navegar el calendario se filtre en el cliente sin AJAX
    // ponytail: rango de ±2 meses; añadir paginación AJAX si la cantidad crece
    $allVisits = Visita::with('paciente')
        ->whereNotNull('fecha_hora')
        ->whereBetween('fecha_hora', [
            $today->copy()->subMonths(1)->startOfWeek(),
            $today->copy()->addMonths(3)->endOfWeek(),
        ])
        ->orderBy('fecha_hora')
        ->get()
        ->map(fn($v) => [
            'date'      => $v->fecha_hora->format('Y-m-d'),
            'time'      => $v->fecha_hora->format('H:i'),
            'name'      => $v->paciente?->nombre ?? 'Paciente',
            'reason'    => $v->paciente?->enfermedad_cronica ?? ($v->notas ?? ''),
            'rawStatus' => $v->status,          // status guardado en DB
            'url'       => $v->paciente_id ? route('pacientes.visitas.index', $v->paciente_id) : null,
        ])
        ->values()->toArray();

    return view('dashboard.index', compact(
        'citasHoy', 'totalPacientes', 'pendientes', 'visitDates', 'allVisits'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pacientes
    Route::post('pacientes/import', [PacienteController::class, 'import'])->name('pacientes.import');
    Route::resource('pacientes', PacienteController::class)->except(['show']);

    // Visitas (anidadas bajo paciente + standalone para update/destroy)
    Route::get('pacientes/{paciente}/visitas', [VisitaController::class, 'index'])->name('pacientes.visitas.index');
    Route::post('pacientes/{paciente}/visitas', [VisitaController::class, 'store'])->name('pacientes.visitas.store');
    Route::put('visitas/{visita}', [VisitaController::class, 'update'])->name('visitas.update');
    Route::patch('visitas/{visita}', [VisitaController::class, 'update']);
    Route::delete('visitas/{visita}', [VisitaController::class, 'destroy'])->name('visitas.destroy');
});

require __DIR__.'/auth.php';
