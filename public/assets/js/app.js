/**
 * CheckMaster - Main JavaScript Entry Point
 * ==========================================
 * Initialisation des composants et utilitaires
 * Version: 1.0.0
 */

// Import utilities
import { initDropdowns } from './components/dropdown.js';
import { initModals } from './components/modal.js';
import { initToasts, toast } from './components/toast.js';
import { initAlerts } from './components/alert.js';
import { initSidebar } from './components/sidebar.js';
import { initTabs } from './components/tabs.js';

/**
 * Initialize all components when DOM is ready
 */
function initApp() {
    // Initialize sidebar
    initSidebar();

    // Initialize dropdowns
    initDropdowns();

    // Initialize modals
    initModals();

    // Initialize toast container
    initToasts();

    // Initialize dismissible alerts
    initAlerts();

    // Initialize tabs
    initTabs();

    console.log('✓ CheckMaster UI initialized');
}

// Run on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

// Expose toast globally for use in inline scripts
window.toast = toast;

// Export for module usage
export { toast };
