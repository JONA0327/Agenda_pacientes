import './bootstrap';
import Alpine from 'alpinejs';
import { initSidebar }    from './sidebar';
import { registerCalendar } from './calendar';
import { registerAgenda }   from './agenda';

window.Alpine = Alpine;

// Alpine.data debe registrarse ANTES de Alpine.start()
registerCalendar(Alpine);
registerAgenda(Alpine);

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
});
