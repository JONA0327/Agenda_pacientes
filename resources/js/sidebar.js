const LS_KEY = 'sidebar_open';

export function initSidebar() {
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('sidebarOverlay');
  const closeBtn = document.getElementById('sidebarClose');
  const mainContent = document.getElementById('mainContent');
  let isOpen = false;
  const isLg = () => window.innerWidth >= 1024;

  function open(persist = true) {
    isOpen = true;
    sidebar?.classList.remove('collapsed');
    overlay?.classList.remove('hidden');
    toggleBtn?.classList.add('shifted');
    if (isLg()) {
      mainContent?.classList.add('shifted');
      if (persist) localStorage.setItem(LS_KEY, '1');
    }
  }

  function close(persist = true) {
    isOpen = false;
    sidebar?.classList.add('collapsed');
    overlay?.classList.add('hidden');
    toggleBtn?.classList.remove('shifted');
    mainContent?.classList.remove('shifted');
    if (persist) localStorage.setItem(LS_KEY, '0');
  }

  toggleBtn?.addEventListener('click', () => isOpen ? close() : open());
  closeBtn?.addEventListener('click', close);
  overlay?.addEventListener('click', close);

  window.addEventListener('resize', () => {
    if (isLg()) {
      if (!isOpen) open(false);
    } else {
      if (isOpen) close(false);
    }
  });

  // En desktop: respetar preferencia guardada (default: abierta la primera vez)
  if (isLg()) {
    const saved = localStorage.getItem(LS_KEY);
    if (saved !== '0') open(false);
  }
}
