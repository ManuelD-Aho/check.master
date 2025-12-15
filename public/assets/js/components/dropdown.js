/**
 * CheckMaster - Dropdown Component
 * =================================
 * Gestion des menus déroulants
 */

/**
 * Initialize all dropdowns
 */
export function initDropdowns() {
    // Toggle on trigger click
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const trigger = dropdown.querySelector('[aria-haspopup], .header-action, .dropdown-trigger');
        if (!trigger) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleDropdown(dropdown);
        });
    });

    // Close on outside click
    document.addEventListener('click', () => {
        closeAllDropdowns();
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAllDropdowns();
        }
    });

    // Keyboard navigation
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('keydown', (e) => {
            handleKeyboardNavigation(e, menu);
        });
    });
}

/**
 * Toggle a dropdown
 * @param {HTMLElement} dropdown 
 */
function toggleDropdown(dropdown) {
    const isOpen = dropdown.classList.contains('is-open');

    // Close all others first
    closeAllDropdowns();

    if (!isOpen) {
        dropdown.classList.add('is-open');
        const trigger = dropdown.querySelector('[aria-haspopup], .header-action');
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'true');
        }

        // Focus first item
        const firstItem = dropdown.querySelector('.dropdown-item');
        if (firstItem) firstItem.focus();
    }
}

/**
 * Close all open dropdowns
 */
function closeAllDropdowns() {
    document.querySelectorAll('.dropdown.is-open').forEach(dropdown => {
        dropdown.classList.remove('is-open');
        const trigger = dropdown.querySelector('[aria-haspopup], .header-action');
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        }
    });
}

/**
 * Handle keyboard navigation within dropdown
 * @param {KeyboardEvent} e 
 * @param {HTMLElement} menu 
 */
function handleKeyboardNavigation(e, menu) {
    const items = Array.from(menu.querySelectorAll('.dropdown-item:not(.is-disabled)'));
    const currentIndex = items.indexOf(document.activeElement);

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        const nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
        items[nextIndex].focus();
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
        items[prevIndex].focus();
    } else if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        if (document.activeElement.classList.contains('dropdown-item')) {
            document.activeElement.click();
        }
    }
}
