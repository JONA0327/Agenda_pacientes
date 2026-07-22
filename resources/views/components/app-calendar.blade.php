@props(['visitDates' => []])

<div x-data="calendarData({{ json_encode($visitDates) }})" class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

    {{-- Header: navegación de mes --}}
    <div class="flex items-center justify-between px-3 sm:px-5 py-3 sm:py-4 border-b border-slate-100">
        <button @click="prev()"
                class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors duration-150"
                aria-label="Mes anterior">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <h3 class="text-base font-semibold text-slate-800" x-text="title"></h3>
        <button @click="next()"
                class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors duration-150"
                aria-label="Mes siguiente">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    {{-- Cabeceras días semana --}}
    <div class="grid grid-cols-7 border-b border-slate-100">
        <template x-for="d in weekDays" :key="d">
            <div class="py-1.5 sm:py-2.5 text-center text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-50"
                 x-text="d"></div>
        </template>
    </div>

    {{-- Grilla de días --}}
    <div class="grid grid-cols-7">
        <template x-for="(cell, i) in cells" :key="i">
            <div @click="pick(cell)"
                 :class="{
                     'text-slate-300 cursor-default': !cell.cur,
                     'cursor-pointer': cell.cur,
                     'text-cyan-600 font-bold': isToday(cell) && !isSelected(cell),
                     'hover:bg-cyan-50': cell.cur && !isSelected(cell),
                 }"
                 class="relative flex flex-col items-center justify-center text-xs sm:text-sm text-slate-700
                        border-t border-slate-50 transition-colors duration-100 select-none min-h-[38px] sm:min-h-[42px]">

                {{-- Número del día --}}
                <span x-text="cell.n"
                      :class="isSelected(cell)
                          ? 'w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center rounded-full bg-cyan-600 text-white text-xs sm:text-sm'
                          : ''">
                </span>

                {{-- Punto de visita --}}
                <span x-show="hasVisit(cell) && !isSelected(cell)"
                      class="absolute bottom-1 w-1.5 h-1.5 rounded-full bg-emerald-400"></span>

                {{-- Punto en día seleccionado con visita (blanco) --}}
                <span x-show="hasVisit(cell) && isSelected(cell)"
                      class="absolute bottom-1 w-1.5 h-1.5 rounded-full bg-white/80"></span>
            </div>
        </template>
    </div>

    {{-- Leyenda --}}
    <div class="px-3 sm:px-5 py-2.5 sm:py-3 border-t border-slate-100 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[10px] sm:text-xs text-slate-400">
        <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-cyan-600 inline-block"></span>
            Hoy / Seleccionado
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span>
            Con visitas
        </span>
    </div>
</div>
