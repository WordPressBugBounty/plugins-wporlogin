<?php
/**
 * Facebook-only authentication orchestration for WPOrLogin.
 *
 * Facebook sign-in uses the validated Meta OAuth response and the email
 * returned by Facebook to find or create a native WordPress account.
 * Google and Microsoft modules remain independent.
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

class Wporlogin_Facebook_Handler implements Wporlogin_Social_Handler {
    const COOKIE_NAME = 'wporlogin_facebook_oauth_state';
    const META_KEY    = 'wporlogin_facebook_id';
    const STATE_TTL   = 600;

    /** @return string Escaped Facebook button HTML. */
    public function render_button( $register = false ) {
        return $this->get_provider()->render_button( $register
            ? __( 'Register with Facebook', 'wporlogin' )
            : __( 'Sign in with Facebook', 'wporlogin' ) );
    }

    /** @return bool Disabled automatically when the optional provider is absent. */
    public function is_available() {
        return class_exists( 'Wporlogin_Social_Facebook', false )
            && Wporlogin_Social_Providers::is_available( 'facebook' );
    }

    /**
     * Handle only Facebook start/callback requests; no effect on Google.
     *
     * @return void
     */
    public function handle_request() {
        $starting = isset( $_GET['wporlogin_social_start'] ) && 'facebook' === $_GET['wporlogin_social_start'];
        $callback = isset( $_GET['wporlogin_social_auth'] ) && 'facebook' === $_GET['wporlogin_social_auth'];
        if ( ! $starting && ! $callback ) {
            return;
        }

        $this->disable_cache();
        if ( ! $this->is_available() ) {
            $this->error_redirect( 'facebook_config' );
        }

        if ( $starting ) {
            $this->start_authorization();
        } else {
            $this->complete_authorization();
        }
    }

    /** Build a one-time authorization request bound to the current browser. */
    private function start_authorization() {
        if ( headers_sent() ) {
            $this->error_redirect( 'facebook_connection' );
        }
        try {
            $state = bin2hex( random_bytes( 32 ) );
        } catch ( Exception $exception ) {
            $this->error_redirect( 'facebook_connection' );
        }

        $link_challenge = Wporlogin_Social_Link::challenge_for_start();
        if ( is_wp_error( $link_challenge ) ) { $this->error_redirect( 'social_link_failed' ); }
        $saved = array( 'redirect' => '', 'link' => $link_challenge );
        if ( isset( $_GET['redirect_to'] ) && is_string( $_GET['redirect_to'] ) ) {
            $saved['redirect'] = $this->internal_redirect( wp_unslash( $_GET['redirect_to'] ) );
        }

        $key = $this->state_key( $state );
        if ( ! set_transient( $key, $saved, self::STATE_TTL ) ) {
            $this->error_redirect( 'facebook_connection' );
        }
        if ( ! $this->set_state_cookie( $state, time() + self::STATE_TTL ) ) {
            delete_transient( $key );
            $this->error_redirect( 'facebook_connection' );
        }

        $provider = $this->get_provider();
        wp_redirect( $provider->get_authorization_url( $state ), 302, 'WPOrLogin' );
        exit;
    }

    /** Verify the browser state before exchanging Meta's authorization code. */
    private function complete_authorization() {
        $saved = $this->consume_state();
        if ( false === $saved ) {
            $this->error_redirect( 'facebook_connection' );
        }
        if ( isset( $_GET['error'] ) ) {
            $this->redirect_authentication_error( Wporlogin_Social_Facebook::classify_oauth_error(
                is_string( $_GET['error'] ) ? sanitize_key( wp_unslash( $_GET['error'] ) ) : ''
            ) );
        }
        if ( ! isset( $_GET['code'] ) || ! is_string( $_GET['code'] ) || '' === trim( $_GET['code'] ) ) {
            $this->error_redirect( 'facebook_connection' );
        }

        $provider = $this->get_provider();
        $profile  = $provider->verify_token( wp_unslash( $_GET['code'] ) );
        if ( ! is_array( $profile ) || empty( $profile['id'] ) ) {
            $this->redirect_authentication_error( $provider->get_last_error() );
        }

        $this->login_or_register( $profile, $saved['redirect'], isset( $saved['link'] ) ? $saved['link'] : '' );
    }

    /**
     * Cookie and transient must match. Consume state before any network calls.
     *
     * @return array|false
     */
    private function consume_state() {
        $value  = isset( $_GET['state'] ) && is_string( $_GET['state'] ) ? wp_unslash( $_GET['state'] ) : '';
        $cookie = isset( $_COOKIE[ self::COOKIE_NAME ] ) && is_string( $_COOKIE[ self::COOKIE_NAME ] )
            ? sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) ) : '';
        $this->set_state_cookie( '', time() - HOUR_IN_SECONDS );

        if (
            ! preg_match( '/\A[a-f0-9]{64}\z/', $value ) ||
            ! preg_match( '/\A[a-f0-9]{64}\z/', $cookie ) ||
            ! hash_equals( $value, $cookie )
        ) {
            return false;
        }

        $key = $this->state_key( $value );
        $saved = get_transient( $key );
        if ( ! is_array( $saved ) || ! isset( $saved['redirect'] ) ) {
            return false;
        }
        delete_transient( $key );
        return $saved;
    }

    /** Resolve accounts through the shared policy after this provider's OAuth validation. */
    private function login_or_register( $profile, $requested_redirect, $challenge = '' ) {
        if ( is_string( $challenge ) && '' !== $challenge ) {
            $confirmed = Wporlogin_Social_Link::confirm_with_provider( 'facebook', $profile, $challenge );
            if ( is_wp_error( $confirmed ) ) { $this->error_redirect( 'social_link_failed' ); }
            Wporlogin_Social_Link::finish_social_confirmation( $confirmed );
            $this->error_redirect( 'social_link_failed' );
        }
        $user = $this->resolve_user_by_email( $profile );
        if ( is_wp_error( $user ) && 'wporlogin_link_confirmation_required' === $user->get_error_code() ) {
            $begun = Wporlogin_Social_Link::begin( 'facebook', $profile, $requested_redirect );
            $this->error_redirect( Wporlogin_Social_Identity::notice_for_error( $begun ) );
        }
        if ( is_wp_error( $user ) || ! is_a( $user, 'WP_User' ) ) {
            $this->error_redirect( Wporlogin_Social_Identity::notice_for_error( $user ) );
        }
        $this->complete_verified_login( $user, $profile, $requested_redirect );
    }

    /** Complete an account resolved by the shared service; never called directly by web parameters. */
    public function complete_verified_login( $user, $profile, $requested_redirect ) {
        Wporlogin_Social_Avatar::update_on_login(
            $user->ID,
            'facebook',
            isset( $profile['picture'] ) ? $profile['picture'] : ''
        );
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
        return Wporlogin_Social_Identity::resolve( 'facebook', $profile );
    }

    /** @return Wporlogin_Social_Facebook */
    private function get_provider() {
        return new Wporlogin_Social_Facebook(
            trim( (string) get_option( 'wporlogin_facebook_app_id', '' ) ),
            trim( (string) get_option( 'wporlogin_facebook_app_secret', '' ) )
        );
    }

    /** @return string */
    private function state_key( $state ) {
        return 'wporlogin_fb_state_' . hash( 'sha256', $state );
    }

    /** @return bool */
    private function set_state_cookie( $value, $expires ) {
        $path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
        return setcookie(
            self::COOKIE_NAME,
            $value,
            array(
                'expires'  => $expires,
                'path'     => $path ? $path : '/',
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            )
        );
    }

    /** Keep OAuth authorization and callbacks out of page caches. */
    private function disable_cache() {
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }
        nocache_headers();
    }

    /** Accept only a previously requested local URL, not a remote redirect. */
    private function internal_redirect( $value ) {
        $url = wp_validate_redirect( $value, '' );
        if ( '' === $url || 0 === strpos( $url, '//' ) ) {
            return '';
        }
        $host = wp_parse_url( $url, PHP_URL_HOST );
        if ( ! $host ) {
            return $url;
        }
        $hosts = array(
            strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ),
            strtolower( (string) wp_parse_url( site_url(), PHP_URL_HOST ) ),
        );
        return in_array( strtolower( $host ), $hosts, true ) ? $url : '';
    }

    /** Redirect safely, without exposing tokens or internal error details. */
    private function redirect_authentication_error( $category ) {
        $codes = array( 'configuration' => 'facebook_config', 'cancelled' => 'facebook_cancel' );
        $this->error_redirect( isset( $codes[ $category ] ) ? $codes[ $category ] : 'facebook_connection' );
    }

    private function error_redirect( $code ) {
        wp_safe_redirect( add_query_arg( 'wporlogin_error', $code, wp_login_url() ) );
        exit;
    }
}
