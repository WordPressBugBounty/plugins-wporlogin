<?php
/**
 * Provider-independent avatar storage and display.
 *
 * Reuses the existing wporlogin_social_avatar meta key so Google users keep
 * their profile pictures after upgrading. Does not download media to WordPress.
 *
 * @package Wporlogin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Wporlogin_Social_Avatar {
    const AVATAR_META   = 'wporlogin_social_avatar';
    const PROVIDER_META = 'wporlogin_social_avatar_provider';

    /**
     * Record the picture of the latest successful sign-in provider.
     * If that provider did not supply a usable HTTPS image, fall back to the
     * normal WordPress avatar rather than keeping a previous provider's photo.
     *
     * @param int    $user_id  Authenticated WP user ID.
     * @param string $provider Provider key, e.g. google or facebook.
     * @param string $picture  URL supplied by verified provider response.
     * @return void
     */
    public static function update_on_login( $user_id, $provider, $picture ) {
        $user_id  = absint( $user_id );
        $provider = sanitize_key( (string) $provider );
        if ( ! $user_id || ! $provider ) {
            return;
        }

        update_user_meta( $user_id, self::PROVIDER_META, $provider );
        $url = self::secure_picture_url( $picture );
        if ( '' === $url ) {
            delete_user_meta( $user_id, self::AVATAR_META );
            return;
        }
        update_user_meta( $user_id, self::AVATAR_META, $url );
    }

    /**
     * Validate a provider-supplied image URL before storing or displaying it.
     * URLs are not obtained from visitor POST or GET data.
     *
     * @param mixed $url Provider image URL.
     * @return string Valid HTTPS URL or empty string.
     */
    private static function secure_picture_url( $url ) {
        if ( ! is_string( $url ) || '' === $url ) {
            return '';
        }
        $url = esc_url_raw( $url, array( 'https', 'http' ) );
        if ( '' === $url || ! wp_parse_url( $url, PHP_URL_HOST ) ) {
            return '';
        }
        $scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );
        if ( 'https' === $scheme ) {
            return $url;
        }
        // Microsoft Graph photos are uploaded as actual image files by WP.
        // An HTTP localhost site cannot serve those images over HTTPS.
        // Allow *only* our own named avatar image on the site's own HTTP origin;
        // external Google/Facebook URLs continue to require HTTPS.
        $home = home_url( '/' );
        $home_scheme = strtolower( (string) wp_parse_url( $home, PHP_URL_SCHEME ) );
        $url_host  = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
        $home_host = strtolower( (string) wp_parse_url( $home, PHP_URL_HOST ) );
        $url_port  = wp_parse_url( $url, PHP_URL_PORT );
        $home_port = wp_parse_url( $home, PHP_URL_PORT );
        $path      = (string) wp_parse_url( $url, PHP_URL_PATH );
        if (
            'http' === $scheme && 'http' === $home_scheme &&
            $url_host === $home_host &&
            (int) ( $url_port ? $url_port : 80 ) === (int) ( $home_port ? $home_port : 80 ) &&
            preg_match( '~\/wporlogin-microsoft-avatar-[0-9]+(?:-[0-9]+)?\.(?:jpg|png|webp)\z~i', $path )
        ) {
            return $url;
        }
        return '';
    }

    /**
     * Resolve a saved picture only when the global setting and its provider
     * are enabled. Called from both markup and avatar-URL filters.
     *
     * @param mixed $id_or_email WordPress avatar identity.
     * @return string HTTPS image URL, or an empty string for WP fallback.
     */
    private static function picture_for_identity( $id_or_email ) {
        if ( '1' !== (string) get_option( 'wporlogin_social_avatar_sync', '0' ) ) {
            return '';
        }

        $user_id = 0;
        if ( is_numeric( $id_or_email ) ) {
            $user_id = absint( $id_or_email );
        } elseif ( is_object( $id_or_email ) ) {
            // A comment's ID is NOT a WordPress user ID. For anonymous
            // comments, preserve the normal avatar instead of guessing.
            if ( isset( $id_or_email->user_id ) ) {
                $user_id = absint( $id_or_email->user_id );
            } elseif ( isset( $id_or_email->ID ) ) {
                $user_id = absint( $id_or_email->ID );
            }
        } elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
            $user = get_user_by( 'email', $id_or_email );
            $user_id = $user ? absint( $user->ID ) : 0;
        }
        if ( ! $user_id ) {
            return '';
        }

        // Older Google avatars may lack a provider marker; preserve them.
        $provider = (string) get_user_meta( $user_id, self::PROVIDER_META, true );
        if ( '' === $provider ) {
            $provider = 'google';
        }
        if ( ! Wporlogin_Social_Providers::is_available( $provider ) ) {
            return '';
        }

        return self::secure_picture_url( get_user_meta( $user_id, self::AVATAR_META, true ) );
    }

    /**
     * Keep WordPress's avatar URL API and REST responses consistent with the
     * avatar HTML shown in wp-admin and on the public site.
     *
     * @param array $args        Data returned by get_avatar_data().
     * @param mixed $id_or_email WordPress avatar identity.
     * @return array
     */
    public static function filter_avatar_data( $args, $id_or_email ) {
        if ( ! is_array( $args ) ) {
            return $args;
        }
        $picture = self::picture_for_identity( $id_or_email );
        if ( '' !== $picture ) {
            $args['url']          = $picture;
            $args['found_avatar'] = true;
        }
        return $args;
    }

    /**
     * Replace Gravatar markup throughout WordPress when a social picture is
     * allowed. Keep the existing markup to avoid a frontend UI regression.
     *
     * @param string $avatar      WordPress avatar markup.
     * @param mixed  $id_or_email WordPress avatar identity.
     * @param int    $size        Requested display size.
     * @param string $default     WordPress default avatar (not modified).
     * @param string $alt         Alternative text.
     * @return string
     */
    public static function filter_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
        $picture = self::picture_for_identity( $id_or_email );
        if ( '' === $picture ) {
            return $avatar;
        }

        $size = max( 1, absint( $size ) );
        return sprintf(
            '<img alt="%s" src="%s" referrerpolicy="no-referrer" class="avatar avatar-%d photo" height="%d" width="%d" loading="lazy" />',
            esc_attr( (string) $alt ),
            esc_url( $picture ),
            $size,
            $size,
            $size
        );
    }

}
