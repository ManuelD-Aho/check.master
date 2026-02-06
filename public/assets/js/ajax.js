'use strict';

/**
 * Ajax - Module utilitaire pour les requêtes HTTP
 * Complète App.ajax() avec gestion avancée (loading, erreurs, FormData)
 */
var Ajax = (function() {

    /**
     * Récupère le token CSRF depuis la meta tag ou un input caché
     */
    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.content;

        var input = document.querySelector('input[name="_csrf_token"]');
        if (input) return input.value;

        return null;
    }

    /**
     * Affiche l'indicateur de chargement
     */
    function showLoading() {
        var loader = document.querySelector('.ajax-loading');
        if (loader) {
            loader.style.display = 'block';
            return;
        }

        loader = document.createElement('div');
        loader.className = 'ajax-loading';
        loader.innerHTML = '<div class="ajax-loading-spinner"></div>';
        document.body.appendChild(loader);
    }

    /**
     * Masque l'indicateur de chargement
     */
    function hideLoading() {
        var loader = document.querySelector('.ajax-loading');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    /**
     * Gère les erreurs de réponse avec messages utilisateur
     */
    function handleError(response) {
        var status = response.status;
        var messages = {
            400: 'Requête invalide.',
            401: 'Vous devez être connecté pour effectuer cette action.',
            403: 'Vous n\'avez pas les droits nécessaires.',
            404: 'Ressource introuvable.',
            419: 'Votre session a expiré. Veuillez rafraîchir la page.',
            422: 'Les données envoyées sont invalides.',
            429: 'Trop de requêtes. Veuillez patienter.',
            500: 'Erreur interne du serveur.',
            503: 'Service temporairement indisponible.'
        };

        var message = messages[status] || 'Une erreur est survenue (code ' + status + ').';

        return response.json().catch(function() {
            return {};
        }).then(function(data) {
            var error = new Error(data.message || message);
            error.status = status;
            error.data = data;
            throw error;
        });
    }

    /**
     * Exécute une requête HTTP
     */
    function request(method, url, data, options) {
        options = options || {};

        var headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        };

        var csrfToken = getCsrfToken();
        if (csrfToken) {
            headers['X-CSRF-Token'] = csrfToken;
        }

        var fetchOptions = {
            method: method,
            headers: headers,
            credentials: 'same-origin'
        };

        if (data !== undefined && data !== null) {
            if (data instanceof FormData) {
                fetchOptions.body = data;
            } else {
                headers['Content-Type'] = 'application/json';
                fetchOptions.body = JSON.stringify(data);
            }
        }

        if (options.showLoading !== false) {
            showLoading();
        }

        return fetch(url, fetchOptions)
            .then(function(response) {
                if (options.showLoading !== false) {
                    hideLoading();
                }

                if (!response.ok) {
                    return handleError(response);
                }

                var contentType = response.headers.get('content-type') || '';
                if (contentType.indexOf('application/json') !== -1) {
                    return response.json();
                }

                return response.text();
            })
            .catch(function(error) {
                if (options.showLoading !== false) {
                    hideLoading();
                }

                if (!error.status) {
                    error.message = 'Erreur de connexion. Vérifiez votre réseau.';
                }

                if (options.silent !== true) {
                    var alertEl = document.querySelector('.ajax-error-alert');
                    if (!alertEl) {
                        alertEl = document.createElement('div');
                        alertEl.className = 'ajax-error-alert alert alert-danger';
                        var container = document.querySelector('.container') || document.body;
                        container.insertBefore(alertEl, container.firstChild);
                    }
                    alertEl.textContent = error.message;
                    alertEl.style.display = 'block';

                    setTimeout(function() {
                        alertEl.style.display = 'none';
                    }, 5000);
                }

                throw error;
            });
    }

    return {
        /**
         * Requête GET
         * @param {string} url
         * @param {Object} options - { showLoading, silent }
         * @returns {Promise}
         */
        get: function(url, options) {
            return request('GET', url, null, options);
        },

        /**
         * Requête POST
         * @param {string} url
         * @param {Object|FormData} data
         * @param {Object} options
         * @returns {Promise}
         */
        post: function(url, data, options) {
            return request('POST', url, data, options);
        },

        /**
         * Requête PUT
         * @param {string} url
         * @param {Object|FormData} data
         * @param {Object} options
         * @returns {Promise}
         */
        put: function(url, data, options) {
            return request('PUT', url, data, options);
        },

        /**
         * Requête DELETE
         * @param {string} url
         * @param {Object} options
         * @returns {Promise}
         */
        delete: function(url, options) {
            return request('DELETE', url, null, options);
        }
    };
})();
