<?php

/**
 * Gestiona la seguridad pública del plugin (reCAPTCHA).
 *
 * Se encarga de renderizar los scripts de reCAPTCHA (v2 y v3) en los formularios
 * de acceso, registro y recuperación, así como de validar la respuesta con Google.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/public
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin_Public_Security {

    /**
     * El identificador único del plugin.
     *
     * @access private
     * @var    string    $plugin_name  El nombre (slug) del plugin.
     */
    private $plugin_name;

    /**
     * La versión actual del plugin.
     *
     * @access private
     * @var    string    $version      La versión actual del plugin.
     */
    private $version;

    /**
     * Inicializa la clase y asigna las propiedades principales.
     *
     * @param    string    $plugin_name    El nombre del plugin.
     * @param    string    $version        La versión del plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * ================================================================
     * 1. FUNCIONES PRIVADAS (Lógica Interna)
     * ================================================================
     */

    /**
     * Obtiene la configuración activa de reCAPTCHA.
     * Maneja la compatibilidad con versiones anteriores (migración de v2 antigua).
     *
     * @return array|bool Retorna array con keys (version, site, secret) o false si no está configurado.
     */
    private function obtener_configuracion() {
        $version    = get_option( 'recaptcha_version_wporlogin', 'none' );
        $v2_activo  = get_option( 'recaptcha_v2_wporlogin', 0 );

        // Compatibilidad: Si dice 'none' pero el check v2 antiguo está activo, forzamos v2
        if ( $version === 'none' && $v2_activo == 1 ) {
            $version = 'v2';
        }

        if ( $version === 'none' ) {
            return false;
        }

        $config = [
            'version' => $version,
            'site'    => '',
            'secret'  => ''
        ];

        // Asignamos las variables de BD según la versión seleccionada
        if ( $version === 'v2' ) {
            $config['site']   = get_option( 'recaptcha_v2_site_key_wporlogin' );
            $config['secret'] = get_option( 'recaptcha_v2_secret_key_wporlogin' );
        } elseif ( $version === 'v3' ) {
            $config['site']   = get_option( 'recaptcha_v3_site_key_wporlogin' );
            $config['secret'] = get_option( 'recaptcha_v3_secret_key_wporlogin' );
        }

        if ( empty( $config['site'] ) || empty( $config['secret'] ) ) {
            return false;
        }

        return $config;
    }

    /**
     * Genera el HTML/JS dinámicamente según el contexto.
     *
     * @param string $contexto El contexto del formulario ('login', 'register', 'lostpassword').
     */
    private function imprimir_html_recaptcha( $contexto ) {
        // 1. Determinar qué opción de activación revisar según el contexto
        $opcion_activacion = '';
        switch ( $contexto ) {
            case 'login':
                $opcion_activacion = 'activa_acceso_recaptcha_v2_wporlogin';
                break;
            case 'register':
                $opcion_activacion = 'activa_registro_recaptcha_v2_wporlogin';
                break;
            case 'lostpassword':
                $opcion_activacion = 'activa_recuperar_recaptcha_wporlogin';
                break;
        }

        // Si la opción no está activa (valor 1), no imprimimos nada
        if ( get_option( $opcion_activacion ) != 1 ) {
            return;
        }

        // 2. Obtener configuración
        $config = $this->obtener_configuracion();
        if ( ! $config ) {
            return;
        }

        $site_key = esc_js( $config['site'] );
        
        // Creamos IDs únicos combinando el nombre del plugin y el contexto
        $id_unico = 'wporlogin_' . esc_attr( $contexto ); 

        // --- RENDERIZADO V2 (Checkbox) ---
        if ( $config['version'] === 'v2' ) {
            $nombre_callback = 'onloadCallback_' . esc_js( $contexto ); 
            ?>
            <div id="html_element_<?php echo $id_unico; ?>" style="margin-bottom: 10px;"></div>
            <script type="text/javascript">
                var <?php echo $nombre_callback; ?> = function() {
                    if ( typeof grecaptcha !== 'undefined' ) {
                        grecaptcha.render('html_element_<?php echo $id_unico; ?>', { 'sitekey': '<?php echo $site_key; ?>' });
                    }
                };
            </script>
            <script src="https://www.google.com/recaptcha/api.js?onload=<?php echo $nombre_callback; ?>&render=explicit" async defer></script>
            <?php
        } 
        // --- RENDERIZADO V3 (Invisible) ---
        elseif ( $config['version'] === 'v3' ) {
            ?>
            <input type="hidden" name="wporlogin-g-recaptcha-response" id="g-recaptcha-response-<?php echo $id_unico; ?>">
            <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $site_key; ?>"></script>
            <script>
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?php echo $site_key; ?>', {action: '<?php echo esc_js( $contexto ); ?>'}).then(function(token) {
                        var el = document.getElementById('g-recaptcha-response-<?php echo $id_unico; ?>');
                        if (el) el.value = token;
                    });
                });
            </script>
            <?php
        }
    }

    /**
     * Realiza la validación técnica contra la API de Google.
     *
     * @return bool|WP_Error True si es válido, WP_Error si falla.
     */
    private function validar_respuesta_google() {
        $config = $this->obtener_configuracion();
        // Fail-open: si no está configurado, dejamos pasar para no bloquear el sitio por error de config.
        if ( ! $config ) return true; 

        // Buscamos el token en los POST fields posibles
        $token = '';
        if ( isset( $_POST['wporlogin-g-recaptcha-response'] ) ) {
            $token = sanitize_text_field( $_POST['wporlogin-g-recaptcha-response'] );
        } elseif ( isset( $_POST['g-recaptcha-response'] ) ) {
            $token = sanitize_text_field( $_POST['g-recaptcha-response'] );
        }

        if ( empty( $token ) ) {
            return new WP_Error( 'invalid_captcha', __( 'Please complete the reCAPTCHA.', 'wporlogin' ) );
        }

        // Llamada a la API de Google
        $respuesta = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret'   => $config['secret'],
                'response' => $token,
                'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : ''
            ],
            'timeout' => 10
        ]);

        if ( is_wp_error( $respuesta ) ) {
            return new WP_Error( 'recaptcha_error', __( 'Connection error with reCAPTCHA.', 'wporlogin' ) );
        }

        $body = wp_remote_retrieve_body( $respuesta );
        $datos = json_decode( $body, true );

        if ( ! isset( $datos['success'] ) || ! $datos['success'] ) {
            return new WP_Error( 'invalid_captcha', __( 'reCAPTCHA verification failed.', 'wporlogin' ) );
        }

        // Validación extra Score V3 (0.0 a 1.0)
        if ( $config['version'] === 'v3' && isset( $datos['score'] ) ) {
            if ( floatval( $datos['score'] ) < 0.5 ) {
                return new WP_Error( 'low_recaptcha_score', __( 'Security Score too low (Bot detected).', 'wporlogin' ) );
            }
        }

        return true;
    }


    /**
     * ================================================================
     * 2. FUNCIONES PÚBLICAS (Hooks de WordPress)
     * ================================================================
     */

    /**
     * Encola scripts necesarios.
     * Hook: login_enqueue_scripts
     */
    public function enqueue_recaptcha_scripts() {
        $config = $this->obtener_configuracion();
        if ( ! $config ) return;

        // V2 carga explícita inline, pero mantenemos el registro por compatibilidad
        if ( $config['version'] === 'v2' ) {
            wp_register_script( 'recaptcha_v2', 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit', [], $this->version, true );
        } 
        // V3 se puede encolar directamente
        elseif ( $config['version'] === 'v3' ) {
            wp_register_script( 'recaptcha_v3', 'https://www.google.com/recaptcha/api.js?render=' . esc_html( $config['site'] ), [], $this->version, true );
            wp_enqueue_script( 'recaptcha_v3' );
        }
    }

    // --- LOGIN ---

    public function render_login_recaptcha() {
        $this->imprimir_html_recaptcha( 'login' );
    }

    public function verify_login_recaptcha( $user, $password ) {
        // Verificamos si está activa la opción antes de validar
        if ( get_option( 'activa_acceso_recaptcha_v2_wporlogin' ) != 1 ) return $user;
        if ( is_wp_error( $user ) ) return $user;

        $resultado = $this->validar_respuesta_google();
        if ( is_wp_error( $resultado ) ) return $resultado;

        return $user;
    }

    // --- REGISTRO ---

    public function render_register_recaptcha() {
        $this->imprimir_html_recaptcha( 'register' );
    }

    public function verify_register_recaptcha( $errors, $sanitized_user_login, $user_email ) {
        if ( get_option( 'activa_registro_recaptcha_v2_wporlogin' ) != 1 ) return $errors;

        $resultado = $this->validar_respuesta_google();
        if ( is_wp_error( $resultado ) ) {
            $errors->add( $resultado->get_error_code(), $resultado->get_error_message() );
        }

        return $errors;
    }

    // --- RECUPERACIÓN DE CONTRASEÑA ---

    public function render_lostpassword_recaptcha() {
        $this->imprimir_html_recaptcha( 'lostpassword' );
    }

    public function verify_lostpassword_recaptcha( $errors ) {
        if ( get_option( 'activa_recuperar_recaptcha_wporlogin' ) != 1 ) return $errors;

        $resultado = $this->validar_respuesta_google();
        if ( is_wp_error( $resultado ) ) {
            if ( is_wp_error( $errors ) ) {
                $errors->add( $resultado->get_error_code(), $resultado->get_error_message() );
            } else {
                return $resultado;
            }
        }

        return $errors;
    }
}