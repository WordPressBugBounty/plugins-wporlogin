<?php
/**
 * Server-side token verification for Cloudflare Turnstile.
 *
 * This class deliberately has no WordPress hooks. The public CAPTCHA controller
 * will call it when Turnstile is connected to the login forms in step 3.
 * Google reCAPTCHA validation remains independent and unchanged.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/public
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Wporlogin_Turnstile_Verifier {

    /** Cloudflare's token-verification endpoint. */
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Verify exactly one form submission. Never cache successful tokens:
     * Turnstile tokens expire after 5 minutes and may be redeemed only once.
     *
     * When rendering the widget in step 3, its `data-action` must be the same
     * as $context: login, register or lostpassword.
     *
     * @param string $token   Token from the cf-turnstile-response POST field.
     * @param string $context One of: login, register, lostpassword.
     * @return true|WP_Error
     */
    public function verify( $token, $context ) {
        if ( ! in_array( $context, array( 'login', 'register', 'lostpassword' ), true ) ) {
            return new WP_Error( 'turnstile_invalid_action', __( 'Security verification failed. Please try again.', 'wporlogin' ) );
        }

        // Keep provider choice and existing Google settings independent.
        if ( 'turnstile' !== get_option( 'recaptcha_version_wporlogin', 'none' ) ) {
            return new WP_Error( 'turnstile_not_selected', __( 'Turnstile is not enabled.', 'wporlogin' ) );
        }

        $site_key   = trim( (string) get_option( 'wporlogin_turnstile_site_key', '' ) );
        $secret_key = trim( (string) get_option( 'wporlogin_turnstile_secret_key', '' ) );

        if ( '' === $site_key || '' === $secret_key ) {
            return new WP_Error( 'turnstile_not_configured', __( 'Turnstile is not configured.', 'wporlogin' ) );
        }

        if ( ! is_string( $token ) || '' === trim( $token ) || strlen( $token ) > 2048 ) {
            return new WP_Error( 'turnstile_missing_token', __( 'Please complete the security check.', 'wporlogin' ) );
        }

        // remoteip is optional. Omitting it avoids trusting proxy headers or
        // transferring an additional visitor identifier unnecessarily.
        $response = wp_remote_post(
            self::VERIFY_URL,
            array(
                'timeout'     => 10,
                'redirection' => 0,
                'body'        => array(
                    'secret'   => $secret_key,
                    'response' => $token,
                ),
            )
        );

        if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            return new WP_Error( 'turnstile_connection_error', __( 'The security service is unavailable. Please try again.', 'wporlogin' ) );
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $data ) || true !== ( $data['success'] ?? false ) ) {
            $errors = is_array( $data ) && isset( $data['error-codes'] ) && is_array( $data['error-codes'] )
                ? $data['error-codes']
                : array();

            if ( in_array( 'timeout-or-duplicate', $errors, true ) ) {
                return new WP_Error( 'turnstile_expired', __( 'The security check expired. Please try again.', 'wporlogin' ) );
            }

            return new WP_Error( 'turnstile_invalid_token', __( 'Security verification failed. Please try again.', 'wporlogin' ) );
        }

        // Do not trust the request Host header; use the configured site URLs.
        $hostname = isset( $data['hostname'] ) && is_string( $data['hostname'] )
            ? strtolower( rtrim( trim( $data['hostname'] ), '.' ) )
            : '';

        if ( '' === $hostname || ! in_array( $hostname, $this->allowed_hostnames(), true ) ) {
            return new WP_Error( 'turnstile_invalid_hostname', __( 'Security domain verification failed.', 'wporlogin' ) );
        }

        if ( ! isset( $data['action'] ) || $context !== $data['action'] ) {
            return new WP_Error( 'turnstile_invalid_action', __( 'Security verification failed. Please try again.', 'wporlogin' ) );
        }

        return true;
    }

    /**
     * Expected login hosts from WordPress settings. An administrator may
     * whitelist an additional mapped domain via a filter, if necessary.
     *
     * @return string[]
     */
    private function allowed_hostnames() {
        $hosts = array();
        foreach ( array( home_url(), site_url() ) as $url ) {
            $hostname = wp_parse_url( $url, PHP_URL_HOST );
            if ( is_string( $hostname ) && '' !== $hostname ) {
                $hosts[] = strtolower( rtrim( trim( $hostname ), '.' ) );
            }
        }

        /**
         * Allow additional explicitly trusted login hosts (domain mapping).
         * Never populate this list from visitor-controlled request headers.
         *
         * @param string[] $hosts WordPress site hostnames.
         */
        $hosts = apply_filters( 'wporlogin_turnstile_allowed_hostnames', $hosts );
        if ( ! is_array( $hosts ) ) {
            return array();
        }

        $hosts = array_map(
            static function ( $host ) {
                return is_string( $host ) ? strtolower( rtrim( trim( $host ), '.' ) ) : '';
            },
            $hosts
        );

        return array_values( array_unique( array_filter( $hosts ) ) );
    }
}
