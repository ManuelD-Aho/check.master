/**
 * CheckMaster - Alert Component
 * ==============================
 * Gestion des alertes dismissibles
 */

/**
 * Initialize all dismissible alerts
 */
export function initAlerts() {
    document.querySelectorAll('.alert .alert-dismiss').forEach(btn => {
        btn.addEventListener('click', () => {
            const alert = btn.closest('.alert');
            if (alert) {
                dismissAlert(alert);
            }
        });
    });
}

/**
 * Dismiss an alert with animation
 * @param {HTMLElement} alert 
 */
function dismissAlert(alert) {
    alert.style.opacity = '0';
    alert.style.transform = 'translateX(10px)';
    alert.style.transition = 'opacity 200ms, transform 200ms';

    setTimeout(() => {
        alert.remove();
    }, 200);
}
