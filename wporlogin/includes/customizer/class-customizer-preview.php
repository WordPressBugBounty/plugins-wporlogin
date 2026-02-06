<?php
/**
 * Clase para gestionar los scripts de vista previa en vivo en el Personalizador de WordPress.
 *
 * @package WPORLogin
 */

if ( ! class_exists( 'WPORLogin_Customizer_Preview' ) ) {

    class WPORLogin_Customizer_Preview {

        /**
         * Constructor.
         * Engancha los métodos necesarios.
         */
        public function __construct() {
            add_action( 'customize_preview_init', array( $this, 'enqueue_preview_script' ) );
            add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_preview_script_controls' ) );
        }

        public function enqueue_preview_script_controls() {
            if ( is_customize_preview() ) {                
                
                // Sección: Autenticación
                wp_enqueue_script(
                    'wporlogin-customizer-authentication-controls',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-authentication-controls.js',
                    array('jquery', 'customize-controls'),
                    WPORLOGIN_VERSION,
                    true
                );
            }
        }
        /**
         * Encolar el script de vista previa y pasar datos de PHP a JavaScript.
         *
         * @return void
         */
        public function enqueue_preview_script() {
            // Encolar el archivo JS para la vista previa
            /*wp_enqueue_script(
                'wporlogin-customize-preview',
                WPORLOGIN_PRO_URL . 'assets/js/wporlogin-customize-preview.js',
                array( 'customize-preview' ),
                WPORLOGIN_PRO_VERSION,
                true
            );    */        
            
            if ( is_customize_preview() ) {
                
                // Fondo y diseño general
                wp_enqueue_script(
                    'wporlogin-customizer-background',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-background.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );
                
                // Sección: Autenticación
                wp_enqueue_script(
                    'wporlogin-customizer-authentication',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-authentication.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );
                // [CORRECCIÓN] Pasamos la variable 'wporloginParams' a ESTE script específico
                wp_localize_script( 
                    'wporlogin-customizer-authentication', 
                    'wporloginParams', 
                    array(
                        'adminUrl' => admin_url() // Esto devuelve ej: https://tusitio.com/wp-admin/
                    ) 
                );
                
                // Sección: Formulario de acceso
                wp_enqueue_script(
                    'wporlogin-customizer-form',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-form.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );
                
                // Encolar JS de campos de entrada
                wp_enqueue_script(
                    'wporlogin-customizer-input-fields',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-input-fields.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );

                // Encolar JS del botón de login
                wp_enqueue_script(
                    'wporlogin-customizer-button',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-button.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );
                
                // Encolar JS para enlaces y ajustes extras
                wp_enqueue_script(
                    'wporlogin-customizer-links',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-links.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );

                // Encolar JS para el selector de idioma
                wp_enqueue_script(
                    'wporlogin-customizer-language',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-language.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );

                // Encolar JS para la política de privacidad
                wp_enqueue_script(
                    'wporlogin-customizer-privacy',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-privacy.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );

                // Mensajes y avisos de error (personalizador)
                wp_enqueue_script(
                    'wporlogin-customizer-messages',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-messages.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );
                
                // Formulario de Registro
                wp_enqueue_script(
                    'wporlogin-customizer-register',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-register.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );
                
                // Formulario de Recuperación
                wp_enqueue_script(
                    'wporlogin-customizer-recovery',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-recovery.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );
                
                // Formulario de Confirmación de Email
                wp_enqueue_script(
                    'wporlogin-customizer-emailconfirm',
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-emailconfirm.js',
                    array('jquery', 'customize-preview'),
                    WPORLOGIN_VERSION,
                    true
                );

            }

            // Preparar valores predeterminados o guardados de diferentes secciones
            $settings = array(
                'bgColorLogin'       => get_option( 'wporlogin_bg_color_login', '#ffffff' ),
                'opacityLogin'       => get_option( 'wporlogin_login_opacity', '1' ),
                'bgColorForm'        => get_option( 'wporlogin_form_bg_color', '#ffffff' ),
                'opacityForm'        => get_option( 'wporlogin_form_opacity', '1' ),
                'bgColorRecovery'    => get_option( 'wporlogin_recovery_bg_color', '#ffffff' ),
                'opacityRecovery'    => get_option( 'wporlogin_recovery_opacity', '1' ),
                'bgColorRegister'    => get_option( 'wporlogin_register_bg_color', '#ffffff' ),
                'opacityRegister'    => get_option( 'wporlogin_register_opacity', '1' ),
                'bgColorEmailConfirm'=> get_option( 'wporlogin_emailconfirm_bg_color', '#ffffff' ),
                'opacityEmailConfirm'=> get_option( 'wporlogin_emailconfirm_opacity', '1' ),
            );

            // Pasar los datos al script de JavaScript
            wp_localize_script( 'wporlogin-customize-preview', 'wporloginSettings', $settings );
        }
    }

    // Inicializar la clase
    new WPORLogin_Customizer_Preview();
}