<?php
/**
 * Provider availability for Social Login and its shared registration settings.
 *
 * "Available" means enabled, with credentials saved and the provider code
 * present. It does NOT mean that remote OAuth credentials were tested.
 *
 * @package Wporlogin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Wporlogin_Social_Providers {
    /**
     * Return enabled providers that have the required local configuration.
     * New providers may register themselves using the filter below.
     *
     * @return string[] Provider keys.
     */
    public static function available() {
        $available = array();

        // Google is optional as well: a missing provider class must not
        // prevent Facebook or Microsoft from loading their own buttons.
        if (
            is_readable( __DIR__ . '/class-wporlogin-social-google.php' ) &&
            is_readable( __DIR__ . '/class-wporlogin-google-handler.php' ) &&
            '1' === (string) get_option( 'wporlogin_google_enable', '0' ) &&
            '' !== trim( (string) get_option( 'wporlogin_google_client_id', '' ) ) &&
            '' !== trim( (string) get_option( 'wporlogin_google_client_secret', '' ) )
        ) {
            $available[] = 'google';
        }

        // Facebook is removable without affecting Google or normal sign-in.
        if (
            is_readable( __DIR__ . '/class-wporlogin-social-facebook.php' ) &&
            is_readable( __DIR__ . '/class-wporlogin-facebook-handler.php' ) &&
            '1' === (string) get_option( 'wporlogin_facebook_enable', '0' ) &&
            '' !== trim( (string) get_option( 'wporlogin_facebook_app_id', '' ) ) &&
            '' !== trim( (string) get_option( 'wporlogin_facebook_app_secret', '' ) )
        ) {
            $available[] = 'facebook';
        }

        // Microsoft is an independent, removable OIDC provider.
        if ( is_readable( __DIR__ . '/class-wporlogin-social-microsoft.php' ) &&
            is_readable( __DIR__ . '/class-wporlogin-microsoft-handler.php' ) ) {
            require_once __DIR__ . '/class-wporlogin-social-microsoft.php';
            // Use exactly the same callback/configuration requirements as the handler.
            if ( Wporlogin_Social_Microsoft::is_configured() ) {
                $available[] = 'microsoft';
            }
        }

        /**
         * Add provider keys for future optional sign-in integrations.
         * A provider should only declare itself available when its implementation
         * is loaded and the site administrator has enabled/configured it.
         *
         * @param string[] $available Enabled provider keys.
         */
        // External providers may extend (or filter) the available list, but
        // filters must not re-enable a built-in provider disabled in settings
        // or missing credentials. Every built-in module uses this same policy.
        $configured_builtin = array_fill_keys( $available, true );
        $filtered = apply_filters( 'wporlogin_available_social_providers', $available );
        if ( ! is_array( $filtered ) ) {
            return array();
        }

        $enabled = array();
        foreach ( $filtered as $provider ) {
            if ( ! is_string( $provider ) ) {
                continue;
            }
            $provider = sanitize_key( $provider );
            if ( '' === $provider ) {
                continue;
            }
            if ( in_array( $provider, array( 'google', 'facebook', 'microsoft' ), true ) &&
                ! isset( $configured_builtin[ $provider ] ) ) {
                continue;
            }
            $enabled[ $provider ] = true;
        }
        return array_keys( $enabled );
    }

    /** @return bool */
    public static function has_available() {
        return ! empty( self::available() );
    }

    /** @param string $provider Provider key. @return bool */
    public static function is_available( $provider ) {
        return in_array( sanitize_key( (string) $provider ), self::available(), true );
    }
}
