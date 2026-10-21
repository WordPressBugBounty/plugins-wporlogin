<?php
/**
 * Meta OAuth / Graph API adapter for Facebook Login.
 *
 * All Facebook-specific network calls and rendering live in this optional file.
 * No Google credentials, methods, or user data are modified here.
 *
 * @package Wporlogin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wporlogin-social-provider.php';

class Wporlogin_Social_Facebook extends Wporlogin_Social_Provider {
    /** Meta Graph API is explicitly versioned to avoid unannounced changes. */
    const GRAPH_VERSION = 'v26.0';

    /**
     * Only expose Facebook when enabled and both credentials are available.
     *
     * @return bool
     */
    public static function is_configured() {
        return '1' === (string) get_option( 'wporlogin_facebook_enable', '0' )
            && '' !== trim( (string) get_option( 'wporlogin_facebook_app_id', '' ) )
            && '' !== trim( (string) get_option( 'wporlogin_facebook_app_secret', '' ) );
    }

    /** @return string Local start URL; state is only generated after a click. */
    public function get_auth_url() {
        $url = add_query_arg( 'wporlogin_social_start', 'facebook', home_url( '/' ) );

        if ( isset( $_GET['redirect_to'] ) && is_string( $_GET['redirect_to'] ) ) {
            $requested = wp_validate_redirect( wp_unslash( $_GET['redirect_to'] ), '' );
            if ( '' !== $requested ) {
                $url = add_query_arg( 'redirect_to', rawurlencode( $requested ), $url );
            }
        }

        return $url;
    }

    /**
     * Return the exact callback URI Meta must be configured to accept.
     *
     * @return string
     */
    public static function callback_url() {
        return home_url( '/?wporlogin_social_auth=facebook' );
    }

    /**
     * @param string $state Cryptographically random, one-use OAuth state.
     * @return string
     */
    public function get_authorization_url( $state ) {
        return 'https://www.facebook.com/' . self::GRAPH_VERSION . '/dialog/oauth?' . http_build_query(
            array(
                'client_id'     => $this->client_id,
                'redirect_uri'  => self::callback_url(),
                'response_type' => 'code',
                'scope'         => 'public_profile,email',
                'state'         => $state,
            ),
            '',
            '&',
            PHP_QUERY_RFC3986
        );
    }

    /**
     * Exchange an authorization code server-side and resolve the app-scoped ID.
     * The app secret and user access token are never sent to the browser.
     *
     * @param string $code Authorization code returned by Meta.
     * @return array|false Validated profile or false.
     */
    public function verify_token( $code ) {
        $this->last_error = 'connection';
        if ( ! is_string( $code ) || '' === trim( $code ) ) {
            return false;
        }

        $token_endpoint = 'https://graph.facebook.com/' . self::GRAPH_VERSION . '/oauth/access_token';

        // POST keeps the App Secret and authorization code out of query strings
        // and any HTTP access logs. WordPress sends the body over HTTPS.
        $response = wp_remote_post(
            $token_endpoint,
            array(
                'timeout' => 15,
                'redirection' => 0,
                'limit_response_size' => 1048576,
                'body'    => array(
                    'client_id'     => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'redirect_uri'  => self::callback_url(),
                    'code'          => $code,
                ),
            )
        );
        $body = $this->decode_successful_response( $response );
        if ( ! is_array( $body ) || ! isset( $body['access_token'] ) || ! is_string( $body['access_token'] ) ||
            '' === trim( $body['access_token'] ) || preg_match( '/[\r\n]/', $body['access_token'] ) ) {
            return false;
        }
        $token = $body['access_token'];

        // Verify that the token belongs to this exact Meta application, not just
        // that it can retrieve an arbitrary Facebook profile.
        $debug_endpoint = add_query_arg(
            array( 'input_token' => rawurlencode( $token ) ),
            'https://graph.facebook.com/' . self::GRAPH_VERSION . '/debug_token'
        );
        $debug_response = wp_remote_get(
            $debug_endpoint,
            array(
                'timeout' => 15,
                'redirection' => 0,
                'limit_response_size' => 1048576,
                'headers' => array( 'Authorization' => 'Bearer ' . $this->client_id . '|' . $this->client_secret ),
            )
        );
        $debug = $this->decode_successful_response( $debug_response );
        if (
            ! is_array( $debug ) || ! isset( $debug['data'] ) || ! is_array( $debug['data'] ) ||
            true !== ( $debug['data']['is_valid'] ?? false ) ||
            ! isset( $debug['data']['app_id'], $debug['data']['user_id'] ) ||
            ! ( is_string( $debug['data']['app_id'] ) || is_int( $debug['data']['app_id'] ) ) ||
            ! ( is_string( $debug['data']['user_id'] ) || is_int( $debug['data']['user_id'] ) ) ||
            ! hash_equals( (string) $this->client_id, (string) $debug['data']['app_id'] ) ||
            ! preg_match( '/\A[0-9]+\z/', (string) $debug['data']['user_id'] )
        ) {
            return false;
        }

        $profile_endpoint = add_query_arg(
            array(
                'fields'          => rawurlencode( 'id,name,first_name,last_name,email,picture.type(large)' ),
                'appsecret_proof' => rawurlencode( hash_hmac( 'sha256', $token, $this->client_secret ) ),
            ),
            'https://graph.facebook.com/' . self::GRAPH_VERSION . '/me'
        );
        $profile_response = wp_remote_get(
            $profile_endpoint,
            array(
                'timeout' => 15,
                'redirection' => 0,
                'limit_response_size' => 1048576,
                'headers' => array( 'Authorization' => 'Bearer ' . $token ),
            )
        );
        $profile = $this->decode_successful_response( $profile_response );
        if (
            ! is_array( $profile ) || empty( $profile['id'] ) ||
            ! ( is_string( $profile['id'] ) || is_int( $profile['id'] ) ) ||
            ! hash_equals( (string) $debug['data']['user_id'], (string) $profile['id'] ) ||
            ! preg_match( '/\A[0-9]+\z/', (string) $profile['id'] )
        ) {
            return false;
        }

        // Email is optional in Facebook Login; an absent email is NOT an error
        // for an already-linked account, but new registration requires one.
        return array(
            'id'         => (string) $profile['id'],
            'name'       => isset( $profile['name'] ) && is_string( $profile['name'] ) ? $profile['name'] : '',
            'first_name' => isset( $profile['first_name'] ) && is_string( $profile['first_name'] ) ? $profile['first_name'] : '',
            'last_name'  => isset( $profile['last_name'] ) && is_string( $profile['last_name'] ) ? $profile['last_name'] : '',
            'email'      => isset( $profile['email'] ) && is_string( $profile['email'] ) ? $profile['email'] : '',
            // Graph API supplies this via public_profile when available.
            // A silhouette is the default placeholder, not a personal photo.
            'picture'    => (
                isset( $profile['picture']['data']['url'] ) &&
                is_string( $profile['picture']['data']['url'] ) &&
                empty( $profile['picture']['data']['is_silhouette'] )
            ) ? $profile['picture']['data']['url'] : '',
        );
    }

    /**
     * @param array|WP_Error $response WordPress HTTP API response.
     * @return array|false
     */
    private function decode_successful_response( $response ) {
        if ( is_wp_error( $response ) ) {
            return false;
        }
        $decoded = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $decoded ) ) {
            return false;
        }
        if ( array_key_exists( 'error', $decoded ) ) {
            $error = $decoded['error'];
            if ( is_array( $error ) ) {
                // Meta's app-ID and redirect-URI configuration errors.
                $this->last_error = isset( $error['code'] ) && in_array( $error['code'], array( 101, 191 ), true )
                    ? 'configuration' : 'connection';
            } else {
                $this->last_error = self::classify_oauth_error( $error );
            }
            return false;
        }
        return 200 === (int) wp_remote_retrieve_response_code( $response ) ? $decoded : false;
    }

    /**
     * @param string|null $custom_text Label for login or registration.
     * @return string Accessible Facebook button markup.
     */
    public function render_button( $custom_text = null ) {
        $text = $custom_text ? $custom_text : __( 'Sign in with Facebook', 'wporlogin' );
        $icon = '<svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M13.8 21v-8.2h2.75l.42-3.2H13.8V7.55c0-.92.26-1.55 1.58-1.55h1.69V3.14A22.8 22.8 0 0 0 14.63 3C12.2 3 10.55 4.48 10.55 7.2v2.4H7.8v3.2h2.75V21z"/></svg>';
        return '<a href="' . esc_url( $this->get_auth_url() ) . '" class="wporlogin-social-btn wporlogin-facebook-btn">' .
            '<span class="wporlogin-social-icon-wrapper">' . $icon . '</span>' .
            '<span class="wporlogin-social-text">' . esc_html( $text ) . '</span></a>';
    }
}
