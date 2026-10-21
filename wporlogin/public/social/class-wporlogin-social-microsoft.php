<?php
/**
 * Microsoft identity platform v2 adapter (personal + work/school accounts).
 * No Google/Facebook state or credentials are shared with this provider.
 *
 * @package Wporlogin
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wporlogin-social-provider.php';

class Wporlogin_Social_Microsoft extends Wporlogin_Social_Provider {
    const AUTHORITY = 'https://login.microsoftonline.com/common/oauth2/v2.0';
    const JWKS_URL  = 'https://login.microsoftonline.com/common/discovery/v2.0/keys';

    public static function is_configured() {
        return extension_loaded( 'openssl' ) && function_exists( 'openssl_verify' )
            && '1' === (string) get_option( 'wporlogin_microsoft_enable', '0' )
            && (bool) preg_match( '/\A[0-9a-fA-F]{8}(?:-[0-9a-fA-F]{4}){3}-[0-9a-fA-F]{12}\z/', trim( (string) get_option( 'wporlogin_microsoft_client_id', '' ) ) )
            && '' !== trim( (string) get_option( 'wporlogin_microsoft_client_secret', '' ) )
            && self::is_callback_supported();
    }

    /** The registered URL must be query-free and use HTTPS (localhost may use HTTP). */
    public static function is_callback_supported() {
        // WordPress creates WP_Rewrite after loading plugins. Avoid resolving a
        // pretty REST URL before that object exists during early plugin loading.
        if ( ! function_exists( 'rest_url' ) || ! isset( $GLOBALS['wp_rewrite'] ) ) {
            return false;
        }
        $parts = wp_parse_url( self::callback_url() );
        if ( ! is_array( $parts ) || isset( $parts['query'] ) || isset( $parts['fragment'] ) ||
            empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
            return false;
        }
        $scheme = strtolower( $parts['scheme'] );
        $host   = strtolower( $parts['host'] );
        return 'https' === $scheme || ( 'http' === $scheme && 'localhost' === $host );
    }

    /** Local start URL: do not generate a state inside cacheable HTML. */
    public function get_auth_url() {
        $url = add_query_arg( 'wporlogin_social_start', 'microsoft', home_url( '/' ) );
        if ( isset( $_GET['redirect_to'] ) && is_string( $_GET['redirect_to'] ) ) {
            $requested = wp_validate_redirect( wp_unslash( $_GET['redirect_to'] ), '' );
            if ( '' !== $requested ) {
                $url = add_query_arg( 'redirect_to', rawurlencode( $requested ), $url );
            }
        }
        return $url;
    }

    /**
     * One query-free, site-aware callback shared by authorization and token exchange.
     * WordPress REST handles subdirectory installations and the HTTPS site scheme.
     * Pretty permalinks must be enabled so rest_url() does not use ?rest_route=.
     */
    public static function callback_url() {
        return rest_url( 'wporlogin/v1/microsoft/callback' );
    }

    /** @param string $state One-use state. @param string $nonce OIDC nonce. @param string $challenge PKCE challenge. */
    public function get_authorization_url( $state, $nonce = '', $challenge = '' ) {
        return self::AUTHORITY . '/authorize?' . http_build_query(
            array(
                'client_id'             => $this->client_id,
                'response_type'         => 'code',
                'redirect_uri'          => self::callback_url(),
                'response_mode'         => 'query',
                'scope'                 => 'openid profile email' . ( '1' === (string) get_option( 'wporlogin_social_avatar_sync', '0' ) ? ' User.Read' : '' ),
                'state'                 => $state,
                'nonce'                 => $nonce,
                'code_challenge'        => $challenge,
                'code_challenge_method' => 'S256',
            ), '', '&', PHP_QUERY_RFC3986
        );
    }

    /**
     * Exchange authorization code on the server and verify signed OIDC ID token.
     * NEVER authenticate based only on an email or an unverified JWT payload.
     *
     * @param string $code Authorization code.
     * @param string $nonce Nonce bound to state.
     * @param string $verifier One-use PKCE verifier.
     * @return array|false Verified identity and short-lived access token for optional photo.
     */
    public function verify_authorization( $code, $nonce, $verifier ) {
        $this->last_error = 'connection';
        if ( ! is_string( $code ) || '' === trim( $code ) || ! is_string( $nonce ) || ! is_string( $verifier ) ||
            ! preg_match( '/\A[a-f0-9]{64}\z/', $nonce ) || ! preg_match( '/\A[a-f0-9]{64}\z/', $verifier ) ) {
            return false;
        }
        $response = wp_remote_post( self::AUTHORITY . '/token', array(
            'timeout' => 20,
            'redirection' => 0,
            'limit_response_size' => 1048576,
            'body'    => array(
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => self::callback_url(),
                'code'          => $code,
                'code_verifier'  => $verifier,
            ),
        ) );
        if ( is_wp_error( $response ) ) {
            return false;
        }
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $body ) ) {
            return false;
        }
        if ( array_key_exists( 'error', $body ) ) {
            $this->last_error = self::classify_oauth_error( $body['error'] );
            return false;
        }
        if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ||
            ! isset( $body['id_token'] ) || ! is_string( $body['id_token'] ) || '' === $body['id_token'] ) {
            return false;
        }
        $claims = $this->validate_id_token( $body['id_token'], $nonce );
        if ( false === $claims ) {
            return false;
        }

        $email = '';
        foreach ( array( 'email', 'preferred_username' ) as $claim ) {
            if ( isset( $claims[ $claim ] ) && is_string( $claims[ $claim ] ) && is_email( $claims[ $claim ] ) ) {
                $email = sanitize_email( $claims[ $claim ] );
                break;
            }
        }
        // The email is DISPLAY/REGISTRATION DATA ONLY, never an identity key.
        return array(
            'id'         => strtolower( $claims['tid'] ) . ':' . $claims['sub'],
            'email'      => $email,
            'name'       => isset( $claims['name'] ) && is_string( $claims['name'] ) ? $claims['name'] : '',
            'first_name' => isset( $claims['given_name'] ) && is_string( $claims['given_name'] ) ? $claims['given_name'] : '',
            'last_name'  => isset( $claims['family_name'] ) && is_string( $claims['family_name'] ) ? $claims['family_name'] : '',
            'access_token' => isset( $body['access_token'] ) && is_string( $body['access_token'] ) ? $body['access_token'] : '',
        );
    }

    /**
     * Validate signature using Microsoft's HTTPS JWKS, including per-key issuer
     * restrictions for the tenant-independent /common authority.
     */
    public function validate_id_token( $token, $expected_nonce ) {
        if ( ! is_string( $token ) || strlen( $token ) > 16000 || 3 !== count( explode( '.', $token ) ) ) {
            return false;
        }
        list( $header_b64, $claims_b64, $signature_b64 ) = explode( '.', $token );
        $header = json_decode( $this->base64url_decode( $header_b64 ), true );
        $claims = json_decode( $this->base64url_decode( $claims_b64 ), true );
        $signature = $this->base64url_decode( $signature_b64 );
        if ( ! is_array( $header ) || ! is_array( $claims ) || ! is_string( $signature ) || '' === $signature ||
            ! isset( $header['alg'], $header['kid'] ) || 'RS256' !== $header['alg'] || ! is_string( $header['kid'] ) ||
            ! isset( $claims['tid'], $claims['sub'], $claims['iss'], $claims['aud'], $claims['exp'], $claims['iat'], $claims['nonce'] ) ||
            ! is_string( $claims['tid'] ) || ! preg_match( '/\A[0-9a-fA-F]{8}(?:-[0-9a-fA-F]{4}){3}-[0-9a-fA-F]{12}\z/', $claims['tid'] ) ||
            ! is_string( $claims['sub'] ) || '' === $claims['sub'] || strlen( $claims['sub'] ) > 255 ||
            ! is_string( $claims['iss'] ) || ! hash_equals( 'https://login.microsoftonline.com/' . $claims['tid'] . '/v2.0', $claims['iss'] ) ||
            ! is_string( $claims['aud'] ) || ! hash_equals( (string) $this->client_id, $claims['aud'] ) ||
            ! is_numeric( $claims['exp'] ) || ! is_numeric( $claims['iat'] ) ||
            (int) $claims['exp'] <= time() - 120 || (int) $claims['iat'] > time() + 120 ||
            ( isset( $claims['nbf'] ) && ( ! is_numeric( $claims['nbf'] ) || (int) $claims['nbf'] > time() + 120 ) ) ||
            ! is_string( $claims['nonce'] ) || ! hash_equals( (string) $expected_nonce, $claims['nonce'] ) ||
            ( isset( $claims['ver'] ) && '2.0' !== $claims['ver'] )
        ) {
            return false;
        }

        $keys = $this->get_jwks();
        if ( false === $keys ) {
            return false;
        }
        for ( $attempt = 0; $attempt < 2; $attempt++ ) {
            foreach ( $keys as $key ) {
                if ( ! is_array( $key ) || ! isset( $key['kid'], $key['issuer'], $key['x5c'][0] ) ||
                    ! is_string( $key['kid'] ) || ! hash_equals( $key['kid'], $header['kid'] ) ||
                    ( isset( $key['kty'] ) && 'RSA' !== $key['kty'] ) ||
                    ( isset( $key['use'] ) && 'sig' !== $key['use'] ) ||
                    ! is_string( $key['issuer'] ) || ! is_string( $key['x5c'][0] )
                ) {
                    continue;
                }
                // Multi-tenant key restrictions are mandatory; a consumer-only
                // signing key must not be accepted for an organization tenant.
                $allowed_issuer = str_replace( '{tenantid}', $claims['tid'], $key['issuer'] );
                if ( ! hash_equals( $claims['iss'], $allowed_issuer ) ) {
                    continue;
                }
                $der = base64_decode( $key['x5c'][0], true );
                if ( ! $der ) {
                    continue;
                }
                $pem = "-----BEGIN CERTIFICATE-----\n" . chunk_split( base64_encode( $der ), 64, "\n" ) . "-----END CERTIFICATE-----\n";
                $public_key = openssl_pkey_get_public( $pem );
                if ( false === $public_key ) {
                    continue;
                }
                $verified = openssl_verify( $header_b64 . '.' . $claims_b64, $signature, $public_key, OPENSSL_ALGO_SHA256 );
                if ( is_resource( $public_key ) ) {
                    openssl_free_key( $public_key );
                }
                if ( 1 === $verified ) {
                    return $claims;
                }
            }
            // Refresh cached keys once on a rotation; never use a key from an
            // untrusted URL provided by a JWT header (jku/x5u are ignored).
            if ( 0 === $attempt ) {
                delete_transient( 'wporlogin_microsoft_jwks' );
                $keys = $this->get_jwks();
                if ( false === $keys ) {
                    break;
                }
            }
        }
        return false;
    }

    /** @return array|false Trusted Microsoft signing keys (cached six hours). */
    private function get_jwks() {
        $cached = get_transient( 'wporlogin_microsoft_jwks' );
        if ( is_array( $cached ) && ! empty( $cached ) ) {
            return $cached;
        }
        $response = wp_remote_get( self::JWKS_URL, array( 'timeout' => 15, 'redirection' => 0, 'limit_response_size' => 1048576 ) );
        if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            return false;
        }
        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $data ) || empty( $data['keys'] ) || ! is_array( $data['keys'] ) ) {
            return false;
        }
        set_transient( 'wporlogin_microsoft_jwks', $data['keys'], 6 * HOUR_IN_SECONDS );
        return $data['keys'];
    }

    private function base64url_decode( $value ) {
        if ( ! is_string( $value ) || ! preg_match( '/\A[A-Za-z0-9_-]+\z/', $value ) ) {
            return false;
        }
        return base64_decode( strtr( $value, '-_', '+/' ) . str_repeat( '=', ( 4 - strlen( $value ) % 4 ) % 4 ), true );
    }

    /** Optional profile image. A missing Graph photo never blocks authentication. */
    public function get_profile_photo( $token ) {
        if ( ! is_string( $token ) || '' === $token || '1' !== (string) get_option( 'wporlogin_social_avatar_sync', '0' ) ) {
            return false;
        }
        $response = wp_remote_get( 'https://graph.microsoft.com/v1.0/me/photo/$value', array(
            'timeout'             => 12,
            'limit_response_size' => 1048576,
            'headers'             => array( 'Authorization' => 'Bearer ' . $token ),
        ) );
        if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            return false;
        }
        $bytes = wp_remote_retrieve_body( $response );
        if ( ! is_string( $bytes ) || '' === $bytes || strlen( $bytes ) > 1048576 || ! function_exists( 'getimagesizefromstring' ) ) {
            return false;
        }
        $info = @getimagesizefromstring( $bytes );
        if ( ! is_array( $info ) || ! isset( $info['mime'] ) || ! in_array( $info['mime'], array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
            return false;
        }
        return array( 'bytes' => $bytes, 'mime' => $info['mime'] );
    }

    /** @param string|null $custom_text Login vs registration button label. */
    public function render_button( $custom_text = null ) {
        $text = $custom_text ? $custom_text : __( 'Sign in with Microsoft', 'wporlogin' );
        $icon = '<svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24"><path fill="#f25022" d="M1 1h10v10H1z"/><path fill="#7fba00" d="M13 1h10v10H13z"/><path fill="#00a4ef" d="M1 13h10v10H1z"/><path fill="#ffb900" d="M13 13h10v10H13z"/></svg>';
        return '<a href="' . esc_url( $this->get_auth_url() ) . '" class="wporlogin-social-btn wporlogin-microsoft-btn">' .
            '<span class="wporlogin-social-icon-wrapper">' . $icon . '</span>' .
            '<span class="wporlogin-social-text">' . esc_html( $text ) . '</span></a>';
    }

    /** Shared adapter contract; OIDC also requires nonce and PKCE verifier. */
    public function verify_token( $code, $nonce = '', $verifier = '' ) {
        return $this->verify_authorization( $code, $nonce, $verifier );
    }
}
