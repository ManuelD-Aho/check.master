'use strict';

/**
 * Datepicker - Wrapper Flatpickr avec locale française
 * Initialise automatiquement les éléments avec data-datepicker
 */
var Datepicker = (function() {

    var instances = [];

    /**
     * Locale française pour Flatpickr
     */
    var frenchLocale = {
        firstDayOfWeek: 1,
        weekdays: {
            shorthand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
            longhand: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
        },
        months: {
            shorthand: ['Janv', 'Févr', 'Mars', 'Avr', 'Mai', 'Juin',
                        'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'],
            longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                       'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
        },
        rangeSeparator: ' au ',
        weekAbbreviation: 'Sem',
        scrollTitle: 'Défiler pour changer',
        toggleTitle: 'Cliquer pour changer',
        ordinal: function() { return 'er'; }
    };

    /**
     * Initialise Flatpickr sur un élément donné
     */
    function initElement(element) {
        if (typeof flatpickr === 'undefined') {
            console.warn('Flatpickr n\'est pas chargé.');
            return null;
        }

        var isRange = element.hasAttribute('data-datepicker-range');
        var format = element.dataset.datepickerFormat || 'd/m/Y';
        var minDate = element.dataset.datepickerMin || null;
        var maxDate = element.dataset.datepickerMax || null;

        var options = {
            locale: frenchLocale,
            dateFormat: format,
            allowInput: true,
            altInput: false,
            mode: isRange ? 'range' : 'single'
        };

        if (minDate) options.minDate = minDate;
        if (maxDate) options.maxDate = maxDate;

        var instance = flatpickr(element, options);
        instances.push(instance);

        return instance;
    }

    return {
        /**
         * Initialise tous les datepickers de la page
         */
        init: function() {
            var elements = document.querySelectorAll('[data-datepicker]');
            elements.forEach(function(el) {
                if (!el._flatpickr) {
                    initElement(el);
                }
            });
        },

        /**
         * Détruit toutes les instances
         */
        destroy: function() {
            instances.forEach(function(instance) {
                if (instance && typeof instance.destroy === 'function') {
                    instance.destroy();
                }
            });
            instances = [];
        }
    };
})();

document.addEventListener('DOMContentLoaded', function() {
    Datepicker.init();
});
