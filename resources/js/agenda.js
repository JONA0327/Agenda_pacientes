export function registerAgenda(Alpine) {
    Alpine.data('agendaData', (allVisits = []) => ({
        filter:         'all',
        dayFilter:      'all',
        allVisits,
        selectedMonday: null,

        init() {
            this.selectedMonday = this._monday(new Date());
        },

        selectWeek({ year, month, day }) {
            this.selectedMonday = this._monday(new Date(year, month, day));
            this.dayFilter = 'all';
        },

        _monday(date) {
            const d = new Date(date);
            d.setHours(0, 0, 0, 0);
            const dow = d.getDay();
            d.setDate(d.getDate() - (dow === 0 ? 6 : dow - 1));
            return d;
        },

        /**
         * Calcula el status real para mostrar:
         * - completado / cancelado / pospuesto → manual, se respeta
         * - cualquier otro → programada si es futuro, en_curso si ya llegó la hora
         */
        _effectiveStatus(v) {
            if (['completado', 'cancelado', 'pospuesto'].includes(v.rawStatus)) {
                return v.rawStatus;
            }
            const visitTime = new Date(`${v.date}T${v.time}`);
            return visitTime > new Date() ? 'programada' : 'en_curso';
        },

        get weekDays() {
            const monday = this.selectedMonday;
            if (!monday) return [];
            return ['Lunes','Martes','Miércoles','Jueves','Viernes'].map((name, i) => {
                const d = new Date(monday);
                d.setDate(monday.getDate() + i);
                const pad = n => String(n).padStart(2, '0');
                return {
                    name,
                    dateStr: `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`,
                    date:    `${pad(d.getDate())}/${pad(d.getMonth() + 1)}`,
                    isToday: d.toDateString() === new Date().toDateString(),
                };
            });
        },

        get weekRange() {
            const d = this.weekDays;
            return d.length ? `${d[0].date} – ${d[4].date}` : '';
        },

        visitsForDay(dayIdx) {
            const day = this.weekDays[dayIdx];
            if (!day) return [];
            return this.allVisits
                .filter(a => a.date === day.dateStr)
                .filter(a => {
                    const hr = parseInt(a.time.split(':')[0]);
                    if (this.filter === 'morning')   return hr < 12;
                    if (this.filter === 'afternoon') return hr >= 12;
                    return true;
                })
                .sort((a, b) => a.time.localeCompare(b.time))
                .map(a => ({ ...a, status: this._effectiveStatus(a) })); // status calculado
        },

        showDay(idx) {
            return this.dayFilter === 'all' || parseInt(this.dayFilter) === idx;
        },

        statusClasses(status) {
            const map = {
                programada:  'border-violet-400 bg-violet-50',
                en_curso:    'border-blue-400 bg-blue-50',
                completado:  'border-emerald-400 bg-emerald-50',
                pospuesto:   'border-amber-400 bg-amber-50',
                cancelado:   'border-red-400 bg-red-50',
                pendiente:   'border-slate-300 bg-slate-50',
            };
            return map[status] ?? map.pendiente;
        },

        statusBadge(status) {
            const map = {
                programada:  'bg-violet-100 text-violet-700',
                en_curso:    'bg-blue-100 text-blue-700',
                completado:  'bg-emerald-100 text-emerald-700',
                pospuesto:   'bg-amber-100 text-amber-700',
                cancelado:   'bg-red-100 text-red-700',
                pendiente:   'bg-slate-100 text-slate-500',
            };
            return map[status] ?? map.pendiente;
        },

        statusLabel(status) {
            const map = {
                programada:  'Programada',
                en_curso:    'En Curso',
                completado:  'Completado',
                pospuesto:   'Pospuesto',
                cancelado:   'Cancelado',
                pendiente:   'Pendiente',
            };
            return map[status] ?? status;
        },
    }));
}
