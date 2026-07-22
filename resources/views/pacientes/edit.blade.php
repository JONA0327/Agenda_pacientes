<x-app-layout>
<div class="max-w-2xl mx-auto px-3 sm:px-6 py-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pacientes.index', ['tab' => $paciente->status]) }}"
           class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-slate-800">Editar Paciente</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-8">
        <form method="POST" action="{{ route('pacientes.update', $paciente) }}">
            @csrf
            @method('PUT')
            @include('pacientes._form')
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-cyan-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-cyan-700 hover:to-teal-700 shadow-sm shadow-cyan-500/25 transition-all">
                    Guardar Cambios
                </button>
                <a href="{{ route('pacientes.index', ['tab' => $paciente->status]) }}"
                   class="px-5 py-2.5 bg-slate-100 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-200 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
