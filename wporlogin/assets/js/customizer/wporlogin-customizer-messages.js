/**
 * WPORLogin - Personalizador de Mensajes y Avisos (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    const errorMessage = $('body.login div#login div#login_error');
    const errorMessageParagraphs = errorMessage.find('p');
    const errorMessageLinks = errorMessage.find('p a, ul li a');

    /**
     * Cambia la visibilidad del mensaje de error.
     *
     * @param {string} display - 'block' para mostrar, 'none' para ocultar.
     */
    function applyErrorVisibility(display) {
        errorMessage.css('display', display);
    }

    /**
     * Cambia el color del borde izquierdo del mensaje de error.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyErrorBorderLeftColor(color) {
        errorMessage.css('border-left-color', color);
    }

    /**
     * Cambia el color de fondo del mensaje de error.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyErrorBackgroundColor(color) {
        errorMessage.css('background-color', color);
    }

    /**
     * Cambia el color del texto dentro del mensaje de error.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyErrorTextColor(color) {
        errorMessageParagraphs.css('color', color);
    }

    /**
     * Cambia el color de los enlaces dentro del mensaje de error.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyErrorLinkColor(color) {
        errorMessageLinks.css('color', color);
    }

    // === Escuchar cambios en el Personalizador === //

    wp.customize('wporlogin_error_visible', function(value) {
        value.bind(function(newVal) {
            applyErrorVisibility(newVal);
        });
        applyErrorVisibility(value.get());
    });

    wp.customize('wporlogin_color_border_left_error', function(value) {
        value.bind(function(newVal) {
            applyErrorBorderLeftColor(newVal);
        });
        applyErrorBorderLeftColor(value.get());
    });

    wp.customize('wporlogin_color_bg_error', function(value) {
        value.bind(function(newVal) {
            applyErrorBackgroundColor(newVal);
        });
        applyErrorBackgroundColor(value.get());
    });

    wp.customize('wporlogin_color_parrafo_error', function(value) {
        value.bind(function(newVal) {
            applyErrorTextColor(newVal);
        });
        applyErrorTextColor(value.get());
    });

    wp.customize('wporlogin_color_link_error', function(value) {
        value.bind(function(newVal) {
            applyErrorLinkColor(newVal);
        });
        applyErrorLinkColor(value.get());
    });

})(jQuery);
