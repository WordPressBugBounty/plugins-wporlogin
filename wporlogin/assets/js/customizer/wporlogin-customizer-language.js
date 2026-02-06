/**
 * WPORLogin - Personalizador del Selector de Idioma (Vista previa en vivo)
 *
 * @package WPORLogin
 */

(function($) {
    'use strict';

    const languageSwitcher = $('body.login .language-switcher');

    /**
     * Alterna la visibilidad.
     * @param {boolean|string} shouldRemove - 1/true (Ocultar) o 0/false (Mostrar).
     */
    function toggleLanguageSwitcher(shouldRemove) {
        // En tu lógica antigua, 1 significa "Eliminar" (Ocultar)
        if (shouldRemove == 1 || shouldRemove === true) {
            languageSwitcher.css('display', 'none');
        } else {
            languageSwitcher.css('display', 'block');
        }
    }

    // Escuchamos la variable ANTIGUA
    wp.customize('remove_language_wporlogin', function(value) {
        value.bind(function(newVal) {
            toggleLanguageSwitcher(newVal);
        });
        toggleLanguageSwitcher(value.get());
    });

})(jQuery);
