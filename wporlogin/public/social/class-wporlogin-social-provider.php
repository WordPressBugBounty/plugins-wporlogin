<?php
/**
 * Clase abstracta para estandarizar proveedores sociales.
 * Futuros proveedores (Facebook, etc.) extenderán de esta clase.
 */
abstract class Wporlogin_Social_Provider {
    
    protected $client_id;
    protected $client_secret;

    public function __construct( $client_id, $client_secret ) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    // Método que debe devolver la URL de autorización
    abstract public function get_auth_url();

    // Método para manejar la respuesta del proveedor y obtener datos del usuario
    abstract public function verify_token( $code );

    // Método para pintar el botón en el formulario
    abstract public function render_button();
}