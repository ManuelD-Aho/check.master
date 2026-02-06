'use strict';

/**
 * PermissionsMatrix - Interface de matrice de permissions RBAC
 * Grille de cases à cocher (lignes: fonctionnalités, colonnes: actions CRUD)
 */
var PermissionsMatrix = (function() {

    var ACTIONS = ['create', 'read', 'update', 'delete'];
    var ACTION_LABELS = {
        create: 'Créer',
        read: 'Lire',
        update: 'Modifier',
        delete: 'Supprimer'
    };

    var container = null;
    var data = null;
    var saveUrl = null;

    /**
     * Crée l'en-tête de la matrice
     */
    function renderHeader(thead) {
        var tr = document.createElement('tr');

        var thLabel = document.createElement('th');
        thLabel.textContent = 'Fonctionnalité';
        tr.appendChild(thLabel);

        ACTIONS.forEach(function(action) {
            var th = document.createElement('th');
            th.className = 'text-center';
            th.innerHTML = ACTION_LABELS[action] +
                '<br><label class="toggle-col-label">' +
                '<input type="checkbox" class="toggle-col" data-action="' + action + '"> Tout' +
                '</label>';
            tr.appendChild(th);
        });

        thead.appendChild(tr);
    }

    /**
     * Crée une ligne de la matrice pour une fonctionnalité
     */
    function renderRow(tbody, fonctionnalite) {
        var tr = document.createElement('tr');
        tr.dataset.id = fonctionnalite.id;

        var tdLabel = document.createElement('td');
        tdLabel.textContent = fonctionnalite.libelle || fonctionnalite.nom;
        if (fonctionnalite.categorie) {
            tdLabel.innerHTML = '<span class="permission-category">' +
                fonctionnalite.categorie + '</span> ' + tdLabel.textContent;
        }
        tr.appendChild(tdLabel);

        ACTIONS.forEach(function(action) {
            var td = document.createElement('td');
            td.className = 'text-center';

            var checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'permission-checkbox';
            checkbox.dataset.fonctionnaliteId = fonctionnalite.id;
            checkbox.dataset.action = action;
            checkbox.checked = fonctionnalite.permissions &&
                               fonctionnalite.permissions.indexOf(action) !== -1;

            checkbox.addEventListener('change', function() {
                updateToggleAll();
            });

            td.appendChild(checkbox);
            tr.appendChild(td);
        });

        tbody.appendChild(tr);
    }

    /**
     * Met à jour les cases "Tout cocher" en-tête
     */
    function updateToggleAll() {
        if (!container) return;

        ACTIONS.forEach(function(action) {
            var colCheckboxes = container.querySelectorAll(
                '.permission-checkbox[data-action="' + action + '"]'
            );
            var toggleCol = container.querySelector('.toggle-col[data-action="' + action + '"]');

            if (toggleCol && colCheckboxes.length > 0) {
                var allChecked = true;
                colCheckboxes.forEach(function(cb) {
                    if (!cb.checked) allChecked = false;
                });
                toggleCol.checked = allChecked;
            }
        });
    }

    /**
     * Collecte les permissions cochées
     */
    function collectPermissions() {
        var permissions = [];

        if (!container) return permissions;

        var rows = container.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            var fonctionnaliteId = row.dataset.id;

            ACTIONS.forEach(function(action) {
                var cb = row.querySelector(
                    '.permission-checkbox[data-action="' + action + '"]'
                );
                if (cb && cb.checked) {
                    permissions.push({
                        fonctionnalite_id: fonctionnaliteId,
                        action: action
                    });
                }
            });
        });

        return permissions;
    }

    /**
     * Affiche un feedback visuel
     */
    function showFeedback(message, type) {
        var existing = container.querySelector('.permissions-feedback');
        if (existing) existing.remove();

        var feedback = document.createElement('div');
        feedback.className = 'permissions-feedback alert alert-' +
            (type === 'success' ? 'success' : 'danger');
        feedback.textContent = message;

        container.insertBefore(feedback, container.firstChild);

        setTimeout(function() {
            feedback.style.transition = 'opacity 0.5s';
            feedback.style.opacity = '0';
            setTimeout(function() { feedback.remove(); }, 500);
        }, 3000);
    }

    /**
     * Sauvegarde les permissions via AJAX
     */
    function savePermissions() {
        if (!saveUrl) return;

        var permissions = collectPermissions();

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

        fetch(saveUrl, {
            method: 'POST',
            headers: headers,
            credentials: 'same-origin',
            body: JSON.stringify({ permissions: permissions })
        })
        .then(function(response) {
            if (!response.ok) throw new Error('Erreur serveur');
            return response.json();
        })
        .then(function() {
            showFeedback('Permissions sauvegardées avec succès.', 'success');
        })
        .catch(function() {
            showFeedback('Erreur lors de la sauvegarde des permissions.', 'error');
        });
    }

    return {
        /**
         * Initialise la matrice de permissions
         * @param {HTMLElement} containerEl - Élément conteneur
         * @param {Object} options
         * @param {Array} options.fonctionnalites - Liste des fonctionnalités
         * @param {string} options.saveUrl - URL de sauvegarde
         */
        init: function(containerEl, options) {
            if (!containerEl) return;

            options = options || {};
            container = containerEl;
            data = options.fonctionnalites || [];
            saveUrl = options.saveUrl || containerEl.dataset.saveUrl;

            container.innerHTML = '';

            // Crée la table
            var table = document.createElement('table');
            table.className = 'table table-bordered permissions-matrix-table';

            var thead = document.createElement('thead');
            renderHeader(thead);
            table.appendChild(thead);

            var tbody = document.createElement('tbody');
            data.forEach(function(fonctionnalite) {
                renderRow(tbody, fonctionnalite);
            });
            table.appendChild(tbody);

            container.appendChild(table);

            // Toggle colonnes
            container.querySelectorAll('.toggle-col').forEach(function(toggle) {
                toggle.addEventListener('change', function() {
                    var action = this.dataset.action;
                    var checked = this.checked;

                    container.querySelectorAll(
                        '.permission-checkbox[data-action="' + action + '"]'
                    ).forEach(function(cb) {
                        cb.checked = checked;
                    });
                });
            });

            // Bouton sauvegarder
            var btnSave = document.createElement('button');
            btnSave.type = 'button';
            btnSave.className = 'btn btn-primary mt-3';
            btnSave.textContent = 'Enregistrer les permissions';
            btnSave.addEventListener('click', savePermissions);
            container.appendChild(btnSave);

            updateToggleAll();
        }
    };
})();
