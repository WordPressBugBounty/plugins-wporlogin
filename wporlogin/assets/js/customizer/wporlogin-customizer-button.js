/**
 * WPORLogin - Personalizador del Botón de Acceso (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    const loginButton = $('body.login div#login form#loginform input#wp-submit');

    /**
     * Aplica el ancho del botón.
     *
     * @param {string} config - Valor combinado (ej: 'auto|content-box|0').
     */
    function applyButtonWidth(config) {
        const [width, boxSizing, borderSpacing] = config.split('|');
        loginButton.css({
            'width': width,
            'box-sizing': boxSizing,
            'border-spacing': borderSpacing
        });
    }

    /**
     * Aplica el padding del botón.
     *
     * @param {string} padding - Padding CSS (ej: '10px 12px').
     */
    function applyButtonPadding(padding) {
        loginButton.css('padding', padding);
    }

    /**
     * Aplica el tamaño de fuente del botón.
     *
     * @param {string} fontSize - Tamaño de fuente (ej: '13px').
     */
    function applyButtonFontSize(fontSize) {
        loginButton.css('font-size', fontSize);
    }

    /**
     * Aplica el color del texto del botón.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyButtonTextColor(color) {
        loginButton.css('color', color);
    }

    /**
     * Aplica el color de fondo del botón.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyButtonBackgroundColor(color) {
        loginButton.css('background-color', color);
    }

    /**
     * Aplica el color del borde del botón.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyButtonBorderColor(color) {
        loginButton.css('border-color', color);
    }

    /**
     * Aplica el radio de borde (border-radius) del botón.
     *
     * @param {string} radius - Valor de radio (ej: '3px').
     */
    function applyButtonBorderRadius(radius) {
        loginButton.css('border-radius', radius);
    }

    // === Escuchar cambios en el Personalizador === //

    wp.customize('wporlogin_submit_button_full_width', function(value) {
        value.bind(function(newVal) {
            applyButtonWidth(newVal);
        });
        applyButtonWidth(value.get());
    });

    wp.customize('wporlogin_submit_button_padding', function(value) {
        value.bind(function(newVal) {
            applyButtonPadding(newVal);
        });
        applyButtonPadding(value.get());
    });

    wp.customize('wporlogin_submit_button_font_size', function(value) {
        value.bind(function(newVal) {
            applyButtonFontSize(newVal);
        });
        applyButtonFontSize(value.get());
    });

    wp.customize('wporlogin_submit_button_color', function(value) {
        value.bind(function(newVal) {
            applyButtonTextColor(newVal);
        });
        applyButtonTextColor(value.get());
    });

    wp.customize('wporlogin_submit_button_bgcolor', function(value) {
        value.bind(function(newVal) {
            applyButtonBackgroundColor(newVal);
        });
        applyButtonBackgroundColor(value.get());
    });

    wp.customize('wporlogin_submit_button_color_borde', function(value) {
        value.bind(function(newVal) {
            applyButtonBorderColor(newVal);
        });
        applyButtonBorderColor(value.get());
    });

    wp.customize('wporlogin_submit_button_radio', function(value) {
        value.bind(function(newVal) {
            applyButtonBorderRadius(newVal);
        });
        applyButtonBorderRadius(value.get());
    });

})(jQuery);
