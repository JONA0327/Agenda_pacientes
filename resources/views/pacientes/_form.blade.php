{{-- Partial: formulario de paciente (create y edit) --}}
<div class="space-y-5">

    {{-- Nombre --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Nombre Completo <span class="text-red-500">*</span>
        </label>
        <input type="text" name="nombre" value="{{ old('nombre', $paciente->nombre ?? '') }}"
               required maxlength="255"
               placeholder="Ej. María de Jesús Avila Andrade"
               class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2.5 px-4 transition-all">
        @error('nombre')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Sección + Categoría --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Sección</label>
            <input type="text" name="seccion" value="{{ old('seccion', $paciente->seccion ?? '') }}"
                   maxlength="50" placeholder="Ej. 1052"
                   class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2.5 px-4 transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
            <input type="text" name="categoria" value="{{ old('categoria', $paciente->categoria ?? '') }}"
                   maxlength="100" placeholder="Ej. PAM"
                   class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2.5 px-4 transition-all">
        </div>
    </div>

    {{-- Calle + Número --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Calle</label>
            <input type="text" name="calle" value="{{ old('calle', $paciente->calle ?? '') }}"
                   maxlength="255" placeholder="Ej. Av. Constitución"
                   class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2.5 px-4 transition-all">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Número</label>
            <input type="text" name="numero" value="{{ old('numero', $paciente->numero ?? '') }}"
                   maxlength="20" placeholder="Ej. 2723"
                   class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2.5 px-4 transition-all">
        </div>
    </div>

    {{-- Teléfono --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono</label>
        <input type="text" name="telefono" value="{{ old('telefono', $paciente->telefono ?? '') }}"
               maxlength="100" placeholder="Ej. 444-123-4567"
               class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2.5 px-4 transition-all">
    </div>

    {{-- Enfermedad crónica --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Enfermedad Crónica</label>
        <textarea name="enfermedad_cronica" maxlength="500" rows="2"
                  placeholder="Ej. HTA, DM, AR..."
                  class="w-full rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2.5 px-4 transition-all resize-none">{{ old('enfermedad_cronica', $paciente->enfermedad_cronica ?? '') }}</textarea>
    </div>

    {{-- Toggle medicamentos --}}
    <div x-data="{ med: {{ (old('requiere_medicamentos', ($paciente->requiere_medicamentos ?? false))) ? 'true' : 'false' }} }">
        <label class="block text-sm font-medium text-slate-700 mb-2">Requiere Medicamentos</label>
        <div class="flex items-center gap-3">
            <button type="button" @click="med = !med"
                    :class="med ? 'bg-cyan-600' : 'bg-slate-300'"
                    class="relative inline-flex h-6 w-11 shrink-0 rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                <span :class="med ? 'translate-x-5' : 'translate-x-0.5'"
                      class="inline-block h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm ring-0 transition-transform duration-200 ease-in-out"></span>
            </button>
            <span class="text-sm" :class="med ? 'text-cyan-700 font-medium' : 'text-slate-500'"
                  x-text="med ? 'Sí, requiere medicamentos' : 'No requiere medicamentos'"></span>
            <input type="hidden" name="requiere_medicamentos" :value="med ? 1 : 0">
        </div>
    </div>

    {{-- Status (solo en edición) --}}
    @if(isset($paciente))
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Estado del paciente</label>
        <select name="status"
                class="w-full sm:w-auto rounded-xl border-slate-200 shadow-sm focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 text-sm py-2.5 px-4 transition-all">
            <option value="efectivo"      {{ ($paciente->status ?? 'efectivo') === 'efectivo'      ? 'selected' : '' }}>Efectivo</option>
            <option value="rechazo"       {{ ($paciente->status ?? '') === 'rechazo'       ? 'selected' : '' }}>Rechazo</option>
            <option value="no_encontrado" {{ ($paciente->status ?? '') === 'no_encontrado' ? 'selected' : '' }}>No Encontrado</option>
        </select>
    </div>
    @endif

</div>
