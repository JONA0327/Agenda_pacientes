<x-app-layout>

{{-- Modal: Confirmación de borrado --}}
<div x-data="{
        show: false,
        input: '',
        url: '',
        openModal(url) { this.show = true; this.url = url; this.input = ''; },
     }"
     @keydown.escape.window="show = false">

    {{-- Backdrop --}}
    <div x-show="show" x-cloak
         @click="show = false"
         class="fixed inset-0 bg-slate-900/50 z-40 transition-opacity"></div>

    {{-- Modal box --}}
    <div x-show="show" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click.stop
             class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Eliminar paciente</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Esta acción no se puede deshacer.</p>
                </div>
            </div>
            <p class="text-sm text-slate-600 mb-4">
                Escribe <strong class="text-red-600 font-semibold">confirmar</strong> para eliminar el paciente y todas sus visitas.
            </p>
            <input x-model="input" type="text" placeholder="confirmar"
                   class="w-full rounded-xl border-slate-200 shadow-sm focus:border-red-400 focus:ring-2 focus:ring-red-400/20 text-sm py-2.5 px-4 mb-4 transition-all">
            <form :action="url" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="submit"
                        :disabled="input !== 'confirmar'"
                        :class="input === 'confirmar'
                            ? 'bg-red-600 hover:bg-red-700 text-white cursor-pointer'
                            : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all">
                    Eliminar
                </button>
                <button type="button" @click="show = false"
                        class="flex-1 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-sm font-medium hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
            </form>
        </div>
    </div>

{{-- Contenido principal --}}
<div class="max-w-screen-xl mx-auto px-3 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Pacientes</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Gestión de pacientes por sección</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('pacientes.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-600 to-teal-600
                      text-white text-sm font-semibold rounded-xl hover:from-cyan-700 hover:to-teal-700
                      shadow-sm shadow-cyan-500/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Paciente
            </a>

            {{-- Importar Excel --}}
            <form method="POST" action="{{ route('pacientes.import') }}" enctype="multipart/form-data">
                @csrf
                <label class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold
                              rounded-xl hover:bg-emerald-700 shadow-sm shadow-emerald-500/25 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Importar Excel
                    <input type="file" name="archivo" accept=".xlsx,.xls" class="hidden"
                           onchange="this.form.submit()">
                </label>
            </form>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-700 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
            @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-5 border-b border-slate-200">
        @foreach(['efectivo' => 'Efectivos', 'rechazo' => 'Rechazo', 'no_encontrado' => 'No Encontrados'] as $key => $label)
            <a href="{{ route('pacientes.index', ['tab' => $key]) }}"
               class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all
                      {{ $tab === $key
                          ? 'border-cyan-600 text-cyan-700 bg-cyan-50/50'
                          : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                {{ $label }}
                <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-bold rounded-full
                             {{ $tab === $key ? 'bg-cyan-100 text-cyan-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $counts[$key] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Tabla de pacientes --}}
    @if($pacientes->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-slate-400 text-sm">No hay pacientes en esta categoría.</p>
            <a href="{{ route('pacientes.create') }}" class="mt-3 inline-block text-sm text-cyan-600 hover:text-cyan-700 font-medium">
                Agregar el primero
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nombre</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Sección</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Teléfono</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Enfermedad</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Medicamentos</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($pacientes as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800">{{ $p->nombre }}</div>
                                @if($p->calle)
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $p->calle }} {{ $p->numero }}</div>
                                @endif
                                {{-- Info visible solo en mobile --}}
                                <div class="sm:hidden text-xs text-slate-400 mt-0.5">Sec. {{ $p->seccion }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 hidden sm:table-cell">{{ $p->seccion ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 hidden md:table-cell">{{ $p->telefono ?? '—' }}</td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                @if($p->enfermedad_cronica)
                                    <span class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full">
                                        {{ Str::limit($p->enfermedad_cronica, 30) }}
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center hidden md:table-cell">
                                @if($p->requiere_medicamentos)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-cyan-50 text-cyan-700 border border-cyan-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>Sí
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-50 text-slate-400 border border-slate-200">
                                        No
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    {{-- Visitas --}}
                                    <a href="{{ route('pacientes.visitas.index', $p) }}"
                                       title="Ver visitas"
                                       class="p-2 rounded-lg text-slate-400 hover:text-cyan-600 hover:bg-cyan-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </a>
                                    {{-- Editar --}}
                                    <a href="{{ route('pacientes.edit', $p) }}"
                                       title="Editar"
                                       class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    {{-- Borrar --}}
                                    <button @click="openModal('{{ route('pacientes.destroy', $p) }}')"
                                            title="Eliminar"
                                            class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
</div>{{-- cierre x-data del modal --}}
</x-app-layout>
