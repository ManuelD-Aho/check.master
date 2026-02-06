'use strict';

/**
 * Editor - Wrapper TinyMCE pour l'édition de rapports
 * Complète tinymce-config.js avec une API programmatique
 */
var Editor = (function() {

    var activeEditor = null;
    var wordCountEl = null;

    /**
     * Met à jour le compteur de mots
     */
    function updateWordCount(editor) {
        if (!wordCountEl) return;

        var content = editor.getContent({ format: 'text' });
        var words = content.trim().split(/\s+/).filter(function(w) { return w.length > 0; });
        wordCountEl.textContent = words.length + ' mot' + (words.length !== 1 ? 's' : '');
    }

    /**
     * Crée le bouton plein écran
     */
    function setupFullscreen(editor) {
        editor.ui.registry.addButton('customFullscreen', {
            icon: 'fullscreen',
            tooltip: 'Plein écran',
            onAction: function() {
                var container = editor.getContainer();
                if (!container) return;

                container.classList.toggle('editor-fullscreen');
                document.body.classList.toggle('editor-fullscreen-active');

                var isFullscreen = container.classList.contains('editor-fullscreen');
                editor.fire('FullscreenStateChanged', { state: isFullscreen });
            }
        });
    }

    return {
        /**
         * Initialise l'éditeur TinyMCE sur le sélecteur donné
         * @param {string} selector - Sélecteur CSS de l'élément textarea
         * @param {Object} options - Options supplémentaires
         */
        init: function(selector, options) {
            if (typeof tinymce === 'undefined') {
                console.warn('TinyMCE n\'est pas chargé.');
                return Promise.resolve(null);
            }

            options = options || {};
            var self = this;

            // Crée le conteneur du compteur de mots
            var target = document.querySelector(selector);
            if (target && !target.parentNode.querySelector('.editor-word-count')) {
                wordCountEl = document.createElement('div');
                wordCountEl.className = 'editor-word-count';
                wordCountEl.textContent = '0 mot';
                target.parentNode.insertBefore(wordCountEl, target.nextSibling);
            }

            var config = {
                selector: selector,
                language: 'fr_FR',
                height: options.height || 500,
                menubar: false,
                plugins: ['lists', 'link', 'table', 'wordcount', 'autosave'],
                toolbar: 'undo redo | formatselect | bold italic underline | ' +
                         'alignleft aligncenter alignright | bullist numlist | ' +
                         'link table | removeformat | customFullscreen',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-size: 14px; }',
                branding: false,
                promotion: false,
                setup: function(editor) {
                    setupFullscreen(editor);

                    editor.on('init', function() {
                        activeEditor = editor;
                        updateWordCount(editor);
                    });

                    editor.on('keyup change', function() {
                        updateWordCount(editor);
                    });

                    editor.on('change', function() {
                        editor.save();

                        // Déclenche l'événement input pour l'intégration avec Autosave
                        var textarea = document.querySelector(selector);
                        if (textarea) {
                            textarea.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });

                    if (options.onInit) {
                        editor.on('init', function() { options.onInit(editor); });
                    }
                }
            };

            return tinymce.init(config).then(function(editors) {
                if (editors && editors.length > 0) {
                    activeEditor = editors[0];
                }
                return activeEditor;
            });
        },

        /**
         * Récupère le contenu HTML de l'éditeur
         * @returns {string}
         */
        getContent: function() {
            if (!activeEditor) return '';
            return activeEditor.getContent();
        },

        /**
         * Définit le contenu HTML de l'éditeur
         * @param {string} html
         */
        setContent: function(html) {
            if (!activeEditor) return;
            activeEditor.setContent(html);
            updateWordCount(activeEditor);
        },

        /**
         * Détruit l'instance de l'éditeur
         */
        destroy: function() {
            if (activeEditor) {
                activeEditor.destroy();
                activeEditor = null;
            }

            if (wordCountEl) {
                wordCountEl.remove();
                wordCountEl = null;
            }
        }
    };
})();
