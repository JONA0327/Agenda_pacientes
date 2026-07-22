export function registerCalendar(Alpine) {
    Alpine.data('calendarData', (visitDates = []) => ({
        year:       new Date().getFullYear(),
        month:      new Date().getMonth(),
        selected:   new Date().getDate(),
        visitDates,
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        weekDays:   ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],

        get title() {
            return `${this.monthNames[this.month]} ${this.year}`;
        },

        get cells() {
            const firstDay  = new Date(this.year, this.month, 1).getDay();
            const daysInMon = new Date(this.year, this.month + 1, 0).getDate();
            const prevDays  = new Date(this.year, this.month, 0).getDate();
            const cells = [];
            for (let i = firstDay - 1; i >= 0; i--)
                cells.push({ n: prevDays - i, cur: false });
            for (let d = 1; d <= daysInMon; d++)
                cells.push({ n: d, cur: true });
            while (cells.length < 42)
                cells.push({ n: cells.length - firstDay - daysInMon + 1, cur: false });
            return cells;
        },

        isToday(cell) {
            if (!cell.cur) return false;
            const t = new Date();
            return cell.n === t.getDate()
                && this.month === t.getMonth()
                && this.year  === t.getFullYear();
        },

        isSelected(cell) {
            return cell.cur && cell.n === this.selected;
        },

        hasVisit(cell) {
            if (!cell.cur) return false;
            const d = `${this.year}-${String(this.month + 1).padStart(2,'0')}-${String(cell.n).padStart(2,'0')}`;
            return this.visitDates.includes(d);
        },

        prev() {
            this.month === 0 ? (this.month = 11, this.year--) : this.month--;
            this.selected = null;
        },

        next() {
            this.month === 11 ? (this.month = 0, this.year++) : this.month++;
            this.selected = null;
        },

        pick(cell) {
            if (!cell.cur) return;
            this.selected = cell.n;
            // Notificar a la agenda qué fecha fue seleccionada
            this.$dispatch('date-selected', {
                year:  this.year,
                month: this.month,
                day:   cell.n,
            });
        },
    }));
}
