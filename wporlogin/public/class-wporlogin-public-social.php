<?php
class Wporlogin_Public_Social {

    /** @var Wporlogin_Social_Handler[] Independent handlers keyed by provider. */
    private $handlers = array();

    /** Keep the existing constructor signature used by the plugin bootstrap. */
    public function __construct( $plugin_name, $version ) {
        require_once __DIR__ . '/social/class-wporlogin-social-providers.php';
        require_once __DIR__ . '/social/class-wporlogin-social-avatar.php';
        require_once __DIR__ . '/social/class-wporlogin-social-identity.php';
        // Native password events may make social-created users eligible for
        // WordPress password confirmation in future link requests.
        add_action( 'after_password_reset', array( 'Wporlogin_Social_Identity', 'after_password_reset' ) );
        add_action( 'wp_set_password', array( 'Wporlogin_Social_Identity', 'after_wp_set_password' ), 10, 2 );
        add_action( 'profile_update', array( 'Wporlogin_Social_Identity', 'profile_updated' ), 10, 2 );
        require_once __DIR__ . '/social/class-wporlogin-social-link.php';

        require_once __DIR__ . '/social/class-wporlogin-social-handler.php';

        // No provider requires another provider's files or credentials.
        $modules = array(
            'google'    => 'Wporlogin_Google_Handler',
            'facebook'  => 'Wporlogin_Facebook_Handler',
            'microsoft' => 'Wporlogin_Microsoft_Handler',
        );
        foreach ( $modules as $provider => $class ) {
            $adapter = __DIR__ . '/social/class-wporlogin-social-' . $provider . '.php';
            $handler = __DIR__ . '/social/class-wporlogin-' . $provider . '-handler.php';
            if ( ! is_readable( $adapter ) || ! is_readable( $handler ) ) {
                continue;
            }
            require_once $adapter;
            require_once $handler;
            $this->handlers[ $provider ] = new $class();
        }

        // Microsoft's REST return endpoint is specific to that module.
        if ( isset( $this->handlers['microsoft'] ) ) {
            $this->handlers['microsoft']->register_rest_hooks();
        }
    }

    /** @return bool Whether any social provider is ready for login. */
    private function has_social_provider() {
        foreach ( $this->handlers as $provider => $handler ) {
            if ( Wporlogin_Social_Providers::is_available( $provider ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * SOLO CSS: Se carga en el HEAD.
     * Ocultamos el contenedor (.wporlogin-social-container) inicialmente con display:none
     * para que no parpadee en el lugar incorrecto.
     */
    public function enqueue_social_styles() {
        Wporlogin_Social_Link::render_styles();
        if ( ! $this->has_social_provider() && ! Wporlogin_Social_Link::has_pending() ) return;
        ?>
        <style type="text/css">
            .wporlogin-social-container {
                padding-top: 10px !important;
                text-align: center;
                flex-direction: column;
                gap: 15px;
                align-items: center;
                width: 100%;
                clear: both;
            }
            .wporlogin-social-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background-color: #ffffff;
                color: #757575;
                border: 1px solid #dadce0;
                border-radius: 4px;
                font-family: 'Roboto', arial, sans-serif;
                font-size: 14px;
                font-weight: 500;
                text-decoration: none;
                width: 100%;
                height: 40px;
                cursor: pointer;
                transition: background-color .218s, border-color .218s;
                position: relative;
            }
            .wporlogin-social-btn:hover {
                background-color: #f7fafe;
                border-color: #d2e3fc;
                color: #3c4043;
            }
            .wporlogin-social-icon-wrapper {
                width: 38px;
                height: 38px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: absolute;
                left: 1px;
                top: 0;
            }
            .wporlogin-social-text {
                padding-left: 20px;
            }
            .wporlogin-facebook-btn {
                background: #1877f2;
                border-color: #1877f2;
                color: #fff;
            }
            .wporlogin-facebook-btn:hover,
            .wporlogin-facebook-btn:focus {
                background: #1465cf;
                border-color: #1465cf;
                color: #fff;
            }
            .wporlogin-facebook-btn .wporlogin-social-icon-wrapper {
                color: #fff;
            }
            .wporlogin-microsoft-btn {
                background-color: #ffffff;
                border-color: #d2d5da;
                color: #242424;
            }
            .wporlogin-microsoft-btn:hover,
            .wporlogin-microsoft-btn:focus {
                background-color: #f5f5f5;
                border-color: #aeb5bf;
                color: #242424;
            }
            .wporlogin-social-btn:focus-visible {
                outline: 2px solid #2271b1;
                outline-offset: 3px;
            }
            .wporlogin-separator {
                width: 100%;
                display: flex;
                align-items: center;
                text-align: center;
                color: #8c8f94;
                font-size: 12px;
                text-transform: uppercase;
            }
            .wporlogin-separator::before, .wporlogin-separator::after {
                content: '';
                flex: 1;
                border-bottom: 1px solid #ddd;
            }
            .wporlogin-separator::before { margin-right: 10px; }
            .wporlogin-separator::after { margin-left: 10px; }
        </style>
        <?php
    }

    /**
     * SOLO JS: Se carga en el FOOTER.
     * VERSIÓN FINAL: Soporta Login (#loginform) y Registro (#registerform).
     */
    public function output_social_footer_script() {
        if ( Wporlogin_Social_Link::has_pending() ) {
            // On the password branch, put the instructions before credentials.
            // Without JavaScript the form remains functional and styled.
            if ( Wporlogin_Social_Link::password_mode() ) {
                ?>
                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var form = document.getElementById('loginform');
                    var confirmation = form && form.querySelector('.wporlogin-link-confirm');
                    if (confirmation) form.insertBefore(confirmation, form.firstChild);
                });
                </script>
                <?php
            }
            return;
        }
        if ( ! $this->has_social_provider() ) return;
        ?>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function(event) {
                // Seleccionamos el bloque social
                var socialBlock = document.querySelector('.wporlogin-social-container');
                
                // Seleccionamos AMBOS formularios posibles
                var loginForm    = document.getElementById('loginform');
                var registerForm = document.getElementById('registerform');

                // Determinamos cuál usar (el que exista en la página actual)
                var targetForm = loginForm || registerForm;

                if (socialBlock && targetForm) {
                    // Mover al final del formulario detectado
                    targetForm.appendChild(socialBlock);
                    
                    // Hacer visible
                    socialBlock.style.display = 'flex';
                } else {
                    // Fallback (por si el tema usa IDs raros)
                    if (socialBlock) {
                        socialBlock.style.display = 'flex';
                    }
                }
            });
        </script>
        <?php
    }

    /**
     * Muestra los botones. 
     * MEJORA: Oculta el botón en la página de Registro si el registro global está desactivado.
     */
    public function render_social_buttons() {
        // Only linked methods are allowed during a pending confirmation.
        // They are presented by the separate account-link guidance above.
        if ( Wporlogin_Social_Link::has_pending() ) { return; }
        if ( ! $this->has_social_provider() ) {
            return;
        }

        $action = isset( $_GET['action'] ) && is_string( $_GET['action'] )
            ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'login';
        if ( 'register' === $action && ! get_option( 'users_can_register' ) ) {
            return;
        }

        $buttons = array();
        foreach ( $this->handlers as $provider => $handler ) {
            if ( Wporlogin_Social_Providers::is_available( $provider ) ) {
                $buttons[] = $handler->render_button( 'register' === $action );
            }
        }

        if ( ! $buttons ) {
            return;
        }
        echo '<div class="wporlogin-social-container">';
        echo '<div class="wporlogin-separator">' . esc_html__( 'OR', 'wporlogin' ) . '</div>';
        echo wp_kses( implode( "\n", $buttons ), self::allowed_social_markup() ); // Preserve the provider SVG icons.
        echo '</div>';
    }

    /**
     * Allow only the elements generated by the three built-in providers and
     * the account-confirmation view, including their branded inline SVG icons.
     * This does not alter the provider URLs or the confirmation workflow.
     */
    private static function allowed_social_markup() {
        return array(
            'a'    => array( 'href' => true, 'class' => true ),
            'div'  => array( 'class' => true, 'role' => true, 'aria-labelledby' => true ),
            'h2'   => array( 'class' => true, 'id' => true ),
            'p'    => array( 'class' => true ),
            'span' => array( 'class' => true, 'aria-hidden' => true ),
            'svg'  => array(
                'aria-hidden' => true, 'focusable' => true, 'xmlns' => true,
                'version' => true, 'width' => true, 'height' => true,
                'viewbox' => true, 'class' => true, 'fill' => true,
                'stroke' => true, 'stroke-width' => true,
                'stroke-linecap' => true, 'stroke-linejoin' => true,
            ),
            'g'    => array(),
            'path' => array( 'fill' => true, 'd' => true ),
            'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
        );
    }

    /** Dispatch the existing init hook to each independent provider module. */
    public function handle_social_callback() {
        Wporlogin_Social_Link::handle_finish();
        foreach ( $this->handlers as $handler ) {
            $handler->handle_request();
        }
    }

    /** Preserve only expiration notices above the login form. */
    public function render_link_confirmation( $message ) {
        return Wporlogin_Social_Link::expired_message( $message );
    }

    /** Confirmation options are children of the existing styled login form. */
    public function render_link_inside_form() {
        echo wp_kses( Wporlogin_Social_Link::render_confirmation(), self::allowed_social_markup() ); // Preserve confirmation links and icons.
    }

    public function link_body_class( $classes ) {
        return Wporlogin_Social_Link::login_body_class( $classes );
    }

    public function preserve_link_redirect( $redirect, $requested, $user ) {
        return Wporlogin_Social_Link::preserve_password_redirect( $redirect, $requested, $user );
    }

    /**
     * Muestra mensajes de error personalizados.
     * Hook: wp_login_errors
     */
    public function custom_login_error_message( $errors ) {
        
        if ( ! isset( $_GET['wporlogin_error'] ) || ! is_string( $_GET['wporlogin_error'] ) ) return $errors;

        $error_code = sanitize_key( wp_unslash( $_GET['wporlogin_error'] ) );
        $message    = '';

        $social_notices = array(
            'social_email_missing' => __( 'The provider did not supply a usable email address. Please use your usual sign-in method or contact the site administrator.', 'wporlogin' ),
            'social_identity_conflict' => __( 'This external account could not be connected because its association conflicts with an existing account. Please use your usual sign-in method or contact the site administrator.', 'wporlogin' ),
            'social_link_confirmation_required' => __( 'An account already uses this email address. Sign in with an existing method to confirm your identity before connecting another provider.', 'wporlogin' ),
            'social_link_failed' => __( 'Account linking could not be completed. Confirm that you signed in to the original account and try again.', 'wporlogin' ),
            'social_email_unverified' => __( 'Google has not confirmed this email address. Sign in using another method or verify your address with Google.', 'wporlogin' ),
            'social_unavailable' => __( 'This sign-in provider is currently unavailable. Please use another sign-in method.', 'wporlogin' ),
            'social_busy' => __( 'Another sign-in request is being processed. Please wait a moment and try again.', 'wporlogin' ),
            'social_locked' => __( 'Sign-in is temporarily blocked because too many attempts were made. Please try again later.', 'wporlogin' ),
            'social_access_failed' => __( 'Sign-in could not be completed. Please use your usual sign-in method or contact the site administrator.', 'wporlogin' ),
        );
        if ( isset( $social_notices[ $error_code ] ) ) {
            $errors->add( 'wporlogin_social_error', $social_notices[ $error_code ] );
            return $errors;
        }

        switch ( $error_code ) {
            case 'account_not_found':
                $message = __( '<strong>Notice</strong>: No matching account was found and new registrations are disabled. Please sign in with your existing account or contact the site administrator.', 'wporlogin' );
                break;
            
            case 'facebook_cancel':
                $message = __( '<strong>Notice</strong>: Facebook login was cancelled.', 'wporlogin' );
                break;
            case 'facebook_config':
                $message = __( '<strong>System Error</strong>: Facebook Login is not configured correctly. Please contact the site administrator.', 'wporlogin' );
                break;
            case 'facebook_connection':
                $message = __( '<strong>Connection Error</strong>: Could not verify your Facebook account. Please try again.', 'wporlogin' );
                break;
            case 'microsoft_cancel':
                $message = __( '<strong>Notice</strong>: Microsoft login was cancelled.', 'wporlogin' );
                break;
            case 'microsoft_config':
                $message = __( '<strong>System Error</strong>: Microsoft Login is not configured correctly. Please contact the site administrator.', 'wporlogin' );
                break;
            case 'microsoft_connection':
                $message = __( '<strong>Connection Error</strong>: Could not verify your Microsoft account. Please try again.', 'wporlogin' );
                break;
            case 'google_cancel':
                $message = __( '<strong>Notice</strong>: Google login cancelled or denied.', 'wporlogin' );
                break;

            case 'google_config':
                $message = __( '<strong>System Error</strong>: Google Login is not configured correctly. Please contact the site administrator.', 'wporlogin' );
                break;

            case 'google_connect_fail':
                $message = __( '<strong>Connection Error</strong>: Could not verify your Google account. Please try again or use your password.', 'wporlogin' );
                break;
        }

        if ( ! empty( $message ) ) {
            $errors->add( 'wporlogin_social_error', $message );
        }
        
        return $errors;
    }
}
