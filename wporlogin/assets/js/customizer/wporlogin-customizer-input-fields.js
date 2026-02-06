/**
 * WPORLogin - Personalizador de Campos de Entrada (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    const inputFields = $('body.login div#login form#loginform input[type="text"], body.login div#login form#loginform input[type="password"]');

    /**
     * Aplica el color de texto en los campos de entrada.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyInputTextColor(color) {
        inputFields.css('color', color);
    }

    /**
     * Aplica el color de fondo en los campos de entrada.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyInputBackgroundColor(color) {
        inputFields.css('background-color', color);
    }

    /**
     * Aplica el color del borde en los campos de entrada.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyInputBorderColor(color) {
        inputFields.css('border-color', color);
    }

    /**
     * Aplica el radio de borde (border-radius) en los campos de entrada.
     *
     * @param {string} radius - Valor de radio (ej: '4px').
     */
    function applyInputBorderRadius(radius) {
        inputFields.css('border-radius', radius);
    }

    // === Escuchar cambios en el Personalizador === //

    wp.customize('wporlogin_input_color', function(value) {
        value.bind(function(newVal) {
            applyInputTextColor(newVal);
        });
        applyInputTextColor(value.get());
    });

    wp.customize('wporlogin_input_bgcolor', function(value) {
        value.bind(function(newVal) {
            applyInputBackgroundColor(newVal);
        });
        applyInputBackgroundColor(value.get());
    });

    wp.customize('wporlogin_input_color_borde', function(value) {
        value.bind(function(newVal) {
            applyInputBorderColor(newVal);
        });
        applyInputBorderColor(value.get());
    });

    wp.customize('wporlogin_input_radio', function(value) {
        value.bind(function(newVal) {
            applyInputBorderRadius(newVal);
        });
        applyInputBorderRadius(value.get());
    });

})(jQuery);
