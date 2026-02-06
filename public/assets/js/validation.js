'use strict';

/**
 * FormValidator - Validation côté client des formulaires
 * Messages d'erreur en français, validation au submit et au blur
 */
var FormValidator = (function() {

    var ERROR_CLASS = 'field-error';
    var ERROR_MSG_CLASS = 'field-error-message';

    /**
     * Messages d'erreur en français
     */
    var messages = {
        required: 'Ce champ est obligatoire.',
        email: 'Veuillez saisir une adresse e-mail valide.',
        minLength: 'Ce champ doit contenir au moins {min} caractères.',
        maxLength: 'Ce champ ne doit pas dépasser {max} caractères.',
        pattern: 'Le format saisi est invalide.',
        date: 'Veuillez saisir une date valide (jj/mm/aaaa).',
        numeric: 'Veuillez saisir une valeur numérique.',
        min: 'La valeur doit être supérieure ou égale à {min}.',
        max: 'La valeur doit être inférieure ou égale à {max}.',
        match: 'Les deux champs ne correspondent pas.'
    };

    /**
     * Règles de validation
     */
    var validators = {
        required: function(value) {
            return value !== null && value !== undefined && value.trim() !== '';
        },

        email: function(value) {
            if (!value) return true;
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        },

        minLength: function(value, params) {
            if (!value) return true;
            return value.length >= params.min;
        },

        maxLength: function(value, params) {
            if (!value) return true;
            return value.length <= params.max;
        },

        pattern: function(value, params) {
            if (!value) return true;
            var regex = params.regex instanceof RegExp ? params.regex : new RegExp(params.regex);
            return regex.test(value);
        },

        date: function(value) {
            if (!value) return true;
            var parts = value.split('/');
            if (parts.length !== 3) return false;

            var day = parseInt(parts[0], 10);
            var month = parseInt(parts[1], 10);
            var year = parseInt(parts[2], 10);

            if (isNaN(day) || isNaN(month) || isNaN(year)) return false;
            if (month < 1 || month > 12) return false;
            if (day < 1 || day > 31) return false;
            if (year < 1900 || year > 2100) return false;

            var dateObj = new Date(year, month - 1, day);
            return dateObj.getFullYear() === year &&
                   dateObj.getMonth() === month - 1 &&
                   dateObj.getDate() === day;
        },

        numeric: function(value) {
            if (!value) return true;
            return !isNaN(parseFloat(value)) && isFinite(value);
        },

        min: function(value, params) {
            if (!value) return true;
            return parseFloat(value) >= params.min;
        },

        max: function(value, params) {
            if (!value) return true;
            return parseFloat(value) <= params.max;
        },

        match: function(value, params, form) {
            if (!value) return true;
            var otherField = form.querySelector('[name="' + params.field + '"]');
            return otherField && value === otherField.value;
        }
    };

    /**
     * Formate un message d'erreur avec les paramètres
     */
    function formatMessage(template, params) {
        var result = template;
        if (params) {
            Object.keys(params).forEach(function(key) {
                result = result.replace('{' + key + '}', params[key]);
            });
        }
        return result;
    }

    /**
     * Affiche un message d'erreur sous le champ
     */
    function showError(field, message) {
        clearError(field);
        field.classList.add(ERROR_CLASS);

        var errorEl = document.createElement('div');
        errorEl.className = ERROR_MSG_CLASS;
        errorEl.textContent = message;

        field.parentNode.insertBefore(errorEl, field.nextSibling);
    }

    /**
     * Supprime le message d'erreur d'un champ
     */
    function clearError(field) {
        field.classList.remove(ERROR_CLASS);

        var errorEl = field.parentNode.querySelector('.' + ERROR_MSG_CLASS);
        if (errorEl) {
            errorEl.remove();
        }
    }

    /**
     * Valide un champ selon ses règles
     * @returns {boolean}
     */
    function validateField(field, fieldRules, form) {
        var value = field.value;
        var isValid = true;

        for (var i = 0; i < fieldRules.length; i++) {
            var rule = fieldRules[i];
            var ruleName = typeof rule === 'string' ? rule : rule.rule;
            var params = typeof rule === 'string' ? {} : rule;

            if (!validators[ruleName]) continue;

            if (!validators[ruleName](value, params, form)) {
                var msg = (rule.message) || formatMessage(messages[ruleName], params);
                showError(field, msg);
                isValid = false;
                break;
            }
        }

        if (isValid) {
            clearError(field);
        }

        return isValid;
    }

    /**
     * Initialise la validation sur un formulaire
     * @param {HTMLFormElement} formElement
     * @param {Object} rules - { fieldName: [{ rule: 'required' }, { rule: 'minLength', min: 3 }] }
     */
    function init(formElement, rules) {
        if (!formElement || !rules) return;

        // Validation au blur
        Object.keys(rules).forEach(function(fieldName) {
            var field = formElement.querySelector('[name="' + fieldName + '"]');
            if (!field) return;

            field.addEventListener('blur', function() {
                validateField(field, rules[fieldName], formElement);
            });

            field.addEventListener('input', function() {
                if (field.classList.contains(ERROR_CLASS)) {
                    validateField(field, rules[fieldName], formElement);
                }
            });
        });

        // Validation au submit
        formElement.addEventListener('submit', function(e) {
            var allValid = true;

            Object.keys(rules).forEach(function(fieldName) {
                var field = formElement.querySelector('[name="' + fieldName + '"]');
                if (!field) return;

                if (!validateField(field, rules[fieldName], formElement)) {
                    allValid = false;
                }
            });

            if (!allValid) {
                e.preventDefault();

                var firstError = formElement.querySelector('.' + ERROR_CLASS);
                if (firstError) {
                    firstError.focus();
                }
            }
        });
    }

    return {
        init: init,
        validateField: validateField,
        clearError: clearError,
        messages: messages
    };
})();
