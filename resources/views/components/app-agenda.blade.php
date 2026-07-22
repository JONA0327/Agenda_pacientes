@props(['allVisits' => []])

<div x-data="agendaData({{ json_encode($allVisits) }})"
     @date-selected.window="selectWeek($event.detail)"
     class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-3 sm:px-5 py-3 sm:py-4 border-b border-slate-100 shrink-0">
        <h3 class="text-sm sm:text-base font-semibold text-slate-800 truncate">Agenda Semanal</h3>
        <span class="text-[10px] sm:text-xs text-slate-400 font-medium shrink-0 ml-2" x-text="weekRange"></span>
    </div>

    {{-- Filtros --}}
    <div class="px-3 sm:px-5 py-2.5 sm:py-3 border-b border-slate-100 bg-slate-50/60 shrink-0">
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">

            {{-- Filtro turno --}}
            <div class="flex gap-1">
                @foreach(['all' => 'Todas', 'morning' => 'Mañana', 'afternoon' => 'Tarde'] as $val => $label)
                <button @click="filter = '{{ $val }}'"
                        :class="filter === '{{ $val }}'
                            ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-500/30'
                            : 'bg-white text-slate-600 border border-slate-200 hover:border-cyan-300 hover:text-cyan-700'"
                        class="px-2 sm:px-3 py-1.5 rounded-lg text-[10px] sm:text-xs font-medium transition-all duration-150">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Filtro día --}}
            <select x-model="dayFilter"
                    class="text-xs sm:text-sm border-slate-200 rounded-lg py-1.5 pl-2 sm:pl-3 pr-6 sm:pr-8
                           focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20
                           text-slate-600 bg-white transition-all max-w-[130px] sm:max-w-none">
                <option value="all">Todos los días</option>
                <template x-for="(day, i) in weekDays" :key="i">
                    <option :value="i" x-text="day.name"></option>
                </template>
            </select>
        </div>
    </div>

    {{-- Columnas de días --}}
    <div class="overflow-auto grow" style="max-height: 52vh; min-height: 280px;">
        <div class="flex h-full" style="min-width: 380px;">

            <template x-for="(day, dayIdx) in weekDays" :key="dayIdx">
                <div x-show="showDay(dayIdx)"
                     class="flex flex-col border-r border-slate-100 last:border-r-0"
                     style="flex: 1; min-width: 0;">

                    {{-- Cabecera del día --}}
                    <div :class="day.isToday ? 'bg-cyan-50' : 'bg-slate-50'"
                         class="text-center py-2 sm:py-3 border-b border-slate-100 sticky top-0 z-10 shrink-0">
                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider"
                           :class="day.isToday ? 'text-cyan-600' : 'text-slate-500'"
                           x-text="day.name"></p>
                        <p class="text-[10px] sm:text-xs mt-0.5"
                           :class="day.isToday ? 'text-cyan-500 font-medium' : 'text-slate-400'"
                           x-text="day.date"></p>
                    </div>

                    {{-- Visitas del día --}}
                    <div class="p-1 space-y-1 flex-1">

                        {{-- Sin visitas --}}
                        <template x-if="visitsForDay(dayIdx).length === 0">
                            <div class="flex items-center justify-center py-4">
                                <span class="text-[10px] text-slate-300">Sin visitas</span>
                            </div>
                        </template>

                        {{-- Tarjetas de visita --}}
                        <template x-for="(v, vi) in visitsForDay(dayIdx)" :key="vi">
                            <a :href="v.url ?? '#'"
                               :title="v.url ? 'Ver visitas de ' + v.name : ''"
                               class="block rounded-lg border-l-4 px-2 py-1.5 transition-opacity duration-150"
                               :class="[statusClasses(v.status), v.url ? 'cursor-pointer hover:opacity-80' : 'cursor-default']">

                                {{-- Hora --}}
                                <p class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-500"
                                   x-text="v.time !== '00:00' ? v.time : '—'"></p>

                                {{-- Nombre paciente --}}
                                <p class="text-[11px] sm:text-xs font-semibold text-slate-800 truncate leading-tight mt-0.5"
                                   x-text="v.name"></p>

                                {{-- Enfermedad / notas --}}
                                <p x-show="v.reason"
                                   class="text-[9px] sm:text-[10px] text-slate-400 truncate hidden sm:block"
                                   x-text="v.reason"></p>

                                {{-- Badge status --}}
                                <span class="inline-block mt-1 text-[8px] sm:text-[9px] px-1.5 py-0.5 rounded-full font-semibold"
                                      :class="statusBadge(v.status)"
                                      x-text="statusLabel(v.status)"></span>
                            </a>
                        </template>

                    </div>
                </div>
            </template>

        </div>
    </div>
</div>
