<?php
/**
 * Manejador para restringir el registro únicamente a Social Login.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/public/social
 */
class Wporlogin_Social_Only_Register {

    private $option_name = 'wporlogin_social_only_register';

    public function init() {
        // 1. Verificación temprana: Si la opción no está activa, no cargamos hooks.
        if ( get_option( $this->option_name ) !== '1' ) {
            return;
        }

        // 2. CSS para ocultar campos visualmente (UX)
        add_action( 'login_enqueue_scripts', array( $this, 'hide_native_form_css' ) );

        // 3. Seguridad: Bloquear peticiones POST nativas (evita bots)
        add_filter( 'registration_errors', array( $this, 'block_native_registration_attempts' ), 10, 3 );
        
        // 4. Mensaje informativo (Opcional: mejora UX)
        //add_action( 'register_form', array( $this, 'render_social_only_message' ), 5 );
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
     * Renderiza un mensaje indicando que el registro es solo vía social.
     */
    public function render_social_only_message() {
        ?>
        <div class="message wporlogin-social-only-msg" style="margin-bottom: 20px; border-left: 4px solid #72aee6; background: #fff; padding: 12px;">
            <?php _e( 'Registration is only available via Social Login.', 'wporlogin' ); ?>
        </div>
        <?php
    }

    /**
     * Bloquea el proceso de registro nativo de WordPress.
     * * Nota: Tu flujo de Social Login usa wp_create_user() directamente, 
     * por lo que NO dispara este hook y el registro social funcionará correctamente.
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