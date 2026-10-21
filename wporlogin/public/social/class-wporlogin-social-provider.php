<?php
/**
 * Clase abstracta para estandarizar proveedores sociales.
 * Futuros proveedores (Facebook, etc.) extenderán de esta clase.
 */
abstract class Wporlogin_Social_Provider {
    
    protected $client_id;
    protected $client_secret;

    /** Safe, internal error category; never contains a token or remote message. */
    protected $last_error = 'connection';

    public function __construct( $client_id, $client_secret ) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    /** @return string connection, configuration or cancelled. */
    public function get_last_error() {
        return $this->last_error;
    }

    /** Classify OAuth protocol errors without displaying remote descriptions. */
    public static function classify_oauth_error( $error ) {
        if ( ! is_string( $error ) ) {
            return 'connection';
        }
        if ( 'access_denied' === $error ) {
            return 'cancelled';
        }
        if ( in_array( $error, array( 'invalid_client', 'unauthorized_client', 'invalid_scope', 'redirect_uri_mismatch' ), true ) ) {
            return 'configuration';
        }
        return 'connection';
    }

    // Método que debe devolver la URL de autorización
    abstract public function get_auth_url();

    // Método para manejar la respuesta del proveedor y obtener datos del usuario
    abstract public function verify_token( $code );

    // Método para pintar el botón en el formulario
    abstract public function render_button();
}
