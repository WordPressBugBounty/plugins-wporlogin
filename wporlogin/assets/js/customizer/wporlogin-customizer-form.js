/**
 * WPORLogin - Personalizador del Formulario de Acceso (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    const loginForm = $('body.login div#login form#loginform');   

    // 1. HELPER: Convertir Hex a RGBA (Vital para la transparencia inteligente)
    function convertToRgba(color, opacity) {
        if (!color) return 'rgba(255, 255, 255, ' + opacity + ')';
        
        if (color.startsWith('rgb')) {
            const rgb = color.match(/\d+/g);
            return `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, ${opacity})`;
        }

        const hex = color.replace('#', '');
        const bigint = parseInt(hex, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;

        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    // 2. FUNCIÓN UNIFICADA: Maneja Color + Opacidad
    function updateFormBackground() {
        var color = wp.customize('wporlogin_form_bg_color') ? wp.customize('wporlogin_form_bg_color').get() : '#ffffff';
        var opacity = wp.customize('wporlogin_form_opacity') ? wp.customize('wporlogin_form_opacity').get() : '1';

        var rgbaString = convertToRgba(color, opacity);

        loginForm.css({
            'background-color': rgbaString,
            'opacity': '' // Borramos opacity para que no afecte al texto
        });
    }

    /**
     * Aplica el color del texto de las etiquetas <p> dentro del formulario.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyFormTextColor(color) {
        loginForm.find('p').css('color', color);
        loginForm.find('div').css('color', color);
    }

    /**
     * Aplica el ancho del borde al formulario.
     *
     * @param {string|number} width - Ancho del borde sin unidad (ej: 1, 2, 0).
     */
    function applyFormBorderWidth(width) {
        const finalWidth = `${parseInt(width, 10)}px`;
        loginForm.css('border-width', finalWidth);
    }

    /**
     * Aplica el estilo del borde.
     *
     * @param {string} style - Estilo CSS de borde ('solid', 'dashed', etc.).
     */
    function applyFormBorderStyle(style) {
        loginForm.css('border-style', style);
    }

    /**
     * Aplica el color del borde.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyFormBorderColor(color) {
        loginForm.css('border-color', color);
    }

    /**
     * Aplica el radio del borde (esquinas redondeadas).
     *
     * @param {string} radius - Radio del borde (ej: '5px').
     */
    function applyFormBorderRadius(radius) {
        loginForm.css('border-radius', radius);
    }
    
    /**
     * Aplica sombra al formulario.
     *
     * @param {string} value - Valor ('default' o 'none').
     */
    function applyBoxShadow(value) {
            if (value === 'none') {
                    loginForm.css('box-shadow', 'none');
            } else {
                    loginForm.css('box-shadow', '0 1px 3px rgba(0, 0, 0, 0.04)');
            }
    }

    /**
     * Aplica un relleno (padding) en una dirección específica.
     *
     * @param {string} side - 'top', 'right', 'bottom', 'left'.
     * @param {number|string} value - Valor del relleno en píxeles.
     */
    function applyFormPadding(side, value) {
        loginForm.css(`padding-${side}`, `${value}px`);  
    }

    /**
    * Aplica margen a un lado específico del formulario.
    */

    function applyFormMargin(side, value){  
        loginForm.css(`margin-${side}`, `${value}px`);        
    }

    // === Escuchar cambios en el Personalizador === //
    
// Color de Fondo: Ahora llama a updateFormBackground
    wp.customize('wporlogin_form_bg_color', function(value) {
        value.bind(function(newVal) {
            updateFormBackground();
        });
        updateFormBackground(); // Persistencia
    });

    // Opacidad: [NUEVO]
    wp.customize('wporlogin_form_opacity', function(value) {
        value.bind(function(newVal) {
            updateFormBackground();
        });
        // No llamamos persistencia aquí para evitar doble ejecución con bg_color
    });

    wp.customize('wporlogin_form_p_color', function(value) {
        value.bind(function(newVal) {
            applyFormTextColor(newVal);
        });
        applyFormTextColor(value.get());
    });

    wp.customize('wporlogin_form_border_width', function(value) {
        value.bind(function(newVal) {
            applyFormBorderWidth(newVal);
        });
        applyFormBorderWidth(value.get());
    });

    wp.customize('wporlogin_form_border_style', function(value) {
        value.bind(function(newVal) {
            applyFormBorderStyle(newVal);
        });
        applyFormBorderStyle(value.get());
    });

    wp.customize('wporlogin_form_border_color', function(value) {
        value.bind(function(newVal) {
            applyFormBorderColor(newVal);
        });
        applyFormBorderColor(value.get());
    });

    wp.customize('wporlogin_form_border_radius', function(value) {
        value.bind(function(newVal) {
            applyFormBorderRadius(newVal);
        });
        applyFormBorderRadius(value.get());
    });
    
    wp.customize('wporlogin_form_box_shadow', function (value) {
        value.bind(function (newVal) {
            applyBoxShadow(newVal);
        });
        applyBoxShadow(value.get());
    });

    ['left', 'top', 'right', 'bottom'].forEach(function(side) {
        wp.customize(`wporlogin_form_padding_${side}`, function(value) {
            value.bind(function(newVal) {
                applyFormPadding(side, newVal);
            });
            applyFormPadding(side, value.get());
        });
    });
    
    ['top', 'right', 'bottom', 'left'].forEach(function (side) {
	wp.customize(`wporlogin_form_margin_${side}`, function (value) {
            value.bind(function (newVal) {
                applyFormMargin(side, newVal);
            });
            applyFormMargin(side, value.get());
        });
    });
})(jQuery);