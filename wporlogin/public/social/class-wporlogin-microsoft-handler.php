<?php
/**
 * Standalone Microsoft OAuth/OIDC flow for WPOrLogin.
 * Microsoft OAuth identity is validated by Entra; WordPress users are resolved
 * by saved Microsoft ID or email, using native registration settings.
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

class Wporlogin_Microsoft_Handler implements Wporlogin_Social_Handler {
    const COOKIE_NAME = 'wporlogin_microsoft_oauth_state';
    const META_KEY    = 'wporlogin_microsoft_id';
    const AVATAR_FILE_META = 'wporlogin_microsoft_avatar_file';
    const STATE_TTL   = 600;

    private function is_available() {
        return class_exists( 'Wporlogin_Social_Microsoft', false )
            && Wporlogin_Social_Providers::is_available( 'microsoft' );
    }

    /** @return string Escaped Microsoft button HTML. */
    public function render_button( $register = false ) {
        return $this->get_provider()->render_button( $register
            ? __( 'Register with Microsoft', 'wporlogin' )
            : __( 'Sign in with Microsoft', 'wporlogin' ) );
    }

    /** Register the OAuth reply endpoint on WordPress's public REST API. */
    public function register_rest_hooks() {
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    public function register_rest_routes() {
        register_rest_route( 'wporlogin/v1', '/microsoft/callback', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'handle_rest_callback' ),
            // Microsoft cannot send a WordPress REST nonce. Authentication is
            // enforced instead using the one-use OAuth state and browser cookie.
            'permission_callback' => '__return_true',
        ) );
    }

    /**
     * OAuth redirects include code/state in the response query, but the
     * registered redirect URI itself contains no query parameters.
     * Read only query parameters, never a posted body or JSON payload.
     *
     * @param WP_REST_Request $request REST GET request.
     */
    public function handle_rest_callback( $request ) {
        $this->disable_cache();
        if ( ! $this->is_available() ) {
            $this->error_redirect( 'microsoft_config' );
        }
        $params = $request->get_query_params();
        if ( ! is_array( $params ) ) {
            $this->error_redirect( 'microsoft_connection' );
        }
        $this->complete_authorization( $params );
        // Successful and rejected login attempts always redirect and exit.
        return new WP_Error( 'wporlogin_microsoft_callback', __( 'Microsoft login could not be completed.', 'wporlogin' ), array( 'status' => 400 ) );
    }

    /** Local start URL is unchanged; Google/Facebook flows remain independent. */
    public function handle_request() {
        if ( ! isset( $_GET['wporlogin_social_start'] ) || 'microsoft' !== $_GET['wporlogin_social_start'] ) {
            return;
        }
        $this->disable_cache();
        if ( ! $this->is_available() ) {
            $this->error_redirect( 'microsoft_config' );
        }
        $this->start_authorization();
    }

    private function start_authorization() {
        if ( headers_sent() ) {
            $this->error_redirect( 'microsoft_connection' );
        }
        try {
            $state = bin2hex( random_bytes( 32 ) );
            $nonce = bin2hex( random_bytes( 32 ) );
            $verifier = bin2hex( random_bytes( 32 ) );
        } catch ( Exception $e ) {
            $this->error_redirect( 'microsoft_connection' );
        }
        $link_challenge = Wporlogin_Social_Link::challenge_for_start();
        if ( is_wp_error( $link_challenge ) ) { $this->error_redirect( 'social_link_failed' ); }
        $saved = array(
            'link' => $link_challenge,
            'nonce'    => $nonce,
            'verifier' => $verifier,
            'redirect' => '',
        );
        if ( isset( $_GET['redirect_to'] ) && is_string( $_GET['redirect_to'] ) ) {
            $saved['redirect'] = $this->internal_redirect( wp_unslash( $_GET['redirect_to'] ) );
        }
        $key = $this->state_key( $state );
        if ( ! set_transient( $key, $saved, self::STATE_TTL ) ) {
            $this->error_redirect( 'microsoft_connection' );
        }
        if ( ! $this->set_state_cookie( $state, time() + self::STATE_TTL ) ) {
            delete_transient( $key );
            $this->error_redirect( 'microsoft_connection' );
        }
        $challenge = rtrim( strtr( base64_encode( hash( 'sha256', $verifier, true ) ), '+/', '-_' ), '=' );
        wp_redirect( $this->get_provider()->get_authorization_url( $state, $nonce, $challenge ), 302, 'WPOrLogin' );
        exit;
    }

    private function complete_authorization( $params ) {
        $saved = $this->consume_state( $params );
        if ( false === $saved ) {
            $this->error_redirect( 'microsoft_connection' );
        }
        if ( isset( $params['error'] ) ) {
            $this->redirect_authentication_error( Wporlogin_Social_Microsoft::classify_oauth_error( $params['error'] ) );
        }
        if ( ! isset( $params['code'] ) || ! is_string( $params['code'] ) || '' === trim( $params['code'] ) ) {
            $this->error_redirect( 'microsoft_connection' );
        }
        $provider = $this->get_provider();
        // WP_REST_Server already unslashes its query parameters. Preserve the code.
        $profile = $provider->verify_authorization(
            $params['code'],
            $saved['nonce'],
            $saved['verifier']
        );
        if ( ! is_array( $profile ) || empty( $profile['id'] ) ) {
            $this->redirect_authentication_error( $provider->get_last_error() );
        }
        $this->login_or_register( $profile, $saved['redirect'], isset( $saved['link'] ) ? $saved['link'] : '' );
    }

    /** Only the request that owns both the cookie and the transient can proceed. */
    private function consume_state( $params ) {
        $value = isset( $params['state'] ) && is_string( $params['state'] ) ? $params['state'] : '';
        $cookie = isset( $_COOKIE[ self::COOKIE_NAME ] ) && is_string( $_COOKIE[ self::COOKIE_NAME ] )
            ? sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) ) : '';
        $this->set_state_cookie( '', time() - HOUR_IN_SECONDS );
        if ( ! preg_match( '/\A[a-f0-9]{64}\z/', $value ) || ! preg_match( '/\A[a-f0-9]{64}\z/', $cookie ) || ! hash_equals( $cookie, $value ) ) {
            return false;
        }
        $key = $this->state_key( $value );
        $saved = get_transient( $key );
        if ( ! is_array( $saved ) || ! isset( $saved['nonce'], $saved['verifier'], $saved['redirect'] ) ) {
            return false;
        }
        delete_transient( $key );
        return $saved;
    }

    /** Resolve accounts through the shared policy after this provider's OAuth validation. */
    private function login_or_register( $profile, $requested_redirect, $challenge = '' ) {
        if ( is_string( $challenge ) && '' !== $challenge ) {
            $confirmed = Wporlogin_Social_Link::confirm_with_provider( 'microsoft', $profile, $challenge );
            if ( is_wp_error( $confirmed ) ) { $this->error_redirect( 'social_link_failed' ); }
            Wporlogin_Social_Link::finish_social_confirmation( $confirmed );
            $this->error_redirect( 'social_link_failed' );
        }
        $user = $this->resolve_user_by_email( $profile );
        if ( is_wp_error( $user ) && 'wporlogin_link_confirmation_required' === $user->get_error_code() ) {
            $begun = Wporlogin_Social_Link::begin( 'microsoft', $profile, $requested_redirect );
            $this->error_redirect( Wporlogin_Social_Identity::notice_for_error( $begun ) );
        }
        if ( is_wp_error( $user ) || ! is_a( $user, 'WP_User' ) ) {
            $this->error_redirect( Wporlogin_Social_Identity::notice_for_error( $user ) );
        }
        $this->complete_verified_login( $user, $profile, $requested_redirect );
    }

    /** Complete an account resolved by the shared service; never called directly by web parameters. */
    public function complete_verified_login( $user, $profile, $requested_redirect ) {
        $this->save_profile_avatar( $user->ID, isset( $profile['access_token'] ) ? $profile['access_token'] : '' );
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

    /** Provider-specific entry point; general WordPress account rules are shared. */
    private function resolve_user_by_email( $profile ) {
        return Wporlogin_Social_Identity::resolve( 'microsoft', $profile );
    }

    /** Save only an actual image returned by Microsoft Graph (no access tokens). */
    private function save_profile_avatar( $user_id, $access_token ) {
        $picture_url = '';
        if ( '1' === (string) get_option( 'wporlogin_social_avatar_sync', '0' ) ) {
            $provider = $this->get_provider();
            $photo = $provider->get_profile_photo( $access_token );
            if ( is_array( $photo ) ) {
                $extension = array( 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp' );
                if ( isset( $extension[ $photo['mime'] ] ) ) {
                    $upload = wp_upload_bits( 'wporlogin-microsoft-avatar-' . absint( $user_id ) . '.' . $extension[ $photo['mime'] ], null, $photo['bytes'] );
                    if ( is_array( $upload ) && empty( $upload['error'] ) && ! empty( $upload['url'] ) && ! empty( $upload['file'] ) ) {
                        $picture_url = $upload['url'];
                        $this->remove_cached_avatar( $user_id );
                        // Store only a local path returned by WordPress upload API.
                        update_user_meta( $user_id, self::AVATAR_FILE_META, $upload['file'] );
                    }
                }
            }
        }
        // Clear an old provider's picture if MS did not supply one.
        Wporlogin_Social_Avatar::update_on_login( $user_id, 'microsoft', $picture_url );
    }

    /** Remove only a file generated for this user by WPOrLogin. */
    private function remove_cached_avatar( $user_id ) {
        $old_file = get_user_meta( $user_id, self::AVATAR_FILE_META, true );
        $uploads = wp_upload_dir();
        if ( is_string( $old_file ) && isset( $uploads['basedir'] ) && is_string( $uploads['basedir'] ) ) {
            $base = realpath( $uploads['basedir'] );
            $file = realpath( $old_file );
            if ( $base && $file && 0 === strpos( $file, $base . DIRECTORY_SEPARATOR ) &&
                preg_match( '/\Awporlogin-microsoft-avatar-' . preg_quote( (string) absint( $user_id ), '/' ) . '(?:-\d+)?\.(?:jpg|png|webp)\z/', basename( $file ) )
            ) {
                wp_delete_file( $file );
            }
        }
        delete_user_meta( $user_id, self::AVATAR_FILE_META );
    }

    private function get_provider() {
        return new Wporlogin_Social_Microsoft(
            trim( (string) get_option( 'wporlogin_microsoft_client_id', '' ) ),
            trim( (string) get_option( 'wporlogin_microsoft_client_secret', '' ) )
        );
    }

    private function state_key( $state ) {
        return 'wporlogin_ms_state_' . hash( 'sha256', $state );
    }

    private function set_state_cookie( $value, $expires ) {
        $path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
        return setcookie( self::COOKIE_NAME, $value, array(
            'expires' => $expires, 'path' => $path ? $path : '/', 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax',
        ) );
    }

    private function disable_cache() {
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }
        nocache_headers();
    }

    private function internal_redirect( $value ) {
        $url = wp_validate_redirect( $value, '' );
        if ( '' === $url || 0 === strpos( $url, '//' ) ) {
            return '';
        }
        $host = wp_parse_url( $url, PHP_URL_HOST );
        if ( ! $host ) {
            return $url;
        }
        $trusted = array(
            strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ),
            strtolower( (string) wp_parse_url( site_url(), PHP_URL_HOST ) ),
        );
        return in_array( strtolower( $host ), $trusted, true ) ? $url : '';
    }

    private function redirect_authentication_error( $category ) {
        $codes = array( 'configuration' => 'microsoft_config', 'cancelled' => 'microsoft_cancel' );
        $this->error_redirect( isset( $codes[ $category ] ) ? $codes[ $category ] : 'microsoft_connection' );
    }

    private function error_redirect( $code ) {
        wp_safe_redirect( add_query_arg( 'wporlogin_error', $code, wp_login_url() ) );
        exit;
    }
}
