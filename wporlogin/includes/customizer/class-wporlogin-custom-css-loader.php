<?php
/**
 * Clase que carga estilos SOLO en el Personalizador (Preview).
 * Usa wp_enqueue_scripts para garantizar que is_page() funcione correctamente.
 *
 * @package WPORLogin_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPORLogin_Custom_CSS_Loader {

    /**
     * Inicializa el hook.
     * Usamos 'wp_enqueue_scripts' porque es el momento seguro donde 
     * WordPress ya sabe en qué página está (Main Query lista).
     */
    public static function init() {
        // Prioridad 20 para cargar después de la mayoría de estilos del tema
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_custom_login_css' ), 20 );

        // 2. LIMPIEZA AGRESIVA (Prioridad 9999 - Al final de todo)
        // Esto se ejecutará después de que el Tema y otros plugins hayan cargado sus cosas.
        add_action('wp_enqueue_scripts', array(__CLASS__, 'clean_foreign_assets'), 9999);
    }

    /**
     * Carga estilos nativos + custom CSS si estamos en el preview y en la página correcta.
     */
    public static function enqueue_custom_login_css() {
        
        // 1. FILTRO DE SEGURIDAD: Solo para el Personalizador
        // Si no estamos en el modo "Live Preview", no hacemos nada.
        // Esto protege tu sitio real (frontend) de cargar estos estilos.
        if ( ! is_customize_preview() ) {
            return;
        }

        // 2. FILTRO DE PÁGINA: Solo en la página de Login
        // Al estar en 'wp_enqueue_scripts', esta función is_page() es 100% confiable.
        $page_id = get_option('wporlogin_page_id');
        
        if ( ! $page_id || ! is_page( $page_id ) ) {
            return; // Si no es la página de login, adiós.
        }

        // -----------------------------------------------------------
        // A PARTIR DE AQUÍ, SOLO SE EJECUTA EN EL PREVIEW DEL LOGIN
        // -----------------------------------------------------------

        // A. Estilos Nativos de WordPress
        wp_enqueue_style( 'dashicons' );
        wp_enqueue_style( 'buttons', includes_url( 'css/buttons.min.css' ) );
        wp_enqueue_style( 'forms', admin_url( 'css/forms.min.css' ) );
        wp_enqueue_style( 'l10n', admin_url( 'css/l10n.min.css' ) );
        wp_enqueue_style( 'login', admin_url( 'css/login.min.css' ) );

        // B. Tu CSS Personalizado
        $upload_dir = wp_upload_dir();
        $filename   = defined('Wporlogin::CUSTOM_CSS_FILENAME') ? Wporlogin::CUSTOM_CSS_FILENAME : 'wporlogin-style-design-premium-zero.css';
        
        $base_url = set_url_scheme( $upload_dir['baseurl'] ); 
        $css_url  = trailingslashit( $base_url ) . 'wporlogin/' . $filename;
        $css_file = trailingslashit( $upload_dir['basedir'] ) . 'wporlogin/' . $filename;

        if ( file_exists( $css_file ) ) {
            wp_enqueue_style(
                'wporlogin-custom-style', 
                $css_url,
                array( 'login' ), // Importante: Cargar después del login base
                filemtime( $css_file )
            );
        }
    }

    /**
     * LA BARREDORA INTELIGENTE:
     * 1. Mantiene scripts del Core necesarios.
     * 2. Detecta automáticamente tus scripts por prefijo 'wporlogin-'.
     * 3. Elimina todo lo demás (Temas, Elementor, etc.).
     */
    public static function clean_foreign_assets() {

        // 1. Validaciones de seguridad
        if ( ! is_customize_preview() ) return;
        $page_id = get_option('wporlogin_page_id');
        if ( ! $page_id || ! is_page( $page_id ) ) return;

        // --------------------------------------------------------
        // LISTA BLANCA ESTÁTICA (Solo cosas de WordPress Core)
        // --------------------------------------------------------
        // No ponemos nada de 'wporlogin' aquí, eso se detectará solo.
        $core_allowed_scripts = array(
            'jquery', 'jquery-core', 'jquery-migrate', 'utils', 'underscore', 'wp-util',
            'customize-preview', 'customize-selective-refresh', // Vitales
            'password-strength-meter', 'zxcvbn-async', 'user-profile'
        );

        $core_allowed_styles = array(
            'dashicons', 'buttons', 'forms', 'l10n', 'login', 
            'customize-preview', 'admin-bar', 'wp-auth-check'
        );

        // --------------------------------------------------------
        // LIMPIEZA DINÁMICA DE SCRIPTS
        // --------------------------------------------------------
        global $wp_scripts;
        if ( isset( $wp_scripts->queue ) ) {
            foreach ( $wp_scripts->queue as $handle ) {
                
                // CONDICIÓN 1: ¿Es un script nativo de WP permitido?
                $is_core = in_array( $handle, $core_allowed_scripts );

                // CONDICIÓN 2: ¿Es un script de MI plugin? (Detección por prefijo)
                // strpos devuelve 0 si la cadena empieza con 'wporlogin-'
                $is_my_plugin = ( strpos( $handle, 'wporlogin-' ) === 0 );

                // Si NO es del core Y NO es mío -> ¡FUERA!
                if ( ! $is_core && ! $is_my_plugin ) {
                    wp_dequeue_script( $handle );
                    wp_deregister_script( $handle );
                }
            }
        }

        // --------------------------------------------------------
        // LIMPIEZA DINÁMICA DE ESTILOS
        // --------------------------------------------------------
        global $wp_styles;
        if ( isset( $wp_styles->queue ) ) {
            foreach ( $wp_styles->queue as $handle ) {
                
                $is_core = in_array( $handle, $core_allowed_styles );
                
                // Lo mismo aquí: Si el estilo empieza con 'wporlogin-', se salva automáticamente.
                $is_my_plugin = ( strpos( $handle, 'wporlogin-' ) === 0 );

                if ( ! $is_core && ! $is_my_plugin ) {
                    wp_dequeue_style( $handle );
                    wp_deregister_style( $handle );
                }
            }
        }
    }
}