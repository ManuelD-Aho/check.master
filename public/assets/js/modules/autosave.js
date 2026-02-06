'use strict';

/**
 * Autosave - Module de sauvegarde automatique
 * Complète App.initAutoSave() avec localStorage fallback et indicateur de statut
 */
var Autosave = (function() {

    var config = {
        interval: 30000,
        url: null,
        getData: null,
        storageKey: null,
        statusSelector: '.autosave-status'
    };

    var timer = null;
    var debounceTimer = null;
    var statusEl = null;
    var isDirty = false;

    /**
     * Met à jour l'indicateur de statut
     */
    function setStatus(text, type) {
        if (!statusEl) return;

        statusEl.textContent = text;
        statusEl.className = config.statusSelector.replace('.', '') +
            (type ? ' autosave-' + type : '');
    }

    /**
     * Sauvegarde dans localStorage comme fallback
     */
    function saveToLocalStorage(data) {
        if (!config.storageKey) return;

        try {
            localStorage.setItem(config.storageKey, JSON.stringify({
                data: data,
                timestamp: new Date().toISOString()
            }));
        } catch (e) {
            // localStorage plein ou indisponible
        }
    }

    /**
     * Restaure depuis localStorage
     */
    function restoreFromLocalStorage() {
        if (!config.storageKey) return null;

        try {
            var stored = localStorage.getItem(config.storageKey);
            if (stored) {
                return JSON.parse(stored);
            }
        } catch (e) {
            // Données corrompues
        }

        return null;
    }

    /**
     * Effectue la sauvegarde via AJAX
     */
    function performSave() {
        if (!config.url || !config.getData) return Promise.resolve();
        if (!isDirty) return Promise.resolve();

        var data = config.getData();
        if (!data) return Promise.resolve();

        setStatus('Sauvegarde...', 'saving');
        isDirty = false;

        var headers = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        };

        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                        document.querySelector('input[name="_csrf_token"]')?.value;
        if (csrfToken) {
            headers['X-CSRF-Token'] = csrfToken;
        }

        return fetch(config.url, {
            method: 'POST',
            headers: headers,
            credentials: 'same-origin',
            body: JSON.stringify(data)
        })
        .then(function(response) {
            if (!response.ok) throw new Error('Erreur serveur');
            return response.json();
        })
        .then(function(result) {
            var time = new Date().toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
            setStatus('Sauvegardé à ' + time, 'saved');

            // Nettoie le localStorage après succès
            if (config.storageKey) {
                try { localStorage.removeItem(config.storageKey); } catch (e) {}
            }

            return result;
        })
        .catch(function(error) {
            setStatus('Erreur de sauvegarde', 'error');
            saveToLocalStorage(data);
            isDirty = true;
            throw error;
        });
    }

    return {
        /**
         * Initialise le module d'autosave
         * @param {Object} options
         * @param {string} options.url - URL de sauvegarde
         * @param {Function} options.getData - Fonction retournant les données à sauvegarder
         * @param {number} options.interval - Intervalle en ms (défaut: 30000)
         * @param {string} options.storageKey - Clé localStorage pour le fallback
         * @param {string} options.statusSelector - Sélecteur de l'élément de statut
         */
        init: function(options) {
            if (!options || !options.url || !options.getData) {
                console.warn('Autosave: url et getData sont requis.');
                return;
            }

            Object.keys(options).forEach(function(key) {
                if (options[key] !== undefined) {
                    config[key] = options[key];
                }
            });

            statusEl = document.querySelector(config.statusSelector);

            // Démarre le timer d'autosave
            timer = setInterval(function() {
                performSave().catch(function() {});
            }, config.interval);

            setStatus('Autosave activé', 'active');
        },

        /**
         * Arrête l'autosave
         */
        stop: function() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
            if (debounceTimer) {
                clearTimeout(debounceTimer);
                debounceTimer = null;
            }
            setStatus('Autosave désactivé', '');
        },

        /**
         * Déclenche une sauvegarde manuelle
         * @returns {Promise}
         */
        save: function() {
            isDirty = true;
            return performSave();
        },

        /**
         * Marque le contenu comme modifié (avec debounce)
         * Appelé typiquement sur les événements input/change
         */
        markDirty: function() {
            isDirty = true;

            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }

            debounceTimer = setTimeout(function() {
                performSave().catch(function() {});
            }, 5000);
        },

        /**
         * Restaure les données depuis localStorage
         * @returns {Object|null}
         */
        restore: function() {
            return restoreFromLocalStorage();
        }
    };
})();
