/**
 * CheckMaster - Tabs Component
 * =============================
 * Gestion des onglets
 */

/**
 * Initialize all tab groups
 */
export function initTabs() {
    document.querySelectorAll('[role="tablist"]').forEach(tablist => {
        const tabs = tablist.querySelectorAll('[role="tab"]');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                activateTab(tab, tablist);
            });

            tab.addEventListener('keydown', (e) => {
                handleTabKeyboard(e, tab, tabs);
            });
        });
    });

    // Also support class-based tabs
    document.querySelectorAll('.tabs').forEach(tablist => {
        const tabs = tablist.querySelectorAll('.tab');

        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const panelId = tab.getAttribute('data-tab-target');

                // Deactivate all tabs
                tabs.forEach(t => t.classList.remove('is-active'));

                // Hide all panels
                if (panelId) {
                    const panels = document.querySelectorAll('[data-tab-panel]');
                    panels.forEach(p => p.classList.add('hidden'));

                    // Show target panel
                    const targetPanel = document.querySelector(`[data-tab-panel="${panelId}"]`);
                    if (targetPanel) {
                        targetPanel.classList.remove('hidden');
                    }
                }

                // Activate clicked tab
                tab.classList.add('is-active');
            });
        });
    });
}

/**
 * Activate a tab
 * @param {HTMLElement} tab 
 * @param {HTMLElement} tablist 
 */
function activateTab(tab, tablist) {
    const panelId = tab.getAttribute('aria-controls');
    const tabs = tablist.querySelectorAll('[role="tab"]');

    // Deactivate all tabs
    tabs.forEach(t => {
        t.setAttribute('aria-selected', 'false');
        t.setAttribute('tabindex', '-1');
    });

    // Hide all panels
    tabs.forEach(t => {
        const pId = t.getAttribute('aria-controls');
        const panel = document.getElementById(pId);
        if (panel) panel.hidden = true;
    });

    // Activate clicked tab
    tab.setAttribute('aria-selected', 'true');
    tab.setAttribute('tabindex', '0');
    tab.focus();

    // Show corresponding panel
    const panel = document.getElementById(panelId);
    if (panel) panel.hidden = false;
}

/**
 * Handle keyboard navigation for tabs
 * @param {KeyboardEvent} e 
 * @param {HTMLElement} currentTab 
 * @param {NodeList} tabs 
 */
function handleTabKeyboard(e, currentTab, tabs) {
    const tabsArray = Array.from(tabs);
    const currentIndex = tabsArray.indexOf(currentTab);

    let newIndex;

    switch (e.key) {
        case 'ArrowLeft':
            newIndex = currentIndex > 0 ? currentIndex - 1 : tabsArray.length - 1;
            break;
        case 'ArrowRight':
            newIndex = currentIndex < tabsArray.length - 1 ? currentIndex + 1 : 0;
            break;
        case 'Home':
            newIndex = 0;
            break;
        case 'End':
            newIndex = tabsArray.length - 1;
            break;
        default:
            return;
    }

    e.preventDefault();
    activateTab(tabsArray[newIndex], currentTab.closest('[role="tablist"]'));
}
