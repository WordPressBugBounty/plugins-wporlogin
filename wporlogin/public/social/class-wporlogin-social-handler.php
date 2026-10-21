<?php
/**
 * Contract for independently loaded social authentication handlers.
 *
 * Protocol validation belongs to each provider. WordPress account lookup and registration
 * belong to the shared identity service.
 *
 * @package Wporlogin
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

interface Wporlogin_Social_Handler {
    /** Handle only this provider's local OAuth entry points. @return void */
    public function handle_request();

    /** Return escaped button HTML. @param bool $register @return string */
    public function render_button( $register = false );

    /** Finish a session for an account already resolved by the shared policy. */
    public function complete_verified_login( $user, $profile, $requested_redirect );
}
