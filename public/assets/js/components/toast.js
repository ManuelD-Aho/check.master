/**
 * CheckMaster - Toast Component
 * ==============================
 * Notifications toast
 */

let toastContainer = null;

/**
 * Initialize toast container
 */
export function initToasts() {
    if (!document.querySelector('.toast-container')) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    } else {
        toastContainer = document.querySelector('.toast-container');
    }
}

/**
 * Show a toast notification
 * @param {Object} options - Toast options
 */
export function toast(options = {}) {
    const {
        title = '',
        message = '',
        type = 'info', // success, warning, error, info
        duration = 5000,
        dismissible = true
    } = typeof options === 'string' ? { message: options } : options;

    if (!toastContainer) initToasts();

    const icons = {
        success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>',
        warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
    };

    const toastEl = document.createElement('div');
    toastEl.className = `toast toast-${type}`;
    toastEl.innerHTML = `
        <span class="toast-icon">${icons[type] || icons.info}</span>
        <div class="toast-content">
            ${title ? `<div class="toast-title">${escapeHtml(title)}</div>` : ''}
            ${message ? `<div class="toast-message">${escapeHtml(message)}</div>` : ''}
        </div>
        ${dismissible ? `
            <button type="button" class="toast-close" aria-label="Fermer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        ` : ''}
        ${duration > 0 ? '<div class="toast-progress"></div>' : ''}
    `;

    toastContainer.appendChild(toastEl);

    // Close button
    const closeBtn = toastEl.querySelector('.toast-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => dismissToast(toastEl));
    }

    // Auto dismiss
    if (duration > 0) {
        setTimeout(() => dismissToast(toastEl), duration);
    }

    return toastEl;
}

function dismissToast(toastEl) {
    toastEl.classList.add('is-leaving');
    setTimeout(() => toastEl.remove(), 200);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Shorthand methods
toast.success = (message, title) => toast({ type: 'success', message, title });
toast.error = (message, title) => toast({ type: 'error', message, title });
toast.warning = (message, title) => toast({ type: 'warning', message, title });
toast.info = (message, title) => toast({ type: 'info', message, title });
