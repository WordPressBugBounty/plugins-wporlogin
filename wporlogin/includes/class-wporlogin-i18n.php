<?php

/**
 * Define la funcionalidad de internacionalización.
 *
 * Carga y define los archivos de traducción para este plugin
 * para que esté listo para su traducción.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/includes
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin_i18n {

    /**
     * Carga el dominio de texto del plugin para la traducción.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'wporlogin',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }

}