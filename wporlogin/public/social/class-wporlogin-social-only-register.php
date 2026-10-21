<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wporlogin-social-providers.php';
/**
 * Manejador para restringir el registro únicamente a Social Login.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/public/social
 */
class Wporlogin_Social_Only_Register {

    private $option_name = 'wporlogin_social_only_register';

    /**
     * Restrict registration only when at least one provider is enabled and
     * has credentials saved. The setting remains stored even if providers are
     * switched off. WordPress controls whether registration is open at all.
     *
     * @return void
     */
    public function init() {
        // Provider availability can depend on a pretty REST callback. WordPress
        // must finish loading its rewrite infrastructure before we check it.
        if ( ! did_action( 'init' ) ) {
            add_action( 'init', array( $this, 'init' ), 11 );
            return;
        }
        if ( '1' !== get_option( $this->option_name ) || ! $this->has_available_provider() ) {
            return;
        }

        add_action( 'login_enqueue_scripts', array( $this, 'hide_native_form_css' ) );
        add_filter( 'registration_errors', array( $this, 'block_native_registration_attempts' ), 10, 3 );
    }

    /**
     * Consult the common provider registry, shared with the button renderer.
     * A provider is locally configured, not remotely verified by Meta/Google.
     *
     * @return bool
     */
    private function has_available_provider() {
        return Wporlogin_Social_Providers::has_available();
    }

    /**
     * Oculta los campos de input y el botón de registro nativo mediante CSS.
     * Solo afecta a la acción 'register'.
     */
    public function hide_native_form_css() {
        // Validamos que estemos en la pantalla de registro
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'register' ) {
            ?>
            <style type="text/css">
                /* Ocultamos etiquetas, inputs y el botón de envío nativo */
                #registerform > p,
                #registerform .user-pass1-wrap,
                #registerform .submit {
                    display: none !important;
                }

                /* Aseguramos que el contenedor social (que es un div) siga visible */
                #registerform .wporlogin-social-container {
                    display: flex !important;
                    margin-top: 0 !important;
                    padding-top: 0 !important;
                }

                /* Ocultar el separador "OR" ya que no hay opción alternativa */
                .wporlogin-separator {
                    display: none !important;
                }
            </style>
            <?php
        }
    }

    /**
     * Bloquea el proceso de registro nativo de WordPress.
     * Social registration uses the shared identity service and wp_insert_user(),
     * after checking the registration setting and account ownership. It does
     * not pass through this native registration_errors filter.
     *
     * @param WP_Error $errors Objeto de errores.
     * @param string   $sanitized_user_login Login del usuario.
     * @param string   $user_email Email del usuario.
     * @return WP_Error
     */
    public function block_native_registration_attempts( $errors, $sanitized_user_login, $user_email ) {
        // Si llegamos aquí, es porque alguien envió el formulario nativo (humano o bot)
        $errors->add( 
            'social_only_error', 
            __( '<strong>Error</strong>: Manual registration is disabled. Please use the Social Login buttons below.', 'wporlogin' ) 
        );
        
        return $errors;
    }
}
