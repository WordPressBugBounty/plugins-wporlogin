/**
 * WPORLogin - Personalizador de Formulario de Recuperación de Contraseña (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    const recoveryForm = $('body.login div#login form#lostpasswordform');
    const recoveryLabels = $('body.login div#login form#lostpasswordform p label');
    const submitButton = $('body.login div#login form#lostpasswordform p.submit input#wp-submit');

    // ======== 1. HELPERS ======== //

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

    // ======== 2. LOGIC FUNCTIONS ======== //

    /**
     * [FIXED] Updates background using RGBA to prevent text transparency issues.
     */
    function updateRecoveryBackground() {
        var color = wp.customize('wporlogin_recovery_bg_color') ? wp.customize('wporlogin_recovery_bg_color').get() : '#ffffff';
        var opacity = wp.customize('wporlogin_recovery_opacity') ? wp.customize('wporlogin_recovery_opacity').get() : '1';

        var rgbaString = convertToRgba(color, opacity);

        recoveryForm.css({
            'background-color': rgbaString,
            'opacity': '' // Clear legacy opacity property
        });
    }

    function applyBorderColor(color) {
        recoveryForm.css('border-color', color);
    }

    function applyLabelTextColor(color) {
        recoveryLabels.css('color', color);
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
        recoveryForm.css('border-style', style);
    }

    function applyFormBorderRadius(radius) {
        recoveryForm.css('border-radius', radius);
    }

    function applyFormBorderWidth(width) {
        recoveryForm.css('border-width', width + 'px');
    }

    function applyPadding(side, value) {
        recoveryForm.css(`padding-${side}`, value + 'px');
    }

    function applyButtonWidth(fullWidth) {
        const parts = fullWidth.split('|');
        if (parts.length === 2) {
            submitButton.css({
                'width': parts[0],
                'box-sizing': parts[1],
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

// ======== 3. LISTENERS (BINDINGS) ======== //

    // Background Color -> Unified Function
    wp.customize('wporlogin_recovery_bg_color', function(value) {
        value.bind(function(newVal){ updateRecoveryBackground(); });
        updateRecoveryBackground(); // Persistence
    });

    // Opacity -> Unified Function
    wp.customize('wporlogin_recovery_opacity', function(value) {
        value.bind(function(newVal){ updateRecoveryBackground(); });
    });

    wp.customize('wporlogin_recovery_border_color', function(value) {
        value.bind(function(newVal){
            applyBorderColor(newVal);
        });
        applyBorderColor(value.get());
    });

    wp.customize('wporlogin_recovery_p_color', function(value) {
        value.bind(function(newVal){
            applyLabelTextColor(newVal);
        });
        applyLabelTextColor(value.get());
    });

    wp.customize('wporlogin_recovery_submit_button_color', function(value) {
        value.bind(function(newVal){
            applySubmitButtonTextColor(newVal);
        });
        applySubmitButtonTextColor(value.get());
    });

    wp.customize('wporlogin_recovery_submit_button_bgcolor', function(value) {
        value.bind(function(newVal){
            applySubmitButtonBackgroundColor(newVal);
        });
        applySubmitButtonBackgroundColor(value.get());
    });

    wp.customize('wporlogin_recovery_submit_button_color_borde', function(value) {
        value.bind(function(newVal){
            applySubmitButtonBorderColor(newVal);
        });
        applySubmitButtonBorderColor(value.get());
    });

    wp.customize('wporlogin_recovery_border_style', function(value) {
        value.bind(function(newVal){
            applyFormBorderStyle(newVal);
        });
        applyFormBorderStyle(value.get());
    });

    wp.customize('wporlogin_recovery_radio', function(value) {
        value.bind(function(newVal){
            applyFormBorderRadius(newVal);
        });
        applyFormBorderRadius(value.get());
    });

    wp.customize('wporlogin_recovery_border_width', function(value) {
        value.bind(function(newVal){
            applyFormBorderWidth(newVal);
        });
        applyFormBorderWidth(value.get());
    });

    ['left', 'top', 'right', 'bottom'].forEach(function(side) {
        wp.customize(`wporlogin_recovery_padding_${side}`, function(value) {
            value.bind(function(newVal) {
                applyPadding(side, newVal);
            });
            applyPadding(side, value.get());
        });
    });

    wp.customize('wporlogin_recovery_submit_button_full_width', function(value) {
        value.bind(function(newVal){
            applyButtonWidth(newVal);
        });
        applyButtonWidth(value.get());
    });

    wp.customize('wporlogin_recovery_submit_button_padding', function(value) {
        value.bind(function(newVal){
            applyButtonPadding(newVal);
        });
        applyButtonPadding(value.get());
    });

    wp.customize('wporlogin_recovery_submit_button_font_size', function(value) {
        value.bind(function(newVal){
            applyButtonFontSize(newVal);
        });
        applyButtonFontSize(value.get());
    });

    wp.customize('wporlogin_recovery_submit_button_radio', function(value) {
        value.bind(function(newVal){
            applyButtonBorderRadius(newVal);
        });
        applyButtonBorderRadius(value.get());
    });

})(jQuery);
