<x-app-layout>
    <div class="max-w-screen-xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6">

        {{-- Bienvenida --}}
        <div class="mb-4 sm:mb-5">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Panel de Control</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 sm:mt-1">Gestiona tus citas y pacientes desde aquí</p>
        </div>

        {{-- Stats reales --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mb-5 sm:mb-6">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $citasHoy }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Citas Hoy</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cyan-100 flex items-center justify-center text-cyan-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $totalPacientes }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Pacientes</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-slate-800">{{ $pendientes }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Pendientes</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Calendario + Agenda --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 items-start">
            <div class="lg:col-span-5">
                <x-app-calendar :visit-dates="$visitDates" />
            </div>
            <div class="lg:col-span-7">
                <x-app-agenda :all-visits="$allVisits" />
            </div>
        </div>
    </div>
</x-app-layout>
