/**
 * WPORLogin - Personalizador de Confirmación de Email de Administrador (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    const form = $('body.login div#login form.admin-email-confirm-form');
    const formH1 = form.find('h1');
    const formParagraph = form.find('p.admin-email__details');
    const formParagraphLink = form.find('p.admin-email__details a');
    const secondaryButton = form.find('div.admin-email__actions div.admin-email__actions-secondary a');
    const primaryButtonLink = form.find('div.admin-email__actions div.admin-email__actions-primary a');
    const primaryButtonInput = form.find('div.admin-email__actions div.admin-email__actions-primary input#correct-admin-email');

    // ====== 1. HELPERS ====== //

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

    // ====== Aplicar cambios en tiempo real ====== //

    /**
     * [FIXED] Unified function for background color + opacity.
     */
    function updateFormBackground() {
        var color = wp.customize('wporlogin_emailconfirm_bg_color') ? wp.customize('wporlogin_emailconfirm_bg_color').get() : '#ffffff';
        var opacity = wp.customize('wporlogin_emailconfirm_opacity') ? wp.customize('wporlogin_emailconfirm_opacity').get() : '1';

        var rgbaString = convertToRgba(color, opacity);

        form.css({
            'background-color': rgbaString,
            'opacity': '' // Clear opacity property to prevent text fading
        });
    }

    /*function applyBackgroundColor(color) {
        form.css('background-color', color);
    }*/

    function applyBorderColor(color) {
        form.css('border-color', color);
    }

    function applyHeadingColor(color) {
        formH1.css('color', color);
    }

    function applyParagraphColor(color) {
        formParagraph.css('color', color);
    }

    function applyLinkColor(color) {
        formParagraphLink.css('color', color);
        secondaryButton.css('color', color);
    }

    function applyButtonOneTextColor(color) {
        primaryButtonLink.css('color', color);
    }

    function applyButtonOneBackgroundColor(color) {
        primaryButtonLink.css('background-color', color);
    }

    function applyButtonOneBorderColor(color) {
        primaryButtonLink.css('border-color', color);
    }

    function applyButtonTwoTextColor(color) {
        primaryButtonInput.css('color', color);
    }

    function applyButtonTwoBackgroundColor(color) {
        primaryButtonInput.css('background-color', color);
    }

    function applyButtonTwoBorderColor(color) {
        primaryButtonInput.css('border-color', color);
    }

    /*function applyFormOpacity(opacity) {
        form.css('opacity', opacity);
    }*/

    function applyFormBorderStyle(style) {
        form.css('border-style', style);
    }

    function applyFormBorderRadius(radius) {
        form.css('border-radius', radius);
    }

    function applyButtonOneBorderRadius(radius) {
        primaryButtonLink.css('border-radius', radius);
    }

    function applyButtonTwoBorderRadius(radius) {
        primaryButtonInput.css('border-radius', radius);
    }

    function applyFormBorderWidth(width) {
        form.css('border-width', width + 'px');
    }

    function applyPadding(side, value) {
        form.css(`padding-${side}`, value + 'px');
    }

    function applyHeadingFontSize(size) {
        formH1.css('font-size', size);
    }

    // ====== Escuchar cambios en el Personalizador ====== //

    /*wp.customize('wporlogin_emailconfirm_bg_color', function(value) {
        value.bind(function(newVal){
            applyBackgroundColor(newVal);
        });
        applyBackgroundColor(value.get());
    });*/

    // Background & Opacity -> Unified Function
    wp.customize('wporlogin_emailconfirm_bg_color', function(value) {
        value.bind(function(newVal){ updateFormBackground(); });
        updateFormBackground(); // Persistence
    });

    wp.customize('wporlogin_emailconfirm_opacity', function(value) {
        value.bind(function(newVal){ updateFormBackground(); });
    });

    wp.customize('wporlogin_emailconfirm_border_color', function(value) {
        value.bind(function(newVal){
            applyBorderColor(newVal);
        });
        applyBorderColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_h1_color', function(value) {
        value.bind(function(newVal){
            applyHeadingColor(newVal);
        });
        applyHeadingColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_p_color', function(value) {
        value.bind(function(newVal){
            applyParagraphColor(newVal);
        });
        applyParagraphColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_link_color', function(value) {
        value.bind(function(newVal){
            applyLinkColor(newVal);
        });
        applyLinkColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_button_one_color', function(value) {
        value.bind(function(newVal){
            applyButtonOneTextColor(newVal);
        });
        applyButtonOneTextColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_button_one_bgcolor', function(value) {
        value.bind(function(newVal){
            applyButtonOneBackgroundColor(newVal);
        });
        applyButtonOneBackgroundColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_button_one_color_borde', function(value) {
        value.bind(function(newVal){
            applyButtonOneBorderColor(newVal);
        });
        applyButtonOneBorderColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_button_two_color', function(value) {
        value.bind(function(newVal){
            applyButtonTwoTextColor(newVal);
        });
        applyButtonTwoTextColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_button_two_bgcolor', function(value) {
        value.bind(function(newVal){
            applyButtonTwoBackgroundColor(newVal);
        });
        applyButtonTwoBackgroundColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_button_two_color_borde', function(value) {
        value.bind(function(newVal){
            applyButtonTwoBorderColor(newVal);
        });
        applyButtonTwoBorderColor(value.get());
    });

    wp.customize('wporlogin_emailconfirm_border_style', function(value) {
        value.bind(function(newVal){
            applyFormBorderStyle(newVal);
        });
        applyFormBorderStyle(value.get());
    });

    wp.customize('wporlogin_emailconfirm_radio', function(value) {
        value.bind(function(newVal){
            applyFormBorderRadius(newVal);
        });
        applyFormBorderRadius(value.get());
    });

    wp.customize('wporlogin_emailconfirm_button_one_radio', function(value) {
        value.bind(function(newVal){
            applyButtonOneBorderRadius(newVal);
        });
        applyButtonOneBorderRadius(value.get());
    });

    wp.customize('wporlogin_emailconfirm_button_two_radio', function(value) {
        value.bind(function(newVal){
            applyButtonTwoBorderRadius(newVal);
        });
        applyButtonTwoBorderRadius(value.get());
    });

    wp.customize('wporlogin_emailconfirm_border_width', function(value) {
        value.bind(function(newVal){
            applyFormBorderWidth(newVal);
        });
        applyFormBorderWidth(value.get());
    });

    ['left', 'top', 'right', 'bottom'].forEach(function(side) {
        wp.customize(`wporlogin_emailconfirm_padding_${side}`, function(value) {
            value.bind(function(newVal) {
                applyPadding(side, newVal);
            });
            applyPadding(side, value.get());
        });
    });

    wp.customize('wporlogin_emailconfirm_h1_size', function(value) {
        value.bind(function(newVal){
            applyHeadingFontSize(newVal);
        });
        applyHeadingFontSize(value.get());
    });

})(jQuery);
