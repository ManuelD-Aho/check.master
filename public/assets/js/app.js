const App = {
    csrfToken: null,

    init: function() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        this.initFlashMessages();
        this.initConfirmDialogs();
        this.initAutoSave();
    },

    initFlashMessages: function() {
        const alerts = document.querySelectorAll('.alert[data-auto-hide]');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    },

    initConfirmDialogs: function() {
        document.querySelectorAll('[data-confirm]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                const message = this.dataset.confirm || 'Etes-vous sur ?';
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    },

    initAutoSave: function() {
        const editor = document.querySelector('[data-autosave]');
        if (!editor) return;

        let saveTimeout;
        const rapportId = editor.dataset.rapportId;
        const statusEl = document.querySelector('.autosave-status');

        editor.addEventListener('input', function() {
            if (statusEl) statusEl.textContent = 'Modification...';
            
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(function() {
                App.autoSave(rapportId, editor.value, statusEl);
            }, 3000);
        });
    },

    autoSave: function(rapportId, content, statusEl) {
        this.ajax('/api/rapport/autosave', {
            method: 'POST',
            body: {
                rapport_id: rapportId,
                content: content
            }
        }).then(function(response) {
            if (statusEl) {
                statusEl.textContent = response.success 
                    ? 'Sauvegarde a ' + response.timestamp 
                    : 'Erreur de sauvegarde';
            }
        }).catch(function() {
            if (statusEl) statusEl.textContent = 'Erreur de connexion';
        });
    },

    ajax: function(url, options) {
        options = options || {};
        const headers = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (this.csrfToken) {
            headers['X-CSRF-Token'] = this.csrfToken;
        }

        const fetchOptions = {
            method: options.method || 'GET',
            headers: headers
        };

        if (options.body) {
            fetchOptions.body = JSON.stringify(options.body);
        }

        return fetch(url, fetchOptions).then(function(response) {
            if (!response.ok) {
                throw new Error('Request failed');
            }
            return response.json();
        });
    },

    search: function(inputId, url, resultsId, onSelect) {
        const input = document.getElementById(inputId);
        const results = document.getElementById(resultsId);
        if (!input || !results) return;

        let timeout;
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                results.innerHTML = '';
                results.style.display = 'none';
                return;
            }

            timeout = setTimeout(function() {
                App.ajax(url + '?q=' + encodeURIComponent(query))
                    .then(function(data) {
                        results.innerHTML = '';
                        const items = data.entreprises || data.etudiants || data.enseignants || [];
                        
                        items.forEach(function(item) {
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.textContent = item.raison_sociale || item.nom + ' ' + item.prenom;
                            div.addEventListener('click', function() {
                                if (onSelect) onSelect(item);
                                results.style.display = 'none';
                            });
                            results.appendChild(div);
                        });
                        
                        results.style.display = items.length ? 'block' : 'none';
                    });
            }, 300);
        });
    }
};

document.addEventListener('DOMContentLoaded', function() {
    App.init();
});
