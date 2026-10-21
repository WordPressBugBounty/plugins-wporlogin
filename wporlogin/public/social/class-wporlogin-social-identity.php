<?php
/**
 * Shared WordPress email lookup, registration and identity persistence.
 * Provider handlers supply profiles only after validating their own protocol.
 * This service accepts only profiles validated by the provider handlers.
 *
 * @package Wporlogin
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Wporlogin_Social_Identity {
    const ORIGIN_META = 'wporlogin_social_account_origin';
    const PASSWORD_META = 'wporlogin_social_password_enabled';

    /**
     * A social-created user gets a random, undisclosed WP password, which
     * must NOT count as an existing password sign-in method. Older users
     * with linked providers have ambiguous origins: do not guess a password.
     * Ordinary WordPress-only accounts and users who explicitly set/reset a
     * password can verify through core WP login.
     */
    public static function has_password_method( $user_id ) {
        $user_id = absint( $user_id );
        if ( ! $user_id || ! get_userdata( $user_id ) ) { return false; }
        if ( '1' === (string) get_user_meta( $user_id, self::PASSWORD_META, true ) ) { return true; }
        if ( 'social' === (string) get_user_meta( $user_id, self::ORIGIN_META, true ) ) { return false; }
        if ( 'native' === (string) get_user_meta( $user_id, self::ORIGIN_META, true ) ) { return true; }
        // No metadata on legacy accounts. A social identity alone cannot
        // prove whether their stored password was ever known to the owner.
        foreach ( array( 'google', 'facebook', 'microsoft' ) as $provider ) {
            $key = self::meta_key( $provider );
            if ( $key && get_user_meta( $user_id, $key, true ) ) { return false; }
        }
        return true;
    }

    /** Successful native confirmation or password reset establishes a method. */
    public static function record_password_method( $user_id ) {
        $user_id = absint( $user_id );
        if ( $user_id && get_userdata( $user_id ) ) {
            update_user_meta( $user_id, self::PASSWORD_META, '1' );
        }
    }

    /** WordPress resets explicitly set a password, even for social accounts. */
    public static function after_password_reset( $user ) {
        if ( $user instanceof WP_User ) { self::record_password_method( $user->ID ); }
    }

    /** wp_set_password() also changes the login credential. */
    public static function after_wp_set_password( $password, $user_id ) {
        self::record_password_method( $user_id );
    }

    /** Detect changes to a password in the native WordPress profile editor. */
    public static function profile_updated( $user_id, $old_user_data ) {
        $user = get_userdata( $user_id );
        if ( $user instanceof WP_User && $old_user_data instanceof WP_User &&
            $old_user_data->user_pass !== $user->user_pass ) {
            self::record_password_method( $user_id );
        }
    }

    private static $locks = array();

    public static function meta_key( $provider ) {
        $keys = apply_filters( 'wporlogin_social_identity_meta_keys', array(
            'google' => 'wporlogin_google_sub', 'facebook' => 'wporlogin_facebook_id', 'microsoft' => 'wporlogin_microsoft_id',
        ) );
        return is_string( $provider ) && is_array( $keys ) && isset( $keys[ $provider ] ) &&
            is_string( $keys[ $provider ] ) && preg_match( '/\A[a-zA-Z0-9_]{1,100}\z/', $keys[ $provider ] ) ? $keys[ $provider ] : '';
    }

    private static function valid_identity( $provider, $id ) {
        if ( ! is_string( $id ) || '' === $id ) { return false; }
        if ( 'google' === $provider ) { return strlen( $id ) <= 255 && ! preg_match( '/[\x00-\x20\x7f]/', $id ); }
        if ( 'facebook' === $provider ) { return strlen( $id ) <= 255 && (bool) preg_match( '/\A[0-9]+\z/', $id ); }
        if ( 'microsoft' === $provider ) {
            return (bool) preg_match( '/\A[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}:[\x21-\x7e]{1,255}\z/', $id );
        }
        return false;
    }

    /** Validate the original address; do not transform malformed input into another address. */
    public static function valid_email( $value ) {
        if ( ! is_string( $value ) ) { return ''; }
        $email = trim( $value );
        return is_email( $email ) && sanitize_email( $email ) === $email ? $email : '';
    }

    /** Exact, case-sensitive provider identifiers, including Microsoft subjects. */
    public static function lookup( $provider, $identity ) {
        $key = self::meta_key( $provider );
        if ( ! $key || ! self::valid_identity( $provider, $identity ) ) { return new WP_Error( 'wporlogin_identity_invalid' ); }
        global $wpdb;
        $wpdb->last_error = '';
        $users = get_users( array( 'number' => 2, 'fields' => 'all', 'count_total' => false,
            'meta_query' => array( array( 'key' => $key, 'value' => $identity, 'compare' => '=', 'type' => 'BINARY' ) ),
        ) );
        if ( ! is_array( $users ) || $wpdb->last_error ) { return new WP_Error( 'wporlogin_account_lookup_failed' ); }
        if ( count( $users ) > 1 ) { return new WP_Error( 'wporlogin_identity_duplicate' ); }
        return $users ? $users[0] : null;
    }

    /** Reject ambiguous WordPress email addresses instead of choosing the first user. */
    private static function find_email_user( $email ) {
        if ( '' === $email ) { return null; }
        global $wpdb;
        $ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} WHERE user_email = %s LIMIT 2", $email ) );
        if ( ! is_array( $ids ) || $wpdb->last_error ) { return new WP_Error( 'wporlogin_account_lookup_failed' ); }
        if ( count( $ids ) > 1 ) { return new WP_Error( 'wporlogin_identity_duplicate' ); }
        if ( ! $ids ) { return null; }
        $user = get_userdata( (int) $ids[0] );
        return $user instanceof WP_User ? $user : new WP_Error( 'wporlogin_account_lookup_failed' );
    }

    /** Return the sole target of a pending first-time link, never authorize it. */
    public static function candidate_for_link( $provider, $profile ) {
        if ( ! Wporlogin_Social_Providers::is_available( $provider ) ) { return new WP_Error( 'wporlogin_social_unavailable' ); }
        $data = self::normalize_profile( $provider, $profile );
        if ( is_wp_error( $data ) || '' === $data['email'] ) { return new WP_Error( 'wporlogin_identity_invalid' ); }
        $linked = self::lookup( $provider, $data['id'] );
        $user = self::find_email_user( $data['email'] );
        if ( is_wp_error( $linked ) ) { return $linked; }
        if ( is_wp_error( $user ) ) { return $user; }
        if ( $linked || ! $user ) { return new WP_Error( 'wporlogin_identity_conflict' ); }
        $access = self::check_user_access( $user, $provider );
        return is_wp_error( $access ) ? $access : $user;
    }

    /** Normalize provider profile fields without retaining access tokens. */
    private static function normalize_profile( $provider, $profile ) {
        if ( ! is_array( $profile ) || ! isset( $profile['id'] ) || ! self::valid_identity( $provider, $profile['id'] ) ) {
            return new WP_Error( 'wporlogin_identity_invalid' );
        }
        $result = array( 'id' => $profile['id'], 'email' => self::valid_email( $profile['email'] ?? '' ) );
        // Google userinfo supplies an explicit boolean; never infer verification
        // from the mere presence of an address or from another provider.
        $result['email_verified'] = 'google' === $provider && true === ( $profile['email_verified'] ?? false );
        foreach ( array( 'name', 'first_name', 'last_name' ) as $field ) {
            $result[ $field ] = isset( $profile[ $field ] ) && is_string( $profile[ $field ] )
                ? substr( sanitize_text_field( $profile[ $field ] ), 0, 250 ) : '';
        }
        $picture = isset( $profile['picture'] ) && is_string( $profile['picture'] ) ? $profile['picture'] : '';
        $result['picture'] = strlen( $picture ) <= 2048 ? esc_url_raw( $picture, array( 'https' ) ) : '';
        return $result;
    }

    /** Backward-compatible Google entry point. */
    public static function resolve_google_by_email( $profile ) { return self::resolve( 'google', $profile ); }

    /**
     * Resolve by a previously linked, validated provider identity only.
     * A matching WordPress email is a candidate for future account linking,
     * NOT proof of authorization. The guided confirmation flow is added in
     * step 3; until then fail closed for unlinked existing addresses.
     * New registrations still respect native WordPress registration settings.
     */
    public static function resolve( $provider, $profile ) {
        if ( ! Wporlogin_Social_Providers::is_available( $provider ) ) { return new WP_Error( 'wporlogin_social_unavailable' ); }
        $profile = self::normalize_profile( $provider, $profile );
        if ( is_wp_error( $profile ) ) { return $profile; }
        $linked = self::lookup( $provider, $profile['id'] );
        if ( is_wp_error( $linked ) ) { return $linked; }
        $by_email = self::find_email_user( $profile['email'] );
        if ( is_wp_error( $by_email ) ) { return $by_email; }
        $target = $linked ?: $by_email;
        $resources = array( 'identity:' . $provider . ':' . $profile['id'] );
        if ( '' !== $profile['email'] ) { $resources[] = 'email:' . strtolower( $profile['email'] ); }
        if ( $target ) { $resources[] = 'user:' . $provider . ':' . $target->ID; }
        $result = self::with_locks( $resources, static function () use ( $provider, $profile ) {
            return self::resolve_locked( $provider, $profile );
        } );
        if ( is_wp_error( $result ) ) { return $result; }
        if ( $result['created'] ) { wp_send_new_user_notifications( $result['user']->ID, 'both' ); }
        return $result['user'];
    }

    private static function resolve_locked( $provider, $profile ) {
        $id = $profile['id'];
        $linked = self::lookup( $provider, $id );
        $by_email = self::find_email_user( $profile['email'] );
        if ( is_wp_error( $linked ) ) { return $linked; }
        if ( is_wp_error( $by_email ) ) { return $by_email; }
        if ( $linked && $by_email && (int) $linked->ID !== (int) $by_email->ID ) {
            return new WP_Error( 'wporlogin_identity_conflict' );
        }
        $user = $linked ?: $by_email;
        $email = $linked ? self::valid_email( $linked->user_email ) : $profile['email'];
        if ( $user ) {
            $saved_ids = get_user_meta( $user->ID, self::meta_key( $provider ), false );
            if ( count( $saved_ids ) > 1 || ( $saved_ids && '' !== $saved_ids[0] && ( ! is_string( $saved_ids[0] ) || ! hash_equals( $id, $saved_ids[0] ) ) ) ) {
                return new WP_Error( 'wporlogin_identity_conflict' );
            }
            $access = self::check_user_access( $user, $provider );
            if ( is_wp_error( $access ) ) { return $access; }
        }
        if ( $linked ) { return array( 'user' => $linked, 'created' => false ); }
        // First Google sign-in for an existing account: save the provider ID
        // only after Google confirms the email. Future logins use that ID.
        // Do not overwrite an existing ID or bypass account restrictions.
        if ( $user && 'google' === $provider ) {
            if ( ! $profile['email_verified'] ) {
                return new WP_Error( 'wporlogin_social_email_unverified' );
            }
            $saved = self::link_identity( 'google', $id, $user->ID );
            return is_wp_error( $saved ) ? $saved : array( 'user' => $user, 'created' => false );
        }
        // Facebook and Microsoft still require account confirmation.
        if ( $user ) { return new WP_Error( 'wporlogin_link_confirmation_required' ); }
        if ( '' === $email ) { return new WP_Error( 'wporlogin_social_email_missing' ); }
        if ( 'google' === $provider && ! $profile['email_verified'] ) {
            return new WP_Error( 'wporlogin_social_email_unverified' );
        }
        if ( ! get_option( 'users_can_register' ) ) { return new WP_Error( 'wporlogin_registration_closed' ); }
        $created = false;
        if ( ! $user ) {
            $user = self::create_account( $email, $profile );
            if ( is_wp_error( $user ) ) { return $user; }
            $created = true;
            $unique = self::find_email_user( $email );
            $access = self::check_user_access( $user, $provider );
            if ( is_wp_error( $unique ) || ! $unique || (int) $unique->ID !== (int) $user->ID || is_wp_error( $access ) ) {
                self::rollback_account( $user->ID, $provider, $id, $email );
                return is_wp_error( $access ) ? $access : new WP_Error( 'wporlogin_identity_duplicate' );
            }
        }
        $before = get_user_meta( $user->ID, self::meta_key( $provider ), true );
        $saved = self::link_identity( $provider, $id, $user->ID );
        if ( is_wp_error( $saved ) ) {
            if ( '' === $before ) { delete_user_meta( $user->ID, self::meta_key( $provider ), $id ); }
            if ( $created ) { self::rollback_account( $user->ID, $provider, $id, $email ); }
            return $saved;
        }
        return array( 'user' => $user, 'created' => $created );
    }

    /** Serialize this plugin's writes by both external identity and user/provider. */
    public static function link_identity( $provider, $identity, $user_id ) {
        $user_id = absint( $user_id );
        if ( ! self::valid_identity( $provider, $identity ) || ! $user_id || ! self::meta_key( $provider ) ) {
            return new WP_Error( 'wporlogin_identity_invalid' );
        }
        return self::with_locks( array( 'identity:' . $provider . ':' . $identity, 'user:' . $provider . ':' . $user_id ), static function () use ( $provider, $identity, $user_id ) {
            $linked = self::lookup( $provider, $identity );
            if ( is_wp_error( $linked ) || ( $linked && (int) $linked->ID !== $user_id ) || ! get_userdata( $user_id ) ) {
                return new WP_Error( 'wporlogin_identity_already_linked' );
            }
            $key = self::meta_key( $provider );
            $values = get_user_meta( $user_id, $key, false );
            if ( count( $values ) > 1 || ( $values && '' !== $values[0] && ( ! is_string( $values[0] ) || ! hash_equals( $identity, $values[0] ) ) ) ) {
                return new WP_Error( 'wporlogin_identity_already_linked' );
            }
            $inserted = ! $values || '' === $values[0];
            if ( $inserted ) {
                $saved = $values ? update_user_meta( $user_id, $key, $identity ) : add_user_meta( $user_id, $key, $identity, true );
                if ( ! $saved ) { return new WP_Error( 'wporlogin_identity_save_failed' ); }
            }
            $confirmed = self::lookup( $provider, $identity );
            if ( is_wp_error( $confirmed ) || ! $confirmed || (int) $confirmed->ID !== $user_id ) {
                if ( $inserted ) { delete_user_meta( $user_id, $key, $identity ); }
                return new WP_Error( 'wporlogin_identity_duplicate' );
            }
            return true;
        } );
    }

    private static function create_account( $email, $profile ) {
        if ( ! get_option( 'users_can_register' ) ) { return new WP_Error( 'wporlogin_registration_closed' ); }
        $base = substr( sanitize_user( strstr( $email, '@', true ), true ), 0, 40 );
        if ( '' === $base ) { $base = 'socialuser'; }
        $username = $base;
        for ( $i = 0; username_exists( $username ) && $i < 15; $i++ ) { $username = $base . strtolower( wp_generate_password( 8, false, false ) ); }
        if ( username_exists( $username ) ) { return new WP_Error( 'wporlogin_registration_failed' ); }
        $id = wp_insert_user( array( 'user_login' => $username, 'user_pass' => wp_generate_password( 32, true, true ), 'user_email' => $email,
            'first_name' => $profile['first_name'], 'last_name' => $profile['last_name'], 'display_name' => '' !== $profile['name'] ? $profile['name'] : $username,
        ) );
        if ( is_wp_error( $id ) ) { return $id; }
        // Generated during OAuth registration: the user was not given this
        // random WordPress password. A later reset can enable native sign-in.
        update_user_meta( $id, self::ORIGIN_META, 'social' );
        $user = get_userdata( $id );
        return $user instanceof WP_User ? $user : new WP_Error( 'wporlogin_registration_failed' );
    }

    private static function rollback_account( $user_id, $provider, $identity, $email ) {
        $user = get_userdata( $user_id );
        if ( ! $user || 0 !== strcasecmp( $user->user_email, $email ) ) { return; }
        // A slow/crashed request's lease can expire. Never delete a new account
        // that another completed association has since made usable.
        foreach ( array( 'google', 'facebook', 'microsoft' ) as $other ) {
            foreach ( get_user_meta( $user_id, self::meta_key( $other ), false ) as $saved ) {
                if ( '' !== $saved && ( $provider !== $other || ! is_string( $saved ) || ! hash_equals( $identity, $saved ) ) ) { return; }
            }
        }
        if ( ! function_exists( 'wp_delete_user' ) ) { require_once ABSPATH . 'wp-admin/includes/user.php'; }
        wp_delete_user( $user_id );
    }

    /** Run account restrictions before any social session is issued. */
    public static function check_user_access( $user, $provider ) {
        if ( ! $user instanceof WP_User || ( ! empty( $user->spam ) ) || ( ! empty( $user->deleted ) ) ) {
            return new WP_Error( 'wporlogin_social_access_denied' );
        }
        $checked = apply_filters( 'wp_authenticate_user', $user, '' );
        if ( is_wp_error( $checked ) ) { return $checked; }
        if ( ! $checked instanceof WP_User || (int) $checked->ID !== (int) $user->ID ) { return new WP_Error( 'wporlogin_social_access_denied' ); }
        $checked = apply_filters( 'wporlogin_social_authenticate_user', $checked, $provider );
        return is_wp_error( $checked ) ? $checked : ( $checked instanceof WP_User && (int) $checked->ID === (int) $user->ID ? $checked : new WP_Error( 'wporlogin_social_access_denied' ) );
    }

    /** Atomic option-name acquisition; bounded leases recover after a PHP crash. */
    public static function with_locks( $resources, $callback ) {
        $keys = array();
        foreach ( $resources as $resource ) { $keys[] = 'wporlogin_social_lock_' . hash( 'sha256', $resource ); }
        $keys = array_unique( $keys ); sort( $keys, SORT_STRING ); $acquired = array();
        try {
            foreach ( $keys as $key ) {
                if ( isset( self::$locks[ $key ] ) ) { self::$locks[ $key ]['depth']++; $acquired[] = $key; continue; }
                $value = ( time() + 300 ) . '|' . bin2hex( random_bytes( 16 ) );
                if ( ! add_option( $key, $value, '', 'no' ) ) {
                    $old = get_option( $key );
                    if ( is_string( $old ) && preg_match( '/\A([0-9]+)\|[a-f0-9]{32}\z/', $old, $match ) && (int) $match[1] < time() ) {
                        self::delete_owned_lock( $key, $old );
                    }
                    if ( ! add_option( $key, $value, '', 'no' ) ) { return new WP_Error( 'wporlogin_social_busy' ); }
                }
                self::$locks[ $key ] = array( 'value' => $value, 'depth' => 1 ); $acquired[] = $key;
            }
            return call_user_func( $callback );
        } catch ( Exception $exception ) {
            return new WP_Error( 'wporlogin_social_operation_failed' );
        } finally {
            foreach ( array_reverse( $acquired ) as $key ) {
                self::$locks[ $key ]['depth']--;
                if ( 0 === self::$locks[ $key ]['depth'] ) { self::delete_owned_lock( $key, self::$locks[ $key ]['value'] ); unset( self::$locks[ $key ] ); }
            }
        }
    }

    private static function delete_owned_lock( $key, $value ) {
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", $key, $value ) );
        wp_cache_delete( $key, 'options' ); wp_cache_delete( 'notoptions', 'options' );
    }

    public static function notice_for_error( $error ) {
        $code = is_wp_error( $error ) ? $error->get_error_code() : '';
        $map = array( 'wporlogin_registration_closed' => 'account_not_found', 'wporlogin_social_email_missing' => 'social_email_missing',
            'wporlogin_social_unavailable' => 'social_unavailable', 'wporlogin_social_busy' => 'social_busy',
            'wporlogin_locked' => 'social_locked',
            'wporlogin_identity_conflict' => 'social_identity_conflict', 'wporlogin_identity_duplicate' => 'social_identity_conflict',
            'wporlogin_identity_already_linked' => 'social_identity_conflict',
            'wporlogin_link_confirmation_required' => 'social_link_confirmation_required',
            'wporlogin_social_email_unverified' => 'social_email_unverified',
        );
        return $map[ $code ] ?? 'social_access_failed';
    }
}
