/**
 * WPORLogin - Personalizador de Formulario de Registro (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    const registerForm = $('body.login div#login form#registerform');
    const registerLabels = $('body.login div#login form#registerform p label');
    const registerPassMail = $('body.login div#login form#registerform p#reg_passmail');
    const submitButton = $('body.login div#login form#registerform p.submit input#wp-submit');

    // ======== HELPER: Convertir Hex a RGBA ======== //
    // Necesario para mezclar color y opacidad sin afectar el texto
    function convertToRgba(color, opacity) {
        if (!color) return 'rgba(255, 255, 255, ' + opacity + ')';
        
        // Si ya es RGB
        if (color.startsWith('rgb')) {
            const rgb = color.match(/\d+/g);
            return `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, ${opacity})`;
        }

        // Si es Hexadecimal
        const hex = color.replace('#', '');
        const bigint = parseInt(hex, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;

        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    // ======== Aplicar estilos en tiempo real ======== //

    /**
     * [SOLUCIÓN] Función Unificada para el Fondo
     * Lee el color y la opacidad actuales y aplica un fondo RGBA.
     * Esto evita que el texto se vuelva transparente.
     */
    function updateRegisterBackground() {
        // 1. Obtener valores actuales desde WP (Fuente de verdad)
        var color = wp.customize('wporlogin_register_bg_color') ? wp.customize('wporlogin_register_bg_color').get() : '#ffffff';
        var opacity = wp.customize('wporlogin_register_opacity') ? wp.customize('wporlogin_register_opacity').get() : '1';

        // 2. Calcular RGBA
        var rgbaString = convertToRgba(color, opacity);

        // 3. Aplicar y limpiar la opacidad CSS heredada
        registerForm.css({
            'background-color': rgbaString,
            'opacity': '' // Importante: Eliminar opacity del CSS para que no afecte hijos
        });
    }

    /*function applyBackgroundColor(color) {
        registerForm.css('background-color', color);
    }

    function applyFormOpacity(opacity) {
        registerForm.css('opacity', opacity);
    }*/

    function applyBorderColor(color) {
        registerForm.css('border-color', color);
    }

    function applyLabelTextColor(color) {
        registerLabels.css('color', color);
        registerPassMail.css('color', color);
    }

    function applySubmitButtonTextColor(color) {
        submitButton.css('color', color);
    }

    function applySubmitButtonBackgroundColor(color) {
        submitButton.css('background-color', color);
    }

    function applySubmitButtonBorderColor(color) {
        submitButton.css('border-color', color);
    }

    function applyFormBorderStyle(style) {
        registerForm.css('border-style', style);
    }

    function applyFormBorderRadius(radius) {
        registerForm.css('border-radius', radius);
    }

    function applyFormBorderWidth(width) {
        registerForm.css('border-width', width + 'px');
    }

    function applyPadding(side, value) {
        registerForm.css(`padding-${side}`, value + 'px');
    }

    function applyButtonWidth(fullWidth) {
        const parts = fullWidth.split('|');
        if (parts.length === 2) {
            submitButton.css({
                'width': parts[0],
                'box-sizing': parts[1],
                // Corrección visual para cuando es 100%
                'float': (parts[0] === '100%') ? 'none' : 'right',
                'display': (parts[0] === '100%') ? 'block' : 'inline-block'
            });
        }
    }

    function applyButtonPadding(padding) {
        submitButton.css('padding', padding);
    }

    function applyButtonFontSize(size) {
        submitButton.css('font-size', size);
    }

    function applyButtonBorderRadius(radius) {
        submitButton.css('border-radius', radius);
    }

    // ======== Escuchar cambios en el Personalizador ======== //

    /*wp.customize('wporlogin_register_bg_color', function(value) {
        value.bind(function(newVal){
            applyBackgroundColor(newVal);
        });
        applyBackgroundColor(value.get());
    });

    wp.customize('wporlogin_register_opacity', function(value) {
        value.bind(function(newVal){
            applyFormOpacity(newVal);
        });
        applyFormOpacity(value.get());
    });*/

    // 1. Color de Fondo (Ahora llama a la función unificada)
    wp.customize('wporlogin_register_bg_color', function(value) {
        value.bind(function(newVal){
            updateRegisterBackground(); // Ignoramos newVal, leemos ambos valores dentro
        });
        updateRegisterBackground(); // Persistencia
    });

    // 2. Opacidad (Ahora llama a la función unificada)
    wp.customize('wporlogin_register_opacity', function(value) {
        value.bind(function(newVal){
            updateRegisterBackground();
        });
        // Nota: No llamamos a la persistencia aquí para evitar doble llamada al inicio, 
        // ya que el bg_color lo hará.
    });

    wp.customize('wporlogin_register_border_color', function(value) {
        value.bind(function(newVal){
            applyBorderColor(newVal);
        });
        applyBorderColor(value.get());
    });

    wp.customize('wporlogin_register_p_color', function(value) {
        value.bind(function(newVal) {
            applyLabelTextColor(newVal);            
        });
        applyLabelTextColor(value.get());
    });

    wp.customize('wporlogin_register_submit_button_color', function(value) {
        value.bind(function(newVal){
            applySubmitButtonTextColor(newVal);
        });
        applySubmitButtonTextColor(value.get());
    });

    wp.customize('wporlogin_register_submit_button_bgcolor', function(value) {
        value.bind(function(newVal){
            applySubmitButtonBackgroundColor(newVal);
        });
        applySubmitButtonBackgroundColor(value.get());
    });

    wp.customize('wporlogin_register_submit_button_color_borde', function(value) {
        value.bind(function(newVal){
            applySubmitButtonBorderColor(newVal);
        });
        applySubmitButtonBorderColor(value.get());
    });

    wp.customize('wporlogin_register_border_style', function(value) {
        value.bind(function(newVal){
            applyFormBorderStyle(newVal);
        });
        applyFormBorderStyle(value.get());
    });

    wp.customize('wporlogin_register_radio', function(value) {
        value.bind(function(newVal){
            applyFormBorderRadius(newVal);
        });
        applyFormBorderRadius(value.get());
    });

    wp.customize('wporlogin_register_border_width', function(value) {
        value.bind(function(newVal){
            applyFormBorderWidth(newVal);
        });
        applyFormBorderWidth(value.get());
    });

    ['left', 'top', 'right', 'bottom'].forEach(function(side) {
        wp.customize(`wporlogin_register_padding_${side}`, function(value) {
            value.bind(function(newVal) {
                applyPadding(side, newVal);
            });
            applyPadding(side, value.get());
        });
    });

    wp.customize('wporlogin_register_submit_button_full_width', function(value) {
        value.bind(function(newVal){
            applyButtonWidth(newVal);
        });
        applyButtonWidth(value.get());
    });

    wp.customize('wporlogin_register_submit_button_padding', function(value) {
        value.bind(function(newVal){
            applyButtonPadding(newVal);
        });
        applyButtonPadding(value.get());
    });

    wp.customize('wporlogin_register_submit_button_font_size', function(value) {
        value.bind(function(newVal){
            applyButtonFontSize(newVal);
        });
        applyButtonFontSize(value.get());
    });

    wp.customize('wporlogin_register_submit_button_radio', function(value) {
        value.bind(function(newVal){
            applyButtonBorderRadius(newVal);
        });
        applyButtonBorderRadius(value.get());
    });

})(jQuery);