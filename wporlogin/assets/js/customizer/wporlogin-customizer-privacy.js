/**
 * WPORLogin - Personalizador de Política de Privacidad y Links de Error (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    // Selectores separados claramente
    const privacyLink = $('body.login div#login div.privacy-policy-page-link a.privacy-policy-link');
    const privacyContainer = $('body.login div#login div.privacy-policy-page-link');

    /**
     * Cambia el color del enlace de política de privacidad.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyPrivacyLinkColor(color) {
        privacyLink.css('color', color);
    }

    /**
     * Cambia el margen superior del contenedor de política de privacidad.
     *
     * @param {number|string} marginTop - Valor en píxeles.
     */
    function applyPrivacyMarginTop(marginTop) {
        privacyContainer.css('margin-top', `${marginTop}px`);
    }

    /**
     * Cambia el margen inferior del contenedor de política de privacidad.
     *
     * @param {number|string} marginBottom - Valor en píxeles.
     */
    function applyPrivacyMarginBottom(marginBottom) {
        privacyContainer.css('margin-bottom', `${marginBottom}px`);
    }

    // === Escuchar cambios en el Personalizador === //

    // Cambios de color general (error links + política)
    wp.customize('wporlogin_privacy_color', function(value) {
        value.bind(function(newVal) {
            applyPrivacyLinkColor(newVal);
        });
        applyPrivacyLinkColor(value.get());
    });

    // Cambios de margen superior de la política
    wp.customize('wporlogin_privacy_margin_top', function(value) {
        value.bind(function(newVal) {
            applyPrivacyMarginTop(newVal);
        });
        applyPrivacyMarginTop(value.get());
    });

    // Cambios de margen inferior de la política
    wp.customize('wporlogin_privacy_margin_bottom', function(value) {
        value.bind(function(newVal) {
            applyPrivacyMarginBottom(newVal);
        });
        applyPrivacyMarginBottom(value.get());
    });

})(jQuery);
