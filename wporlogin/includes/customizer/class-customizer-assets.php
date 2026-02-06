<?php
/**
 * Archivo: class-customizer-assets.php
 * Encargado de encolar los scripts de controles del personalizador.
 *
 * @package WPORLogin
 */

// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Clase para encolar scripts en los controles del Customizer.
 */
class WPORLogin_Customizer_Assets {

    /**
     * Inicializa los hooks necesarios.
     */
    public static function init() {
        add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'enqueue_controls_script' ) );
    }

    /**
     * Encola el script y pasa datos desde PHP a JS para el panel de controles.
     */
    public static function enqueue_controls_script() {
        wp_enqueue_script(
            'wporlogin-customize-controls',
            WPORLOGIN_URL . 'assets/js/wporlogin-customize-controls.js',
            array( 'customize-controls', 'jquery' ),
            WPORLOGIN_VERSION,
            true
        );

        wp_localize_script(
            'wporlogin-customize-controls',
            'wporloginControlsSettings',
            array(
                'loginPageUrl' => esc_url( get_permalink( get_option( 'wporlogin_page_id' ) ) ),
            )
        );
    }
}

// Inicializar
WPORLogin_Customizer_Assets::init();
