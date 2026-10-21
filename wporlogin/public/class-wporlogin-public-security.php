<?php
/**
 * CAPTCHA protection for the three native WordPress login forms.
 *
 * Keeps the existing WPOrLogin options and WordPress hooks. A valid token is
 * checked server-side before login, registration or password recovery proceeds.
 *
 * @package Wporlogin
 * @subpackage Wporlogin/public
 */
class Wporlogin_Public_Security {

    /** @var string Plugin slug. */
    private $plugin_name;

    /** @var string Plugin version. */
    private $version;

    /**
     * @param string $plugin_name Plugin slug.
     * @param string $version     Plugin version.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Check the existing per-form activation option (also used by v3).
     *
     * @param string $context login, register or lostpassword.
     * @return bool
     */
    private function is_enabled_for( $context ) {
        $options = array(
            'login'        => 'activa_acceso_recaptcha_v2_wporlogin',
            'register'     => 'activa_registro_recaptcha_v2_wporlogin',
            'lostpassword' => 'activa_recuperar_recaptcha_wporlogin',
        );

        return isset( $options[ $context ] ) && '1' === (string) get_option( $options[ $context ], '0' );
    }

    /**
     * Use the saved provider. The legacy v2 flag is consulted only if the
     * modern selection is absent: an explicit Disabled choice must win.
     *
     * @return string none|v2|v3|turnstile
     */
    private function selected_version() {
        $version = get_option( 'recaptcha_version_wporlogin', false );

        if ( false === $version ) {
            $version = '1' === (string) get_option( 'recaptcha_v2_wporlogin', '0' )
                ? 'v2' : 'none';
        }

        return in_array( $version, array( 'none', 'v2', 'v3', 'turnstile' ), true ) ? $version : 'none';
    }

    /**
     * Fetch a usable configuration; do not treat missing credentials as a
     * successful reCAPTCHA check. The incomplete state is handled explicitly.
     *
     * @return array|false
     */
    private function obtener_configuracion() {
        $version = $this->selected_version();

        if ( 'none' === $version ) {
            return false;
        }

        // Cloudflare has its own credentials; Google option names stay intact.
        $site_key_option   = 'turnstile' === $version
            ? 'wporlogin_turnstile_site_key' : 'recaptcha_' . $version . '_site_key_wporlogin';
        $secret_key_option = 'turnstile' === $version
            ? 'wporlogin_turnstile_secret_key' : 'recaptcha_' . $version . '_secret_key_wporlogin';
        $site_key          = trim( (string) get_option( $site_key_option, '' ) );
        $secret_key        = trim( (string) get_option( $secret_key_option, '' ) );

        if ( '' === $site_key || '' === $secret_key ) {
            return false;
        }

        return array(
            'version' => $version,
            'site'    => $site_key,
            'secret'  => $secret_key,
        );
    }

    /**
     * Emergency administrative opt-out if the selected service becomes unreachable.
     * Only the server owner can set this in wp-config.php; no browser input is read.
     *
     * @return bool
     */
    private function is_emergency_disabled() {
        return defined( 'WPORLOGIN_DISABLE_RECAPTCHA' ) && true === WPORLOGIN_DISABLE_RECAPTCHA;
    }

    /**
     * Whether CAPTCHA can be enforced for a selected form.
     * An incomplete config is left unenforced to preserve existing site access;
     * the administrative warning identifies the missing credentials.
     *
     * @param string $context Form action.
     * @return bool
     */
    private function should_validate( $context ) {
        return $this->is_enabled_for( $context )
            && ! $this->is_emergency_disabled()
            && false !== $this->obtener_configuracion();
    }

    /**
     * Load only the selected provider and only on its protected native form.
     * Turnstile renders itself; Google reuses the existing versioned client.
     */
    public function enqueue_recaptcha_scripts() {
        $config = $this->obtener_configuracion();
        if ( false === $config || $this->is_emergency_disabled() ) {
            return;
        }

        // Both services must obey the same per-form selection. Loading an API
        // on logout, password reset, or an unprotected form adds unnecessary
        // requests and can conflict with unrelated login-page extensions.
        if ( ! $this->is_enabled_for( $this->current_login_context() ) ) {
            return;
        }

        if ( 'turnstile' === $config['version'] ) {
            wp_enqueue_script(
                'wporlogin-turnstile',
                'https://challenges.cloudflare.com/turnstile/v0/api.js',
                array(),
                null,
                false
            );
            wp_script_add_data( 'wporlogin-turnstile', 'strategy', 'defer' );
            return;
        }

        $client_handle = 'wporlogin-recaptcha-client';
        wp_register_script(
            $client_handle,
            WPORLOGIN_URL . 'assets/js/wporlogin-recaptcha.js',
            array(),
            $this->version,
            false
        );

        $client_settings = array(
            'version' => $config['version'],
            'siteKey' => $config['site'],
            'error'   => __( 'reCAPTCHA could not be loaded. Please try again.', 'wporlogin' ),
        );

        wp_add_inline_script(
            $client_handle,
            'window.wporloginRecaptchaSettings = ' . wp_json_encode( $client_settings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';',
            'before'
        );
        wp_enqueue_script( $client_handle );

        $query = 'v2' === $config['version']
            ? 'onload=wporloginRecaptchaOnload&render=explicit'
            : 'render=' . rawurlencode( $config['site'] );

        // External script depends on the local controller so its callback exists.
        wp_enqueue_script(
            'wporlogin-recaptcha-google',
            'https://www.google.com/recaptcha/api.js?' . $query,
            array( $client_handle ),
            null,
            false
        );
    }

    /**
     * Determine which native wp-login.php form is being displayed.
     * Other actions do not need either provider's client scripts.
     *
     * @return string login|register|lostpassword|other
     */
    private function current_login_context() {
        $action = isset( $GLOBALS['action'] ) && is_string( $GLOBALS['action'] )
            ? $GLOBALS['action'] : 'login';

        if ( 'register' === $action ) {
            return 'register';
        }
        if ( in_array( $action, array( 'lostpassword', 'retrievepassword' ), true ) ) {
            return 'lostpassword';
        }
        return 'login' === $action ? 'login' : 'other';
    }

    /**
     * Render markup for the selected CAPTCHA provider inside its native form.
     * Cloudflare creates the cf-turnstile-response input automatically.
     *
     * @param string $context login|register|lostpassword.
     */
    private function imprimir_html_recaptcha( $context ) {
        if ( ! $this->should_validate( $context ) ) {
            return;
        }

        $config = $this->obtener_configuracion();
        if ( 'turnstile' === $config['version'] ) {
            printf(
                '<div class="cf-turnstile wporlogin-turnstile" data-sitekey="%s" data-action="%s" data-size="flexible" data-theme="auto" style="max-width:100%%;margin-bottom:12px;"></div>',
                esc_attr( $config['site'] ),
                esc_attr( $context )
            );
            return;
        }

        if ( 'v2' === $config['version'] ) {
            printf(
                '<div class="wporlogin-recaptcha-v2" data-wporlogin-recaptcha="v2" data-context="%s" style="margin-bottom:10px;"></div>',
                esc_attr( $context )
            );
            return;
        }

        printf(
            '<input type="hidden" name="wporlogin-g-recaptcha-response" value="" data-wporlogin-recaptcha="v3" data-context="%s" />',
            esc_attr( $context )
        );
    }

    /**
     * Normalized approved domains for validation (without a port or scheme).
     * Use trusted WordPress settings, not the HTTP Host header from the request.
     *
     * @return string[]
     */
    private function allowed_hostnames() {
        $hosts = array();

        foreach ( array( home_url(), site_url() ) as $url ) {
            $host = wp_parse_url( $url, PHP_URL_HOST );
            if ( is_string( $host ) && '' !== $host ) {
                $hosts[] = strtolower( rtrim( $host, '.' ) );
            }
        }

        /**
         * Permit explicitly trusted additional login hostnames in domain-mapped
         * installations. Filter values must be controlled by site administrators.
         *
         * @param string[] $hosts Allowed DNS hostnames.
         */
        $hosts = apply_filters( 'wporlogin_recaptcha_allowed_hostnames', array_unique( $hosts ) );
        if ( ! is_array( $hosts ) ) {
            return array();
        }

        return array_values( array_unique( array_filter( array_map( static function ( $host ) {
            return is_string( $host ) ? strtolower( rtrim( trim( $host ), '.' ) ) : '';
        }, $hosts ) ) ) );
    }

    /**
     * Server-side validation. Both versions require success and matching host;
     * v3 additionally requires matching action and a valid score.
     *
     * @param string $context Expected form action.
     * @return true|WP_Error
     */
    private function validar_respuesta_google( $context ) {
        $config = $this->obtener_configuracion();
        if ( false === $config ) {
            return new WP_Error( 'recaptcha_not_configured', __( 'reCAPTCHA is not configured.', 'wporlogin' ) );
        }

        // The v3 field is intentionally distinct from the standard v2 field.
        $field = 'v3' === $config['version'] ? 'wporlogin-g-recaptcha-response' : 'g-recaptcha-response';
        $token = isset( $_POST[ $field ] ) && is_string( $_POST[ $field ] )
            ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) )
            : '';

        if ( '' === $token ) {
            return new WP_Error( 'invalid_captcha', __( 'Please complete the reCAPTCHA.', 'wporlogin' ) );
        }

        $response = wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            array(
                'body'    => array(
                    'secret'   => $config['secret'],
                    'response' => $token,
                ),
                'timeout' => 10,
            )
        );

        if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            return new WP_Error( 'recaptcha_error', __( 'Connection error with reCAPTCHA.', 'wporlogin' ) );
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $data ) || true !== ( $data['success'] ?? false ) ) {
            return new WP_Error( 'invalid_captcha', __( 'reCAPTCHA verification failed.', 'wporlogin' ) );
        }

        // Reject Google's documented fail-open quota response when reported.
        $errors = isset( $data['error-codes'] ) && is_array( $data['error-codes'] ) ? $data['error-codes'] : array();
        if ( in_array( 'Over free quota.', $errors, true ) ) {
            return new WP_Error( 'recaptcha_quota', __( 'reCAPTCHA verification is temporarily unavailable.', 'wporlogin' ) );
        }

        $hostname = isset( $data['hostname'] ) && is_string( $data['hostname'] )
            ? strtolower( rtrim( trim( $data['hostname'] ), '.' ) )
            : '';
        if ( '' === $hostname || ! in_array( $hostname, $this->allowed_hostnames(), true ) ) {
            return new WP_Error( 'invalid_captcha_hostname', __( 'reCAPTCHA domain verification failed.', 'wporlogin' ) );
        }

        if ( 'v3' === $config['version'] ) {
            if ( ! isset( $data['action'] ) || ! is_string( $data['action'] ) || $context !== $data['action'] ) {
                return new WP_Error( 'invalid_captcha_action', __( 'reCAPTCHA action verification failed.', 'wporlogin' ) );
            }

            if ( ! isset( $data['score'] ) || ! is_numeric( $data['score'] ) ) {
                return new WP_Error( 'invalid_captcha_score', __( 'reCAPTCHA score is missing or invalid.', 'wporlogin' ) );
            }

            $score = (float) $data['score'];
            if ( ! is_finite( $score ) || $score < 0.0 || $score > 1.0 ) {
                return new WP_Error( 'invalid_captcha_score', __( 'reCAPTCHA score is missing or invalid.', 'wporlogin' ) );
            }

            if ( $score < 0.5 ) {
                return new WP_Error( 'low_recaptcha_score', __( 'Security Score too low (Bot detected).', 'wporlogin' ) );
            }
        }

        return true;
    }

    /**
     * Validate against the selected service. Only the chosen provider receives
     * the token, and the secret key never enters the generated HTML.
     *
     * @param string $context Expected native form action.
     * @return true|WP_Error
     */
    private function validate_selected_captcha( $context ) {
        if ( 'turnstile' !== $this->selected_version() ) {
            return $this->validar_respuesta_google( $context );
        }

        $token = isset( $_POST['cf-turnstile-response'] ) && is_string( $_POST['cf-turnstile-response'] )
            ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) )
            : '';

        // Load the small verifier only when a Turnstile submission is checked.
        require_once __DIR__ . '/class-wporlogin-turnstile-verifier.php';
        $verifier = new Wporlogin_Turnstile_Verifier();
        return $verifier->verify( $token, $context );
    }

    /** Show the selected CAPTCHA on the standard login form. */
    public function render_login_recaptcha() {
        $this->imprimir_html_recaptcha( 'login' );
    }

    /**
     * Validate the interactive login POST, including attempts with unknown users.
     *
     * Unlike wp_authenticate_user, authenticate also sees unknown usernames.
     * Skip XML-RPC, REST and programmatic wp_signon() because no reCAPTCHA field
     * is rendered in those entry points; other login filters remain untouched.
     *
     * @param WP_User|WP_Error|null $user     Existing authentication result.
     * @param string              $username Username or email address.
     * @param string              $password Password (never logged).
     * @return WP_User|WP_Error|null
     */
    public function verify_login_recaptcha( $user, $username = '', $password = '' ) {
        if ( ! $this->should_validate( 'login' ) ) {
            return $user;
        }

        if ( 'POST' !== strtoupper( isset( $_SERVER['REQUEST_METHOD'] ) ? (string) $_SERVER['REQUEST_METHOD'] : '' ) ) {
            return $user;
        }

        // WordPress executes login_init only in the interactive wp-login flow.
        if ( 0 === did_action( 'login_init' ) ) {
            return $user;
        }

        $result = $this->validate_selected_captcha( 'login' );
        return is_wp_error( $result ) ? $result : $user;
    }

    /** Show reCAPTCHA on the standard registration form. */
    public function render_register_recaptcha() {
        $this->imprimir_html_recaptcha( 'register' );
    }

    /** Check registration without changing existing WP_Error handling. */
    public function verify_register_recaptcha( $errors, $sanitized_user_login, $user_email ) {
        if ( ! $this->should_validate( 'register' ) ) {
            return $errors;
        }

        $result = $this->validate_selected_captcha( 'register' );
        if ( is_wp_error( $result ) ) {
            $errors->add( $result->get_error_code(), $result->get_error_message() );
        }

        return $errors;
    }

    /** Show reCAPTCHA on the standard password recovery form. */
    public function render_lostpassword_recaptcha() {
        $this->imprimir_html_recaptcha( 'lostpassword' );
    }

    /** Check password recovery while preserving the incoming WP_Error. */
    public function verify_lostpassword_recaptcha( $errors ) {
        if ( ! $this->should_validate( 'lostpassword' ) ) {
            return $errors;
        }

        $result = $this->validate_selected_captcha( 'lostpassword' );
        if ( is_wp_error( $result ) ) {
            if ( is_wp_error( $errors ) ) {
                $errors->add( $result->get_error_code(), $result->get_error_message() );
            } else {
                return $result;
            }
        }

        return $errors;
    }
}
