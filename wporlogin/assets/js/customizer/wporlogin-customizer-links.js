/**
 * WPORLogin - Personalizador de Enlaces y Ajustes Extras (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    const links = $('body.login #nav a, body.login #backtoblog a');
    const linkContainers = $('body.login #nav, body.login #backtoblog');

    /**
     * Aplica el color a los enlaces.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyLinkColor(color) {
        links.css('color', color);
    }

    /**
     * Aplica la alineación de los contenedores de enlaces.
     *
     * @param {string} align - Valor de alineación CSS ('left', 'center', 'right').
     */
    function applyLinkAlignment(align) {
        linkContainers.css('text-align', align);
    }

    // === Escuchar cambios en el Personalizador === //

    wp.customize('wporlogin_link_color', function(value) {
        value.bind(function(newVal) {
            applyLinkColor(newVal);
        });
        applyLinkColor(value.get());
    });

    wp.customize('wporlogin_link_align', function(value) {
        value.bind(function(newVal) {
            applyLinkAlignment(newVal);
        });
        applyLinkAlignment(value.get());
    });

})(jQuery);
