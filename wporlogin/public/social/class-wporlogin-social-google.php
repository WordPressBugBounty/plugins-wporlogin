<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once __DIR__ . '/class-wporlogin-social-provider.php';

class Wporlogin_Social_Google extends Wporlogin_Social_Provider {

    /**
     * El botón apunta a una URL local sin datos efímeros, compatible con páginas
     * de login cacheadas. El servidor genera el state al recibir el clic.
     *
     * @return string URL local de inicio del flujo.
     */
    public function get_auth_url() {
        $url = add_query_arg( 'wporlogin_social_start', 'google', home_url( '/' ) );

        // Propagar el destino solicitado únicamente al inicio del flujo.
        // El servidor volverá a validarlo antes de guardarlo en OAuth state.
        if ( isset( $_GET['redirect_to'] ) && is_string( $_GET['redirect_to'] ) ) {
            $requested = wp_unslash( $_GET['redirect_to'] );
            $requested = wp_validate_redirect( $requested, '' );
            if ( '' !== $requested ) {
                // add_query_arg() NO codifica nuevos valores: preservar ? y & internos.
                $url = add_query_arg( 'redirect_to', rawurlencode( $requested ), $url );
            }
        }

        return $url;
    }

    /**
     * Crea la URL real de autorización una vez generado el state en el servidor.
     * Mantiene el redirect_uri existente; NO hay que modificar Google Cloud.
     *
     * @param string $state Valor aleatorio asociado al navegador del usuario.
     * @return string URL de Google.
     */
    public function get_google_authorization_url( $state ) {
        $redirect_uri = home_url( '/?wporlogin_social_auth=google' );
        $params = [
            'response_type' => 'code',
            'client_id'     => $this->client_id,
            'redirect_uri'  => $redirect_uri,
            'scope'         => 'email profile',
            'access_type'   => 'online',
            'state'         => $state,
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query( $params );
    }

    public function verify_token( $code ) {
        $this->last_error = 'connection';
        if ( ! is_string( $code ) || '' === trim( $code ) ) {
            return false;
        }
        $redirect_uri = home_url( '/?wporlogin_social_auth=google' );
        
        // 1. Intercambiar código por token
        $response = wp_remote_post( 'https://oauth2.googleapis.com/token', [
            'timeout'             => 20,
            'redirection'         => 0,
            'limit_response_size' => 1048576,
            'body' => [
                'code'          => $code,
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code'
            ]
        ]);

        $body = $this->decode_successful_response( $response );
        if ( ! is_array( $body ) || ! isset( $body['access_token'] ) ||
            ! is_string( $body['access_token'] ) || '' === trim( $body['access_token'] ) ||
            preg_match( '/[\r\n]/', $body['access_token'] ) ) {
            return false;
        }

        // 2. Obtener datos del usuario
        $user_info = wp_remote_get( 'https://www.googleapis.com/oauth2/v2/userinfo', [
            'timeout'             => 15,
            'redirection'         => 0,
            'limit_response_size' => 1048576,
            'headers' => [ 'Authorization' => 'Bearer ' . $body['access_token'] ]
        ]);

        $profile = $this->decode_successful_response( $user_info );
        if ( ! is_array( $profile ) || ! isset( $profile['id'] ) ||
            ! is_string( $profile['id'] ) || '' === $profile['id'] || strlen( $profile['id'] ) > 255 ||
            ( isset( $profile['email'] ) && ! is_string( $profile['email'] ) ) ||
            ( isset( $profile['verified_email'] ) && ! is_bool( $profile['verified_email'] ) ) ) {
            return false;
        }

        // Preserve the existing v2 userinfo field names and real verification value.
        $result = array( 'id' => $profile['id'], 'verified_email' => true === ( $profile['verified_email'] ?? false ) );
        foreach ( array( 'email', 'name', 'given_name', 'family_name', 'picture', 'hd' ) as $field ) {
            $result[ $field ] = isset( $profile[ $field ] ) && is_string( $profile[ $field ] ) ? $profile[ $field ] : '';
        }
        return $result;
    }

    /** Accept only a successful JSON object, retaining a safe OAuth error category. */
    private function decode_successful_response( $response ) {
        if ( is_wp_error( $response ) ) {
            return false;
        }
        $decoded = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $decoded ) ) {
            return false;
        }
        if ( array_key_exists( 'error', $decoded ) ) {
            $this->last_error = self::classify_oauth_error( $decoded['error'] );
            return false;
        }
        return 200 === (int) wp_remote_retrieve_response_code( $response ) ? $decoded : false;
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
