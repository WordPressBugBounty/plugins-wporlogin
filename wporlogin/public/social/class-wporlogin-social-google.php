<?php
require_once 'class-wporlogin-social-provider.php';

class Wporlogin_Social_Google extends Wporlogin_Social_Provider {

    public function get_auth_url() {
        $redirect_uri = home_url( '/?wporlogin_social_auth=google' );
        $params = [
            'response_type' => 'code',
            'client_id'     => $this->client_id,
            'redirect_uri'  => $redirect_uri,
            'scope'         => 'email profile',
            'access_type'   => 'online'
        ];
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query( $params );
    }

    public function verify_token( $code ) {
        $redirect_uri = home_url( '/?wporlogin_social_auth=google' );
        
        // 1. Intercambiar código por token
        $response = wp_remote_post( 'https://oauth2.googleapis.com/token', [
            'body' => [
                'code'          => $code,
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code'
            ]
        ]);

        if ( is_wp_error( $response ) ) return false;
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! isset( $body['access_token'] ) ) return false;

        // 2. Obtener datos del usuario
        $user_info = wp_remote_get( 'https://www.googleapis.com/oauth2/v2/userinfo', [
            'headers' => [ 'Authorization' => 'Bearer ' . $body['access_token'] ]
        ]);

        if ( is_wp_error( $user_info ) ) return false;

        return json_decode( wp_remote_retrieve_body( $user_info ), true );
    }

    // Modificamos para aceptar un argumento de texto (opcional)
    public function render_button( $custom_text = null ) {
        $url = $this->get_auth_url();
        
        // Si no nos pasan texto, usamos el predeterminado
        if ( ! $custom_text ) {
            $custom_text = __( 'Sign in with Google', 'wporlogin' );
        }

        // SVG Oficial de Google (Mismo de antes)
        $google_svg = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 48 48" class="abcRioButtonSvg">' .
            '<g><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>' .
            '<path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>' .
            '<path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>' .
            '<path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path></g>' .
            '</svg>';

        return '<a href="' . esc_url( $url ) . '" class="wporlogin-social-btn wporlogin-google-btn">' .
               '<div class="wporlogin-social-icon-wrapper">' . $google_svg . '</div>' .
               '<span class="wporlogin-social-text">' . esc_html( $custom_text ) . '</span>' .
               '</a>';
    }
}