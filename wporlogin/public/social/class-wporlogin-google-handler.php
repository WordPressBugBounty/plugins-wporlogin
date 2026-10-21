<?php
/**
 * Google-only OAuth orchestration for WPOrLogin.
 *
 * Keeps the existing callback URL, browser state and profile mapping while
 * delegating WordPress account lookup and registration to the shared services.
 *
 * @package Wporlogin
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wporlogin-social-handler.php';
require_once __DIR__ . '/class-wporlogin-social-providers.php';
require_once __DIR__ . '/class-wporlogin-social-avatar.php';
require_once __DIR__ . '/class-wporlogin-social-identity.php';

class Wporlogin_Google_Handler implements Wporlogin_Social_Handler {
    /** @var string Internal destination recovered only from validated OAuth state. */
    private $google_requested_redirect = '';
    private $google_link_challenge = '';

    /** @return string Escaped Google button HTML. */
    public function render_button( $register = false ) {
        $provider = new Wporlogin_Social_Google(
            trim( (string) get_option( 'wporlogin_google_client_id', '' ) ),
            trim( (string) get_option( 'wporlogin_google_client_secret', '' ) )
        );
        return $provider->render_button( $register
            ? __( 'Register with Google', 'wporlogin' )
            : __( 'Sign in with Google', 'wporlogin' ) );
    }

    /** Handle only Google's existing start and callback URLs. */
    public function handle_request() {
        // Iniciar OAuth solo después del clic; no generar state en HTML cacheable.
        if ( isset( $_GET['wporlogin_social_start'] ) && 'google' === $_GET['wporlogin_social_start'] ) {
            $this->start_google_authorization();
            return;
        }

        if ( ! isset( $_GET['wporlogin_social_auth'] ) || 'google' !== $_GET['wporlogin_social_auth'] ) {
            return;
        }

        $this->disable_google_oauth_cache();

        // Recheck configuration on return as well as at the start of OAuth.
        if ( ! Wporlogin_Social_Providers::is_available( 'google' ) ) {
            $this->redirect_with_error( 'google_config' );
        }
        require_once __DIR__ . '/class-wporlogin-social-google.php';

        // Verificar y consumir state ANTES de usar el código que devuelve Google.
        if ( ! $this->consume_google_oauth_state() ) {
            $this->redirect_with_error( 'google_connect_fail' );
            return;
        }

        if ( isset( $_GET['error'] ) ) {
            $this->redirect_google_authentication_error( Wporlogin_Social_Google::classify_oauth_error(
                is_string( $_GET['error'] ) ? sanitize_key( wp_unslash( $_GET['error'] ) ) : ''
            ) );
        }
        if ( ! isset( $_GET['code'] ) || ! is_string( $_GET['code'] ) || '' === trim( $_GET['code'] ) ) {
            $this->redirect_with_error( 'google_connect_fail' );
        }

        $client_id = trim( (string) get_option( 'wporlogin_google_client_id', '' ) );
        $secret    = trim( (string) get_option( 'wporlogin_google_client_secret', '' ) );
        if ( ! $client_id || ! $secret ) {
            $this->redirect_with_error( 'google_config' );
            return;
        }

        $provider = new Wporlogin_Social_Google( $client_id, $secret );
        // Authorization codes are opaque credentials, not display text.
        $user_data = $provider->verify_token( wp_unslash( $_GET['code'] ) );

        // Google /userinfo provides a stable account ID. Email is NOT identity.
        // The shared account service resolves the WordPress account by ID/email.
        if ( is_array( $user_data ) && isset( $user_data['id'] ) && is_string( $user_data['id'] ) && '' !== $user_data['id'] ) {
            $this->login_or_register_user( $user_data );
            return;
        }
        $this->redirect_google_authentication_error( $provider->get_last_error() );
    }

    /** Map a safe protocol category to an existing, translatable login notice. */
    private function redirect_google_authentication_error( $category ) {
        $codes = array( 'configuration' => 'google_config', 'cancelled' => 'google_cancel' );
        $this->redirect_with_error( isset( $codes[ $category ] ) ? $codes[ $category ] : 'google_connect_fail' );
    }

    /**
     * Inicia una solicitud OAuth protegida. El state tiene 256 bits de entropía,
     * expira a los 10 minutos y se vincula al navegador mediante una cookie.
     *
     * @return void
     */
    private function start_google_authorization() {
        $this->disable_google_oauth_cache();

        if ( ! Wporlogin_Social_Providers::is_available( 'google' ) ) {
            $this->redirect_with_error( 'google_config' );
            return;
        }

        $client_id = trim( (string) get_option( 'wporlogin_google_client_id', '' ) );
        $secret    = trim( (string) get_option( 'wporlogin_google_client_secret', '' ) );
        if ( ! $client_id || ! $secret || headers_sent() ) {
            $this->redirect_with_error( 'google_config' );
            return;
        }

        require_once __DIR__ . '/class-wporlogin-social-google.php';

        try {
            $state = bin2hex( random_bytes( 32 ) );
        } catch ( Exception $exception ) {
            $this->redirect_with_error( 'google_connect_fail' );
            return;
        }

        $key = $this->get_google_oauth_transient_key( $state );
        // Guardar la URL legítima dentro del state de un solo uso. El callback
        // de Google nunca debe aceptar un redirect_to directamente desde GET.
        $requested_redirect = '';
        if ( isset( $_GET['redirect_to'] ) && is_string( $_GET['redirect_to'] ) ) {
            $requested_redirect = $this->sanitize_google_requested_redirect( wp_unslash( $_GET['redirect_to'] ) );
        }
        $link_challenge = Wporlogin_Social_Link::challenge_for_start();
        if ( is_wp_error( $link_challenge ) ) { $this->redirect_with_error( 'social_link_failed' ); }
        $state_value = array( 'redirect_to' => $requested_redirect, 'link' => $link_challenge );

        if ( ! set_transient( $key, $state_value, 10 * MINUTE_IN_SECONDS ) ) {
            $this->redirect_with_error( 'google_connect_fail' );
            return;
        }

        if ( ! $this->set_google_oauth_cookie( $state, time() + 10 * MINUTE_IN_SECONDS ) ) {
            delete_transient( $key );
            $this->redirect_with_error( 'google_connect_fail' );
            return;
        }

        $provider = new Wporlogin_Social_Google( $client_id, $secret );
        wp_redirect( $provider->get_google_authorization_url( $state ), 302, 'WPOrLogin' );
        exit;
    }

    /**
     * Comprueba cookie + state + transient y elimina el token de un solo uso.
     * Si no coinciden o expiraron, no se intercambia ningún código con Google.
     *
     * @return bool
     */
    private function consume_google_oauth_state() {
        $incoming = isset( $_GET['state'] ) && is_string( $_GET['state'] )
            ? sanitize_text_field( wp_unslash( $_GET['state'] ) )
            : '';
        $cookie = isset( $_COOKIE['wporlogin_google_oauth_state'] ) && is_string( $_COOKIE['wporlogin_google_oauth_state'] )
            ? sanitize_text_field( wp_unslash( $_COOKIE['wporlogin_google_oauth_state'] ) )
            : '';

        // Limpiar la cookie incluso si el flujo es rechazado.
        $this->set_google_oauth_cookie( '', time() - HOUR_IN_SECONDS );

        if (
            ! preg_match( '/\A[a-f0-9]{64}\z/', $incoming ) ||
            ! preg_match( '/\A[a-f0-9]{64}\z/', $cookie ) ||
            ! hash_equals( $cookie, $incoming )
        ) {
            return false;
        }

        $key = $this->get_google_oauth_transient_key( $incoming );
        $saved_state = get_transient( $key );
        if ( '1' !== $saved_state && ! is_array( $saved_state ) ) {
            return false;
        }

        // Consumir antes de autenticar; una respuesta no debe reutilizarse.
        delete_transient( $key );
        $this->google_requested_redirect = '';
        $this->google_link_challenge = isset( $saved_state['link'] ) && is_string( $saved_state['link'] ) ? $saved_state['link'] : '';
        if ( is_array( $saved_state ) && isset( $saved_state['redirect_to'] ) && is_string( $saved_state['redirect_to'] ) ) {
            $this->google_requested_redirect = $this->sanitize_google_requested_redirect( $saved_state['redirect_to'] );
        }
        return true;
    }

    /**
     * Autoriza solo destinos internos en el mismo dominio que la web o el
     * núcleo WordPress. Evita open redirects desde parámetros del visitante.
     *
     * @param string $requested_url URL solicitada antes del OAuth.
     * @return string URL interna validada, o cadena vacía.
     */
    private function sanitize_google_requested_redirect( $requested_url ) {
        if ( ! is_string( $requested_url ) || '' === $requested_url ) {
            return '';
        }

        $url = wp_validate_redirect( $requested_url, '' );
        if ( '' === $url || 0 === strpos( $url, '//' ) ) {
            return '';
        }

        $host = wp_parse_url( $url, PHP_URL_HOST );
        if ( ! $host ) {
            return $url; // Ruta relativa validada por WordPress.
        }

        $trusted_hosts = array(
            strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ),
            strtolower( (string) wp_parse_url( site_url(), PHP_URL_HOST ) ),
        );
        return in_array( strtolower( $host ), $trusted_hosts, true ) ? $url : '';
    }

    /**
     * @param string $state State aleatorio del flujo actual.
     * @return string Clave de transient estable y sin datos personales.
     */
    private function get_google_oauth_transient_key( $state ) {
        return 'wporlogin_google_state_' . hash( 'sha256', $state );
    }

    /**
     * Cookie HttpOnly + SameSite=Lax; Lax permite el retorno GET desde Google.
     * Usa la ruta de home_url para instalaciones en subdirectorios.
     *
     * @param string $value   State o cadena vacía para borrar.
     * @param int    $expires Marca de tiempo de expiración.
     * @return bool
     */
    private function set_google_oauth_cookie( $value, $expires ) {
        $path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
        return setcookie(
            'wporlogin_google_oauth_state',
            $value,
            [
                'expires'  => $expires,
                'path'     => $path ? $path : '/',
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    /**
     * Evita caché de respuestas que generan o consumen credenciales temporales.
     */
    private function disable_google_oauth_cache() {
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }
        nocache_headers();
    }

    /**
     * Helper privado para redirigir con error (DRY - Don't Repeat Yourself)
     */
    private function redirect_with_error( $error_code ) {
        $login_url = wp_login_url();
        $redirect_url = add_query_arg( 'wporlogin_error', $error_code, $login_url );
        wp_safe_redirect( $redirect_url );
        exit;
    }

    /**
     * Map Google userinfo to the shared WordPress account service.
     */
    private function login_or_register_user( $social_user ) {
        $profile = array(
            'id'             => (string) $social_user['id'],
            'email_verified' => true === ( $social_user['verified_email'] ?? false ),
            'email'          => isset( $social_user['email'] ) ? $social_user['email'] : '',
            'name'           => isset( $social_user['name'] ) ? $social_user['name'] : '',
            'first_name'     => isset( $social_user['given_name'] ) ? $social_user['given_name'] : '',
            'last_name'      => isset( $social_user['family_name'] ) ? $social_user['family_name'] : '',
            'picture'        => isset( $social_user['picture'] ) ? $social_user['picture'] : '',
        );
        if ( '' !== $this->google_link_challenge ) {
            $confirmed = Wporlogin_Social_Link::confirm_with_provider( 'google', $profile, $this->google_link_challenge );
            if ( is_wp_error( $confirmed ) ) { $this->redirect_with_error( 'social_link_failed' ); }
            Wporlogin_Social_Link::finish_social_confirmation( $confirmed );
            $this->redirect_with_error( 'social_link_failed' );
        }
        $user = Wporlogin_Social_Identity::resolve_google_by_email( $profile );
        if ( is_wp_error( $user ) && 'wporlogin_link_confirmation_required' === $user->get_error_code() ) {
            $begun = Wporlogin_Social_Link::begin( 'google', $profile, $this->google_requested_redirect );
            $this->redirect_with_error( Wporlogin_Social_Identity::notice_for_error( $begun ) );
        }
        if ( is_wp_error( $user ) || ! is_a( $user, 'WP_User' ) ) {
            $this->redirect_with_error( Wporlogin_Social_Identity::notice_for_error( $user ) );
        }
        $this->complete_verified_login( $user, $profile, $this->google_requested_redirect );
    }

    public function complete_verified_login( $user, $profile, $requested_redirect ) {
        Wporlogin_Social_Avatar::update_on_login( $user->ID, 'google', $profile['picture'] );
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID );
        do_action( 'wp_login', $user->user_login, $user );

        $destination = home_url( '/' );
        if ( class_exists( 'Wporlogin_Public_Redirects' ) ) {
            $destination = Wporlogin_Public_Redirects::get_calculated_redirect_url( $user, $requested_redirect );
        }
        wp_safe_redirect( $destination );
        exit;
    }

}
