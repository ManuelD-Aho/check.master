/**
 * CheckMaster - Modal Component
 * ==============================
 * Gestion des modales et dialogues
 */

let activeModal = null;

/**
 * Initialize all modals
 */
export function initModals() {
    // Close on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                closeModal(backdrop);
            }
        });
    });

    // Close button click
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal-backdrop');
            if (modal) closeModal(modal);
        });
    });

    // Open triggers
    document.querySelectorAll('[data-modal-open]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = trigger.getAttribute('data-modal-open');
            const modal = document.getElementById(modalId);
            if (modal) openModal(modal);
        });
    });

    // Escape key to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && activeModal) {
            closeModal(activeModal);
        }
    });
}

/**
 * Open a modal
 * @param {HTMLElement|string} modal - Modal element or ID
 */
export function openModal(modal) {
    if (typeof modal === 'string') {
        modal = document.getElementById(modal);
    }
    if (!modal) return;

    // Close any active modal first
    if (activeModal) {
        closeModal(activeModal);
    }

    activeModal = modal;
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';

    // Focus first focusable element
    const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable) {
        setTimeout(() => focusable.focus(), 100);
    }
}

/**
 * Close a modal
 * @param {HTMLElement|string} modal - Modal element or ID
 */
export function closeModal(modal) {
    if (typeof modal === 'string') {
        modal = document.getElementById(modal);
    }
    if (!modal) return;

    modal.classList.remove('is-open');
    document.body.style.overflow = '';

    if (activeModal === modal) {
        activeModal = null;
    }
}

/**
 * Create and show a confirm dialog
 * @param {Object} options - Dialog options
 * @returns {Promise<boolean>}
 */
export function confirm(options = {}) {
    const {
        title = 'Confirmer',
        message = 'Êtes-vous sûr ?',
        confirmText = 'Confirmer',
        cancelText = 'Annuler',
        type = 'warning' // info, warning, danger, success
    } = options;

    return new Promise((resolve) => {
        // Create modal element
        const modalId = 'confirm-' + Date.now();
        const modalHtml = `
            <div class="modal-backdrop" id="${modalId}">
                <div class="modal modal-confirm modal-sm">
                    <div class="modal-body">
                        <div class="modal-confirm-icon icon-${type}">
                            ${getConfirmIcon(type)}
                        </div>
                        <h3 class="modal-title" style="margin-bottom: 8px;">${escapeHtml(title)}</h3>
                        <p class="text-secondary">${escapeHtml(message)}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-action="cancel">
                            ${escapeHtml(cancelText)}
                        </button>
                        <button type="button" class="btn btn-${type === 'danger' ? 'danger' : 'primary'}" data-action="confirm">
                            ${escapeHtml(confirmText)}
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = document.getElementById(modalId);

        // Handle actions
        modal.querySelector('[data-action="confirm"]').addEventListener('click', () => {
            closeModal(modal);
            setTimeout(() => modal.remove(), 200);
            resolve(true);
        });

        modal.querySelector('[data-action="cancel"]').addEventListener('click', () => {
            closeModal(modal);
            setTimeout(() => modal.remove(), 200);
            resolve(false);
        });

        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal);
                setTimeout(() => modal.remove(), 200);
                resolve(false);
            }
        });

        openModal(modal);
    });
}

function getConfirmIcon(type) {
    const icons = {
        info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
        warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        danger: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>'
    };
    return icons[type] || icons.info;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Expose globally
window.openModal = openModal;
window.closeModal = closeModal;
window.confirmDialog = confirm;
