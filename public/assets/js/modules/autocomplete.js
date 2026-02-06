'use strict';

/**
 * Autocomplete - Autocomplétion pour les champs de recherche
 * Complète App.search() avec navigation clavier et dropdown
 */
var Autocomplete = (function() {

    var DEBOUNCE_DELAY = 300;
    var MIN_CHARS = 2;
    var activeInstances = [];

    /**
     * Crée une instance d'autocomplete sur un input
     * @param {HTMLInputElement} input
     * @param {Object} options
     * @param {string} options.url - URL de recherche (le terme est ajouté en ?q=)
     * @param {Function} options.onSelect - Callback de sélection (reçoit l'item)
     * @param {Function} options.renderItem - Rendu personnalisé d'un item (reçoit l'item, retourne du texte)
     * @param {string} options.dataKey - Clé dans la réponse JSON contenant les résultats
     * @param {number} options.minChars - Nombre minimum de caractères (défaut: 2)
     * @param {number} options.debounce - Délai anti-rebond en ms (défaut: 300)
     */
    function create(input, options) {
        if (!input || !options || !options.url) return;

        var debounceTimer = null;
        var selectedIndex = -1;
        var items = [];
        var minChars = options.minChars || MIN_CHARS;
        var debounceDelay = options.debounce || DEBOUNCE_DELAY;

        // Crée le dropdown
        var dropdown = document.createElement('div');
        dropdown.className = 'autocomplete-dropdown';
        dropdown.style.display = 'none';
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(dropdown);

        /**
         * Affiche les résultats
         */
        function renderResults(data) {
            items = options.dataKey ? (data[options.dataKey] || []) : (data || []);
            dropdown.innerHTML = '';
            selectedIndex = -1;

            if (items.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            items.forEach(function(item, index) {
                var div = document.createElement('div');
                div.className = 'autocomplete-item';
                div.textContent = options.renderItem
                    ? options.renderItem(item)
                    : (item.label || item.nom || item.raison_sociale || item.name || String(item));
                div.dataset.index = index;

                div.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    selectItem(index);
                });

                dropdown.appendChild(div);
            });

            dropdown.style.display = 'block';
        }

        /**
         * Sélectionne un item
         */
        function selectItem(index) {
            if (index < 0 || index >= items.length) return;

            var item = items[index];
            input.value = options.renderItem
                ? options.renderItem(item)
                : (item.label || item.nom || item.raison_sociale || item.name || String(item));

            dropdown.style.display = 'none';

            if (options.onSelect) {
                options.onSelect(item);
            }
        }

        /**
         * Met en surbrillance l'item sélectionné
         */
        function highlightItem() {
            var allItems = dropdown.querySelectorAll('.autocomplete-item');
            allItems.forEach(function(el, i) {
                el.classList.toggle('autocomplete-item-active', i === selectedIndex);
            });

            if (selectedIndex >= 0 && allItems[selectedIndex]) {
                allItems[selectedIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        /**
         * Recherche les suggestions
         */
        function fetchSuggestions(query) {
            var separator = options.url.indexOf('?') !== -1 ? '&' : '?';
            var url = options.url + separator + 'q=' + encodeURIComponent(query);

            var headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            };

            fetch(url, { headers: headers, credentials: 'same-origin' })
                .then(function(response) {
                    if (!response.ok) throw new Error('Erreur réseau');
                    return response.json();
                })
                .then(function(data) {
                    renderResults(data);
                })
                .catch(function() {
                    dropdown.style.display = 'none';
                });
        }

        // Événement input avec debounce
        input.addEventListener('input', function() {
            var query = input.value.trim();

            if (debounceTimer) clearTimeout(debounceTimer);

            if (query.length < minChars) {
                dropdown.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(function() {
                fetchSuggestions(query);
            }, debounceDelay);
        });

        // Navigation clavier
        input.addEventListener('keydown', function(e) {
            if (dropdown.style.display === 'none') return;

            var itemCount = items.length;

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    selectedIndex = (selectedIndex + 1) % itemCount;
                    highlightItem();
                    break;

                case 'ArrowUp':
                    e.preventDefault();
                    selectedIndex = selectedIndex <= 0 ? itemCount - 1 : selectedIndex - 1;
                    highlightItem();
                    break;

                case 'Enter':
                    if (selectedIndex >= 0) {
                        e.preventDefault();
                        selectItem(selectedIndex);
                    }
                    break;

                case 'Escape':
                    dropdown.style.display = 'none';
                    selectedIndex = -1;
                    break;
            }
        });

        // Ferme le dropdown au clic extérieur
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Ferme le dropdown au blur
        input.addEventListener('blur', function() {
            setTimeout(function() {
                dropdown.style.display = 'none';
            }, 200);
        });

        var instance = {
            destroy: function() {
                if (debounceTimer) clearTimeout(debounceTimer);
                dropdown.remove();
            }
        };

        activeInstances.push(instance);
        return instance;
    }

    return {
        /**
         * Initialise l'autocomplete sur un input
         * @param {HTMLInputElement} input
         * @param {Object} options
         * @returns {Object} Instance avec méthode destroy()
         */
        init: function(input, options) {
            return create(input, options);
        },

        /**
         * Détruit toutes les instances
         */
        destroyAll: function() {
            activeInstances.forEach(function(instance) {
                instance.destroy();
            });
            activeInstances = [];
        }
    };
})();
