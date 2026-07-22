<x-app-layout>

@php
$statusColors = [
    'programada' => ['bg' => 'bg-violet-50',  'border' => 'border-violet-400', 'text' => 'text-violet-700', 'badge' => 'bg-violet-100 text-violet-700'],
    'completado' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-400','text' => 'text-emerald-700','badge' => 'bg-emerald-100 text-emerald-700'],
    'pospuesto'  => ['bg' => 'bg-amber-50',   'border' => 'border-amber-400',  'text' => 'text-amber-700',  'badge' => 'bg-amber-100 text-amber-700'],
    'en_curso'   => ['bg' => 'bg-blue-50',    'border' => 'border-blue-400',   'text' => 'text-blue-700',   'badge' => 'bg-blue-100 text-blue-700'],
    'cancelado'  => ['bg' => 'bg-red-50',     'border' => 'border-red-400',    'text' => 'text-red-700',    'badge' => 'bg-red-100 text-red-700'],
    'pendiente'  => ['bg' => 'bg-slate-50',   'border' => 'border-slate-300',  'text' => 'text-slate-500',  'badge' => 'bg-slate-100 text-slate-500'],
];
@endphp

<div class="max-w-screen-lg mx-auto px-3 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pacientes.index', ['tab' => $paciente->status]) }}"
           class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800">Visitas</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $paciente->nombre }}</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-700 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Columna izquierda: Agregar visita + Pendientes --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Agregar nueva visita --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
                 x-data="{
                    fechaHora: '',
                    proxima: '',
                    calcProxima() {
                        if (!this.fechaHora) { this.proxima = ''; return; }
                        const d = new Date(this.fechaHora);
                        d.setMonth(d.getMonth() + 2);
                        this.proxima = d.toISOString().split('T')[0];
                    }
                 }">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/60">
                    <h3 class="text-sm font-semibold text-slate-700">Agregar Nueva Visita</h3>
                </div>
                <form method="POST" action="{{ route('pacientes.visitas.store', $paciente) }}" class="p-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Fecha y Hora</label>
                        <input type="datetime-local" name="fecha_hora" x-model="fechaHora"
                               @change="calcProxima()"
                               class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2 px-3 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Próxima Visita</label>
                        <input type="date" name="proxima_visita" x-model="proxima"
                               class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2 px-3 transition-all">
                        <p class="text-[10px] text-slate-400 mt-1">Auto: +2 meses desde fecha de visita</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Estado inicial (opcional)</label>
                        <select name="status"
                                class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2 px-3 transition-all">
                            <option value="">Automático (programada / en curso)</option>
                            <option value="completado">Completado</option>
                            <option value="pospuesto">Pospuesto</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Programada/En Curso se calculan por fecha y hora automáticamente.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Notas</label>
                        <textarea name="notas" rows="2" maxlength="1000" placeholder="Observaciones..."
                                  class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2 px-3 transition-all resize-none"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full py-2 bg-gradient-to-r from-cyan-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-cyan-700 hover:to-teal-700 shadow-sm shadow-cyan-500/20 transition-all">
                        Guardar Visita
                    </button>
                </form>
            </div>

            {{-- Visitas pendientes (del Excel) --}}
            @if($pendientes->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 bg-amber-50/60 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <h3 class="text-sm font-semibold text-amber-700">Pendientes ({{ $pendientes->count() }})</h3>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($pendientes as $v)
                    <div class="p-4" x-data="{
                            open: false,
                            fechaHora: '{{ $v->fecha_hora ? $v->fecha_hora->format('Y-m-d\TH:i') : '' }}',
                            proxima: '{{ $v->proxima_visita ? $v->proxima_visita->format('Y-m-d') : '' }}',
                            calcProxima() {
                                if (!this.fechaHora) { this.proxima = ''; return; }
                                const d = new Date(this.fechaHora);
                                d.setMonth(d.getMonth() + 2);
                                this.proxima = d.toISOString().split('T')[0];
                            }
                         }">
                        {{-- Info compacta --}}
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-xs font-semibold text-slate-600">
                                    {{ $v->fecha_hora ? $v->fecha_hora->format('d/m/Y H:i') : 'Sin fecha' }}
                                </p>
                                @if($v->proxima_visita)
                                    <p class="text-[10px] text-slate-400 mt-0.5">
                                        Próxima: {{ $v->proxima_visita->format('d/m/Y') }}
                                    </p>
                                @endif
                                <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded mt-1 inline-block">Excel</span>
                            </div>
                            <button type="button" @click="open = !open"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-cyan-600 hover:bg-cyan-50 transition-colors shrink-0">
                                <svg class="w-4 h-4" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Formulario de confirmación --}}
                        <div x-show="open" x-cloak class="mt-3 pt-3 border-t border-slate-100">
                            <form method="POST" action="{{ route('visitas.update', $v) }}" class="space-y-2">
                                @csrf @method('PUT')
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Fecha y Hora</label>
                                    <input type="datetime-local" name="fecha_hora" x-model="fechaHora"
                                           @change="calcProxima()"
                                           class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Próxima Visita</label>
                                    <input type="date" name="proxima_visita" x-model="proxima"
                                           class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Estado (manual)</label>
                                    <select name="status"
                                            class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 transition-all">
                                        <option value="">Automático</option>
                                        <option value="completado">Completado</option>
                                        <option value="pospuesto">Pospuesto</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-500 mb-1">Notas</label>
                                    <textarea name="notas" rows="2" maxlength="1000"
                                              class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 transition-all resize-none">{{ $v->notas }}</textarea>
                                </div>
                                <button type="submit"
                                        class="w-full py-1.5 bg-cyan-600 text-white text-xs font-semibold rounded-lg hover:bg-cyan-700 transition-colors">
                                    Confirmar y guardar al historial
                                </button>
                            </form>
                            <form method="POST" action="{{ route('visitas.destroy', $v) }}" class="mt-1">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full py-1.5 text-xs text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                    Eliminar esta visita pendiente
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Columna derecha: Historial --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/60">
                    <h3 class="text-sm font-semibold text-slate-700">
                        Historial de Visitas
                        @if($historial->isNotEmpty())
                            <span class="ml-2 text-[10px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded-full font-normal">
                                {{ $historial->count() }}
                            </span>
                        @endif
                    </h3>
                </div>

                @if($historial->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-slate-400 text-sm">Sin visitas en el historial aún.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-50">
                        @foreach($historial as $i => $v)
                        @php
                            $eff = $v->effectiveStatus();
                            $col = $statusColors[$eff] ?? $statusColors['pendiente'];
                            $isAuto = in_array($eff, ['programada', 'en_curso']);
                        @endphp
                        <div class="p-4 sm:p-5 {{ $col['bg'] }} border-l-4 {{ $col['border'] }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-bold text-slate-800">
                                            Visita {{ $i + 1 }}
                                        </span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $col['badge'] }}">
                                            {{ $v->statusLabel($eff) }}
                                        </span>
                                        @if($isAuto)
                                            <span class="text-[9px] text-slate-400 italic">automático</span>
                                        @endif
                                        @if($v->source === 'excel')
                                            <span class="text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full">Excel</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-700 mt-1 font-medium truncate">{{ $paciente->nombre }}</p>
                                    @if($v->fecha_hora)
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $v->fecha_hora->format('d/m/Y') }}
                                            <span class="text-slate-400">{{ $v->fecha_hora->format('H:i') }} h</span>
                                        </p>
                                    @endif
                                    @if($v->proxima_visita)
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            Próxima visita: <strong class="text-slate-600">{{ $v->proxima_visita->format('d/m/Y') }}</strong>
                                        </p>
                                    @endif
                                    @if($v->notas)
                                        <p class="text-xs text-slate-500 mt-1 italic">{{ $v->notas }}</p>
                                    @endif
                                </div>

                                {{-- Solo las 3 opciones manuales; programada/en_curso son automáticas --}}
                                <div class="shrink-0">
                                    <form method="POST" action="{{ route('visitas.update', $v) }}">
                                        @csrf @method('PUT')
                                        <select name="status" onchange="this.form.submit()"
                                                class="text-xs border border-slate-200 bg-white rounded-lg py-1 pl-2 pr-6 cursor-pointer {{ $col['text'] }} focus:ring-0 focus:border-slate-300">
                                            <option value="" {{ $isAuto ? 'selected' : '' }}>Automático</option>
                                            <option value="completado" {{ $v->status === 'completado' ? 'selected' : '' }}>Completado</option>
                                            <option value="pospuesto"  {{ $v->status === 'pospuesto'  ? 'selected' : '' }}>Pospuesto</option>
                                            <option value="cancelado"  {{ $v->status === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
                                        </select>
                                    </form>
                                    <form method="POST" action="{{ route('visitas.destroy', $v) }}" class="mt-1 text-right">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Eliminar visita"
                                                class="p-1 text-slate-300 hover:text-red-500 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>
