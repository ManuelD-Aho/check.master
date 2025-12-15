/**
 * CheckMaster - Sidebar Component
 * ================================
 * Gestion du sidebar
 */

/**
 * Initialize sidebar
 */
export function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const menuToggle = document.getElementById('menu-toggle');
    const sidebarToggle = document.getElementById('sidebar-toggle');

    if (!sidebar) return;

    // Mobile menu toggle
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-open');
        });
    }

    // Backdrop click to close (mobile)
    if (backdrop) {
        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('is-open');
        });
    }

    // Collapse toggle (desktop)
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-collapsed');
            // Save preference
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('is-collapsed'));
        });
    }

    // Restore collapsed state
    const savedState = localStorage.getItem('sidebar-collapsed');
    if (savedState === 'true' && window.innerWidth >= 1024) {
        sidebar.classList.add('is-collapsed');
    }

    // Handle resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('is-open');
        }
    });
}
