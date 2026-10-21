<?php
/**
 * First-time social account linking. The initial OAuth profile is NOT an
 * authorization to access an existing WordPress user. A separately verified
 * existing sign-in method is required before committing its provider ID.
 *
 * Pending challenges live server-side for ten minutes, keyed by a 256-bit
 * HttpOnly browser secret. No provider credentials or tokens are persisted.
 *
 * @package Wporlogin
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Wporlogin_Social_Link {
    const COOKIE = 'wporlogin_social_link';
    const TTL = 600;

    private static function cookie( $value, $expires ) {
        if ( headers_sent() ) { return false; }
        return setcookie( self::COOKIE, $value, array(
            'expires' => $expires, 'path' => '/', 'secure' => is_ssl(),
            'httponly' => true, 'samesite' => 'Lax',
        ) );
    }

    private static function key( $secret ) {
        return 'wporlogin_link_' . hash( 'sha256', $secret );
    }

    private static function secret() {
        $value = isset( $_COOKIE[ self::COOKIE ] ) && is_string( $_COOKIE[ self::COOKIE ] )
            ? sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE ] ) ) : '';
        return preg_match( '/\A[a-f0-9]{64}\z/', $value ) ? $value : '';
    }

    /** Only show guided account confirmation while its session is still valid. */
    public static function has_pending() {
        return (bool) self::pending();
    }

    private static function pending() {
        $secret = self::secret();
        if ( '' === $secret ) { return null; }
        $saved = get_transient( self::key( $secret ) );
        if ( ! is_array( $saved ) || empty( $saved['user_id'] ) || empty( $saved['provider'] ) || empty( $saved['id'] ) || empty( $saved['email'] ) ) {
            return null;
        }
        return $saved;
    }

    private static function clear() {
        $secret = self::secret();
        if ( '' !== $secret ) { delete_transient( self::key( $secret ) ); }
        self::cookie( '', time() - HOUR_IN_SECONDS );
    }

    private static function finish_url() {
        return add_query_arg( 'wporlogin_link_finish', '1', home_url( '/' ) );
    }

    /** Called only after a real provider callback returned the link-required error. */
    public static function begin( $provider, $profile, $requested_redirect = '' ) {
        $candidate = Wporlogin_Social_Identity::candidate_for_link( $provider, $profile );
        if ( is_wp_error( $candidate ) ) { return $candidate; }
        if ( ! $candidate instanceof WP_User || headers_sent() ) { return new WP_Error( 'wporlogin_social_access_denied' ); }
        try { $secret = bin2hex( random_bytes( 32 ) ); }
        catch ( Exception $exception ) { return new WP_Error( 'wporlogin_social_operation_failed' ); }
        $id = $profile['id'];
        $email = Wporlogin_Social_Identity::valid_email( $profile['email'] ?? '' );
        $saved = array(
            'provider' => $provider, 'id' => $id, 'email' => $email,
            'user_id' => (int) $candidate->ID, 'redirect' => self::internal_redirect( $requested_redirect ),
        );
        // Invalidate an abandoned flow in the same browser.
        $prior = self::secret();
        if ( $prior ) { delete_transient( self::key( $prior ) ); }
        if ( ! set_transient( self::key( $secret ), $saved, self::TTL ) ) { return new WP_Error( 'wporlogin_social_operation_failed' ); }
        if ( ! self::cookie( $secret, time() + self::TTL ) ) {
            delete_transient( self::key( $secret ) );
            return new WP_Error( 'wporlogin_social_operation_failed' );
        }
        // A cookie is not reflected in PHP's $_COOKIE until the next request.
        // Give WordPress its native redirect_to so the ordinary login form
        // can finish linking without a second password screen.
        wp_safe_redirect( add_query_arg( 'wporlogin_link', '1', wp_login_url( self::finish_url() ) ) );
        exit;
    }

    /** Include this browser-bound identifier in the *existing* OAuth state. */
    public static function challenge_for_start() {
        if ( ! isset( $_GET['wporlogin_link'] ) || '1' !== $_GET['wporlogin_link'] ) { return ''; }
        return self::pending() ? hash( 'sha256', self::secret() ) : new WP_Error( 'wporlogin_link_expired' );
    }

    /** Authenticate with a provider already linked to the *same* WP user. */
    public static function confirm_with_provider( $provider, $profile, $challenge ) {
        $secret = self::secret();
        $saved = self::pending();
        if ( ! $saved || ! is_string( $challenge ) || '' === $secret ||
            ! hash_equals( hash( 'sha256', $secret ), $challenge ) ||
            ! Wporlogin_Social_Providers::is_available( $provider ) ||
            ! isset( $profile['id'] ) || ! is_string( $profile['id'] ) ) {
            return new WP_Error( 'wporlogin_link_expired' );
        }
        $known = Wporlogin_Social_Identity::lookup( $provider, $profile['id'] );
        if ( is_wp_error( $known ) || ! $known instanceof WP_User || (int) $known->ID !== (int) $saved['user_id'] ) {
            return new WP_Error( 'wporlogin_link_wrong_account' );
        }
        $linked = self::complete( $known, $saved );
        return is_wp_error( $linked ) ? $linked : $known;
    }

    /** Password flow uses the normal WordPress login, including auth filters. */
    public static function handle_finish() {
        if ( isset( $_GET['wporlogin_link_cancel'] ) && '1' === $_GET['wporlogin_link_cancel'] ) {
            self::clear();
            wp_safe_redirect( wp_login_url() );
            exit;
        }
        if ( ! isset( $_GET['wporlogin_link_finish'] ) || '1' !== $_GET['wporlogin_link_finish'] ) { return; }
        self::no_cache();
        $saved = self::pending();
        $user = wp_get_current_user();
        if ( ! $saved || ! is_user_logged_in() || (int) $user->ID !== (int) $saved['user_id'] ) {
            self::clear();
            wp_safe_redirect( add_query_arg( 'wporlogin_error', 'social_link_failed', wp_login_url() ) );
            exit;
        }
        $result = self::complete( $user, $saved );
        if ( is_wp_error( $result ) ) {
            self::clear();
            wp_safe_redirect( add_query_arg( 'wporlogin_error', 'social_link_failed', wp_login_url() ) );
            exit;
        }
        Wporlogin_Social_Identity::record_password_method( $user->ID );
        self::redirect_after_link( $user, $saved );
    }

    /** Ensure the requested login redirect survives the plugin's general redirect rules. */
    public static function preserve_password_redirect( $redirect, $requested, $user ) {
        if ( $user instanceof WP_User && self::pending() &&
            is_string( $requested ) && wp_validate_redirect( $requested, '' ) === self::finish_url() ) {
            return self::finish_url();
        }
        return $redirect;
    }

    /** Only the previously verified target user can authorize linking. */
    private static function complete( $user, $saved ) {
        $existing = get_userdata( (int) $saved['user_id'] );
        if ( ! $existing || (int) $existing->ID !== (int) $user->ID ||
            0 !== strcasecmp( $existing->user_email, $saved['email'] ) ||
            ! Wporlogin_Social_Providers::is_available( $saved['provider'] ) ) {
            return new WP_Error( 'wporlogin_link_wrong_account' );
        }
        $access = Wporlogin_Social_Identity::check_user_access( $existing, $saved['provider'] );
        if ( is_wp_error( $access ) ) { return $access; }
        return Wporlogin_Social_Identity::link_identity( $saved['provider'], $saved['id'], $existing->ID );
    }

    /** Social-confirmation flow: finish the *existing provider's* valid login. */
    public static function finish_social_confirmation( $user ) {
        $saved = self::pending();
        if ( ! $saved || (int) $saved['user_id'] !== (int) $user->ID ) { return; }
        $redirect = self::destination( $user, $saved );
        self::clear();
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID );
        do_action( 'wp_login', $user->user_login, $user );
        wp_safe_redirect( $redirect );
        exit;
    }

    private static function redirect_after_link( $user, $saved ) {
        $redirect = self::destination( $user, $saved );
        self::clear();
        wp_safe_redirect( $redirect );
        exit;
    }

    private static function destination( $user, $saved ) {
        $requested = isset( $saved['redirect'] ) ? $saved['redirect'] : '';
        if ( class_exists( 'Wporlogin_Public_Redirects' ) ) {
            return Wporlogin_Public_Redirects::get_calculated_redirect_url( $user, $requested );
        }
        return '' !== $requested ? $requested : home_url( '/' );
    }

    private static function internal_redirect( $value ) {
        if ( ! is_string( $value ) ) { return ''; }
        $url = wp_validate_redirect( $value, '' );
        if ( ! $url || 0 === strpos( $url, '//' ) ) { return ''; }
        $host = wp_parse_url( $url, PHP_URL_HOST );
        $hosts = array( wp_parse_url( home_url(), PHP_URL_HOST ), wp_parse_url( site_url(), PHP_URL_HOST ) );
        return ! $host || in_array( strtolower( $host ), array_map( 'strtolower', $hosts ), true ) ? $url : '';
    }

    private static function no_cache() {
        if ( ! defined( 'DONOTCACHEPAGE' ) ) { define( 'DONOTCACHEPAGE', true ); }
        nocache_headers();
    }

    /**
     * Password confirmation is an explicit choice, not the default screen.
     * A normal login POST must stay in password mode if WordPress rejects
     * incorrect credentials, even though core does not retain query args.
     */
    public static function password_mode() {
        if ( ! self::has_pending() ) { return false; }
        if ( isset( $_GET['wporlogin_link_password'] ) && '1' === $_GET['wporlogin_link_password'] ) { return true; }
        return isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD']
            && isset( $_POST['log'], $_POST['pwd'] );
    }

    /**
     * The WordPress login form is the one visual container for account linking.
     * This markup is rendered *inside* #loginform through the login_form hook.
     */
    public static function render_confirmation() {
        $saved = self::pending();
        if ( ! $saved ) { return ''; }
        if ( isset( $_GET['action'] ) && 'login' !== $_GET['action'] ) { return ''; }
        self::no_cache();

        $password_mode = self::password_mode();
        $methods = self::available_methods( $saved );
        $cancel = add_query_arg( 'wporlogin_link_cancel', '1', home_url( '/' ) );
        $html = '<div class="wporlogin-link-confirm" role="group" aria-labelledby="wporlogin-link-title">';
        $html .= '<h2 class="wporlogin-link-title" id="wporlogin-link-title">' . esc_html__( 'Confirm your account', 'wporlogin' ) . '</h2>';

        if ( $password_mode ) {
            $html .= '<p class="wporlogin-link-description">' . esc_html__( 'Sign in using your WordPress username and password to confirm this account.', 'wporlogin' ) . '</p>';
            if ( $methods['social'] ) {
                $back = add_query_arg( 'wporlogin_link', '1', wp_login_url( self::finish_url() ) );
                $html .= '<p class="wporlogin-link-alternative"><a href="' . esc_url( $back ) . '">' . esc_html__( 'Choose another confirmation method', 'wporlogin' ) . '</a></p>';
            }
        } else {
            $html .= '<p class="wporlogin-link-description">' . esc_html__( 'This account already exists. Confirm your identity with a sign-in method you have used before. We will then connect your new provider.', 'wporlogin' ) . '</p>';
            $options = '';
            foreach ( $methods['social'] as $provider => $name ) {
                $url = add_query_arg( array( 'wporlogin_social_start' => $provider, 'wporlogin_link' => '1' ), home_url( '/' ) );
                // Reuse the same button classes and markup structure as normal
                // social login, instead of maintaining a second visual design.
                $options .= '<a class="wporlogin-social-btn wporlogin-' . esc_attr( $provider ) . '-btn" href="' . esc_url( $url ) . '">';
                $options .= '<span class="wporlogin-social-icon-wrapper" aria-hidden="true">' . self::provider_icon( $provider ) . '</span>';
                $options .= '<span class="wporlogin-social-text">' . esc_html( sprintf(
                    /* translators: %s: Social authentication provider name (for example, Google). */
                    __( 'Continue with %s', 'wporlogin' ), $name
                ) ) . '</span></a>';
            }
            if ( $methods['password'] ) {
                $url = add_query_arg( array( 'wporlogin_link' => '1', 'wporlogin_link_password' => '1' ), wp_login_url( self::finish_url() ) );
                $options .= '<a class="wporlogin-social-btn" href="' . esc_url( $url ) . '">';
                $options .= '<span class="wporlogin-social-icon-wrapper" aria-hidden="true">' . self::provider_icon( 'password' ) . '</span>';
                $options .= '<span class="wporlogin-social-text">' . esc_html__( 'Continue with password', 'wporlogin' ) . '</span></a>';
            }
            if ( $options ) {
                $html .= '<div class="wporlogin-social-container">' . $options . '</div>';
            } else {
                $html .= '<p class="wporlogin-link-description">' . esc_html__( 'No available confirmation method was found. Return to login or contact the site administrator.', 'wporlogin' ) . '</p>';
            }
        }
        $html .= '<p class="wporlogin-link-footer"><a href="' . esc_url( $cancel ) . '">' . esc_html__( 'Cancel and return to login', 'wporlogin' ) . '</a></p>';
        return $html . '</div>';
    }

    /** Small provider icons use the same branding as normal sign-in buttons. */
    private static function provider_icon( $provider ) {
        if ( 'password' === $provider ) {
            // Neutral lock icon: reuse the existing social-button icon layout.
            return '<svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>';
        }
        if ( 'facebook' === $provider ) {
            return '<svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M13.8 21v-8.2h2.75l.42-3.2H13.8V7.55c0-.92.26-1.55 1.58-1.55h1.69V3.14A22.8 22.8 0 0 0 14.63 3C12.2 3 10.55 4.48 10.55 7.2v2.4H7.8v3.2h2.75V21z"/></svg>';
        }
        if ( 'microsoft' === $provider ) {
            return '<svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24"><path fill="#f25022" d="M1 1h10v10H1z"/><path fill="#7fba00" d="M13 1h10v10H13z"/><path fill="#00a4ef" d="M1 13h10v10H1z"/><path fill="#ffb900" d="M13 13h10v10H13z"/></svg>';
        }
        if ( 'google' === $provider ) {
            return '<svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>';
        }
        return '';
    }

    /** Preserve expired-state guidance without showing a second card. */
    public static function expired_message( $message ) {
        if ( ! self::has_pending() && isset( $_GET['wporlogin_link'] ) && '1' === $_GET['wporlogin_link'] ) {
            return $message . '<p class="message">' . esc_html__( 'This confirmation request has expired. Start again using your preferred sign-in provider.', 'wporlogin' ) . '</p>';
        }
        return $message;
    }

    /** Select choice/password mode without changing how WordPress renders a login. */
    public static function login_body_class( $classes ) {
        if ( self::has_pending() ) {
            $classes[] = self::password_mode() ? 'wporlogin-link-password' : 'wporlogin-link-choice';

            // Match the contrast of the selected built-in design, without
            // changing its original stylesheet or the user's saved settings.
            $design = get_option( 'wporlogin_active_design', 'wporlogin_design_basic' );
            $dark_designs = array(
                'wporlogin_design_img_premium_one',
                'wporlogin_design_img_premium_four',
                'wporlogin_design_img_premium_six',
            );
            $classes[] = in_array( $design, $dark_designs, true )
                ? 'wporlogin-link-dark' : 'wporlogin-link-light';
        }
        return $classes;
    }

    /** Existing linked social identities are one-click confirmation choices. */
    private static function available_methods( $saved ) {
        $social = array();
        foreach ( array( 'google' => 'Google', 'facebook' => 'Facebook', 'microsoft' => 'Microsoft' ) as $provider => $name ) {
            if ( $provider === $saved['provider'] || ! Wporlogin_Social_Providers::is_available( $provider ) ) { continue; }
            $key = Wporlogin_Social_Identity::meta_key( $provider );
            if ( '' === $key ) { continue; }
            $ids = get_user_meta( $saved['user_id'], $key, false );
            if ( is_array( $ids ) && 1 === count( $ids ) && is_string( $ids[0] ) && '' !== $ids[0] ) {
                $social[ $provider ] = $name;
            }
        }
        return array(
            'social' => $social,
            'password' => Wporlogin_Social_Identity::has_password_method( (int) $saved['user_id'] ),
        );
    }

    /**
     * Account-confirmation adjustments only. The original form and social
     * button classes still supply each design's card, typography and branding.
     * One shared rule set supports all eight built-in designs and screen sizes.
     */
    public static function render_styles() {
        if ( ! self::has_pending() ) { return; }
        ?>
        <style id="wporlogin-social-link-visibility">
        /* The choices replace the normal fields, inside the original #loginform. */
        body.wporlogin-link-choice #loginform > :not(.wporlogin-link-confirm) { display: none !important; }
        body.wporlogin-link-choice #loginform .wporlogin-link-confirm .wporlogin-social-container { display: flex; }
        body.wporlogin-link-choice #nav, body.wporlogin-link-password #nav,
        body.wporlogin-link-choice #backtoblog, body.wporlogin-link-password #backtoblog { display: none !important; }

        /* Spacing and legibility: the containing card is styled by the selected theme. */
        body.login #loginform .wporlogin-link-confirm {
            box-sizing: border-box;
            width: 100%;
            min-width: 0;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.55;
            overflow-wrap: break-word;
            color: #30363d;
        }
        body.login.wporlogin-link-dark #loginform .wporlogin-link-confirm {
            color: #f4f6f8;
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-link-title {
            margin: 0 0 8px !important;
            padding: 0 !important;
            color: inherit !important;
            font-family: inherit;
            font-weight: 600;
            font-size: 19px;
            line-height: 1.3;
            letter-spacing: normal;
            text-transform: none;
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-link-description {
            margin: 0 0 20px !important;
            padding: 0 !important;
            font-size: 14px;
            line-height: 1.6;
            color: #4a5562 !important;
            font-weight: 400;
            letter-spacing: normal;
            text-transform: none;
        }
        body.login.wporlogin-link-dark #loginform .wporlogin-link-confirm .wporlogin-link-description {
            color: #e3e8ef !important;
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-social-container {
            box-sizing: border-box;
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-social-btn {
            box-sizing: border-box;
            max-width: 100%;
            min-height: 44px;
            height: auto;
            padding: 10px 12px;
            white-space: normal;
            line-height: 1.35;
            text-align: center;
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-social-icon-wrapper {
            top: 50%;
            transform: translateY(-50%);
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-social-text {
            display: block;
            min-width: 0;
            padding: 0 25px;
            overflow-wrap: anywhere;
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-link-footer,
        body.login #loginform .wporlogin-link-confirm .wporlogin-link-alternative {
            margin: 22px 0 0 !important;
            padding: 0 !important;
            text-align: center;
            font-size: 13px;
            line-height: 1.5;
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-link-alternative {
            margin-top: 14px !important;
            margin-bottom: 0 !important;
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-link-footer a,
        body.login #loginform .wporlogin-link-confirm .wporlogin-link-alternative a {
            display: inline-block;
            padding: 3px 0;
            color: #285889 !important;
            text-decoration: none;
            font-size: inherit;
            line-height: inherit;
            font-weight: 500;
        }
        body.login.wporlogin-link-dark #loginform .wporlogin-link-confirm .wporlogin-link-footer a,
        body.login.wporlogin-link-dark #loginform .wporlogin-link-confirm .wporlogin-link-alternative a {
            color: #e3eaff !important;
        }
        body.login #loginform .wporlogin-link-confirm .wporlogin-link-footer a:hover,
        body.login #loginform .wporlogin-link-confirm .wporlogin-link-alternative a:hover {
            text-decoration: underline;
        }
        body.login #loginform .wporlogin-link-confirm a:focus-visible {
            outline: 2px solid currentColor;
            outline-offset: 3px;
            border-radius: 2px;
        }
        @media (max-width: 782px) {
            body.login #loginform .wporlogin-link-confirm .wporlogin-link-title { font-size: 18px; }
            body.login #loginform .wporlogin-link-confirm .wporlogin-social-container { gap: 12px; }
        }
        @media (max-width: 480px) {
            body.login #loginform .wporlogin-link-confirm .wporlogin-link-description {
                line-height: 1.55;
                margin-bottom: 18px !important;
            }
            body.login #loginform .wporlogin-link-confirm .wporlogin-link-footer { margin-top: 20px !important; }
        }
        /* Some existing designs set 350px for small screens; prevent horizontal scroll. */
        @media (max-width: 400px) {
            body.login.wporlogin-link-choice #login,
            body.login.wporlogin-link-password #login {
                box-sizing: border-box;
                width: 100% !important;
                max-width: 100% !important;
            }
            body.login.wporlogin-link-choice #loginform,
            body.login.wporlogin-link-password #loginform {
                box-sizing: border-box;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
        </style>
        <?php
    }
}
