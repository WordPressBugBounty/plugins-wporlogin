<?php
class Wporlogin_Public_Social {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * SOLO CSS: Se carga en el HEAD.
     * Ocultamos el contenedor (.wporlogin-social-container) inicialmente con display:none
     * para que no parpadee en el lugar incorrecto.
     */
    public function enqueue_social_styles() {
        if ( get_option( 'wporlogin_google_enable' ) !== '1' ) return;
        ?>
        <style type="text/css">
            .wporlogin-social-container {
                padding-top: 10px !important;
                text-align: center;
                flex-direction: column;
                gap: 15px;
                align-items: center;
                width: 100%;
                clear: both;
            }
            .wporlogin-social-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background-color: #ffffff;
                color: #757575;
                border: 1px solid #dadce0;
                border-radius: 4px;
                font-family: 'Roboto', arial, sans-serif;
                font-size: 14px;
                font-weight: 500;
                text-decoration: none;
                width: 100%;
                height: 40px;
                cursor: pointer;
                transition: background-color .218s, border-color .218s;
                position: relative;
            }
            .wporlogin-social-btn:hover {
                background-color: #f7fafe;
                border-color: #d2e3fc;
                color: #3c4043;
            }
            .wporlogin-social-icon-wrapper {
                width: 38px;
                height: 38px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: absolute;
                left: 1px;
                top: 0;
            }
            .wporlogin-social-text {
                padding-left: 20px;
            }
            .wporlogin-separator {
                width: 100%;
                display: flex;
                align-items: center;
                text-align: center;
                color: #8c8f94;
                font-size: 12px;
                text-transform: uppercase;
            }
            .wporlogin-separator::before, .wporlogin-separator::after {
                content: '';
                flex: 1;
                border-bottom: 1px solid #ddd;
            }
            .wporlogin-separator::before { margin-right: 10px; }
            .wporlogin-separator::after { margin-left: 10px; }
        </style>
        <?php
    }

    /**
     * SOLO JS: Se carga en el FOOTER.
     * VERSIÓN FINAL: Soporta Login (#loginform) y Registro (#registerform).
     */
    public function output_social_footer_script() {
        if ( get_option( 'wporlogin_google_enable' ) !== '1' ) return;
        ?>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function(event) {
                // Seleccionamos el bloque social
                var socialBlock = document.querySelector('.wporlogin-social-container');
                
                // Seleccionamos AMBOS formularios posibles
                var loginForm    = document.getElementById('loginform');
                var registerForm = document.getElementById('registerform');

                // Determinamos cuál usar (el que exista en la página actual)
                var targetForm = loginForm || registerForm;

                if (socialBlock && targetForm) {
                    // Mover al final del formulario detectado
                    targetForm.appendChild(socialBlock);
                    
                    // Hacer visible
                    socialBlock.style.display = 'flex';
                } else {
                    // Fallback (por si el tema usa IDs raros)
                    if (socialBlock) {
                        socialBlock.style.display = 'flex';
                    }
                }
            });
        </script>
        <?php
    }

    /**
     * Muestra los botones. 
     * MEJORA: Oculta el botón en la página de Registro si el registro global está desactivado.
     */
    public function render_social_buttons() {
        if ( get_option( 'wporlogin_google_enable' ) !== '1' ) return;

        // Verificar si el registro está permitido globalmente en WP
        $registration_enabled = get_option( 'users_can_register' );
        $action = isset( $_GET['action'] ) ? $_GET['action'] : 'login';

        // SI estamos en la pantalla de registro Y el registro está cerrado -> NO mostrar nada.
        if ( $action === 'register' && ! $registration_enabled ) {
            return;
        }

        require_once plugin_dir_path( __FILE__ ) . 'social/class-wporlogin-social-google.php';
        
        $client_id = get_option( 'wporlogin_google_client_id' );
        $secret    = get_option( 'wporlogin_google_client_secret' );

        if ( $client_id && $secret ) {
            $google = new Wporlogin_Social_Google( $client_id, $secret );
            
            // Texto dinámico
            if ( $action === 'register' ) {
                $btn_text = __( 'Register with Google', 'wporlogin' );
            } else {
                $btn_text = __( 'Sign in with Google', 'wporlogin' );
            }

            echo '<div class="wporlogin-social-container">';
            echo '<div class="wporlogin-separator">' . __( 'OR', 'wporlogin' ) . '</div>';
            echo $google->render_button( $btn_text );
            echo '</div>';
        }
    }

    /**
     * Escucha cuando Google nos devuelve al usuario.
     * Hook: init
     */
    public function handle_social_callback() {
        if ( isset( $_GET['wporlogin_social_auth'] ) && $_GET['wporlogin_social_auth'] == 'google' ) {
            
            // Si Google devuelve un error (ej: usuario canceló) o no hay código
            if ( isset( $_GET['error'] ) || ! isset( $_GET['code'] ) ) {
                $this->redirect_with_error( 'google_cancel' );
                return;
            }

            require_once plugin_dir_path( __FILE__ ) . 'social/class-wporlogin-social-google.php';
            
            $client_id = get_option( 'wporlogin_google_client_id' );
            $secret    = get_option( 'wporlogin_google_client_secret' );
            
            // Validación básica de configuración
            if ( ! $client_id || ! $secret ) {
                $this->redirect_with_error( 'google_config' );
                return;
            }

            $provider = new Wporlogin_Social_Google( $client_id, $secret );
            
            // Intentamos obtener datos. Si falla (claves mal, error de red), devuelve false.
            $user_data = $provider->verify_token( sanitize_text_field( $_GET['code'] ) );

            if ( $user_data && isset( $user_data['email'] ) ) {
                $this->login_or_register_user( $user_data );
            } else {
                // AQUÍ ESTABA EL wp_die(). Lo cambiamos por redirección elegante.
                $this->redirect_with_error( 'google_connect_fail' );
            }
        }
    }

    /**
     * Helper privado para redirigir con error (DRY - Don't Repeat Yourself)
     */
    private function redirect_with_error( $error_code ) {
        $login_url = wp_login_url();
        $redirect_url = add_query_arg( 'wporlogin_error', $error_code, $login_url );
        wp_safe_redirect( $redirect_url );
        exit;
    }

    /**
     * Lógica universal para registrar/loguear en WP.
     * MEJORA: Bloquea la creación de usuarios si 'users_can_register' es falso.
     */
    private function login_or_register_user( $social_user ) {
        $email = $social_user['email'];
        
        // 1. Buscar si existe el usuario
        $user = get_user_by( 'email', $email );

        // 2. Si NO existe, intentamos crearlo
        if ( ! $user ) {
            
            // --- BLOQUEO DE SEGURIDAD (MEJORADO UX) ---
            // Si el registro está desactivado, NO matamos la página.
            // Redirigimos al login con una variable 'wporlogin_error'
            if ( ! get_option( 'users_can_register' ) ) {
                
                $login_url = wp_login_url();
                
                // Añadimos un parámetro a la URL: ?wporlogin_error=account_not_found
                $redirect_url = add_query_arg( 'wporlogin_error', 'account_not_found', $login_url );
                
                wp_safe_redirect( $redirect_url );
                exit; // Salimos limpiamente
            }
            // ------------------------------------------

            $username = sanitize_user( current( explode( '@', $email ) ) );
            if( username_exists($username) ) { $username .= rand(100, 999); }
            $password = wp_generate_password();
            
            $user_id = wp_create_user( $username, $password, $email );
            
            if ( is_wp_error( $user_id ) ) wp_die( $user_id->get_error_message() );

            // --- CORRECCIÓN: Actualizar Nombre y Apellido ---
            // Google suele enviar 'given_name' y 'family_name'
            $user_data_update = array(
                'ID' => $user_id,
                'first_name' => isset($social_user['given_name']) ? sanitize_text_field($social_user['given_name']) : '',
                'last_name'  => isset($social_user['family_name']) ? sanitize_text_field($social_user['family_name']) : '',
                // Opcional: Mostrar el nombre públicamente como "Nombre Apellido"
                'display_name' => isset($social_user['name']) ? sanitize_text_field($social_user['name']) : '' 
            );
            
            wp_update_user( $user_data_update );
            // ------------------------------------------------
            
            $user = get_user_by( 'id', $user_id );
        }

        // --- [NUEVO] SINCRONIZACIÓN DE AVATAR ---
        // Guardamos la URL de la foto en usermeta cada vez que hace login.
        // Esto asegura que si cambia su foto en Google, se actualice aquí eventualmente.
        if ( isset( $social_user['picture'] ) && ! empty( $social_user['picture'] ) ) {
            update_user_meta( $user->ID, 'wporlogin_social_avatar', esc_url_raw( $social_user['picture'] ) );
        }
        // -----------------------------------------

        // 3. Iniciar sesión (para usuarios existentes o recién creados si estaba permitido)
        wp_set_current_user( $user->ID );
        wp_set_auth_cookie( $user->ID );
        
        // 4. Redirección (Delegando a la clase Redirects como acordamos)
        $redirect_url = home_url(); 
        if ( class_exists( 'Wporlogin_Public_Redirects' ) ) {
            //require_once plugin_dir_path( __FILE__ ) . 'class-wporlogin-public-redirects.php';
            $redirect_url = Wporlogin_Public_Redirects::get_calculated_redirect_url( $user );
        }

        wp_safe_redirect( $redirect_url );
        exit;
    }

    /**
     * [NUEVO MÉTODO] Reemplaza el Avatar por defecto de WP/Gravatar
     * por la foto de Google si existe en los metadatos.
     */
    public function replace_gravatar_with_social_image( $avatar, $id_or_email, $size, $default, $alt ) {
        
        // 1. [NUEVO] Verificación Global
        // Si el admin NO activó la opción, retornamos el avatar normal y salimos.
        if ( get_option( 'wporlogin_social_avatar_sync' ) !== '1' ) {
            return $avatar;
        }
        
        $user_id = false;

        // Normalizamos para obtener el ID del usuario
        if ( is_numeric( $id_or_email ) ) {
            $user_id = (int) $id_or_email;
        } elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
            $user_id = (int) $id_or_email->user_id;
        } elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
            $user = get_user_by( 'email', $id_or_email );
            if ( $user ) $user_id = $user->ID;
        }

        if ( ! $user_id ) {
            return $avatar;
        }

        // Buscamos si tiene avatar social guardado
        $social_avatar = get_user_meta( $user_id, 'wporlogin_social_avatar', true );

        if ( $social_avatar ) {
            // [CAMBIO CLAVE] Añadimos referrerpolicy='no-referrer'
            $avatar = "<img alt='" . esc_attr( $alt ) . "' src='" . esc_url( $social_avatar ) . "' referrerpolicy='no-referrer' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' loading='lazy' />";
        }

        return $avatar;
    }

    /**
     * Muestra mensajes de error personalizados.
     * Hook: wp_login_errors
     */
    public function custom_login_error_message( $errors ) {
        
        if ( ! isset( $_GET['wporlogin_error'] ) ) return $errors;

        $error_code = sanitize_text_field( $_GET['wporlogin_error'] );
        $message    = '';

        switch ( $error_code ) {
            case 'account_not_found':
                $message = __( '<strong>Error</strong>: No account found with this Google email. Please log in with your username and password.', 'wporlogin' );
                break;
            
            case 'google_cancel':
                $message = __( '<strong>Notice</strong>: Google login cancelled or denied.', 'wporlogin' );
                break;

            case 'google_config':
                $message = __( '<strong>System Error</strong>: Google Login is not configured correctly. Please contact the site administrator.', 'wporlogin' );
                break;

            case 'google_connect_fail':
                $message = __( '<strong>Connection Error</strong>: Could not verify your Google account. Please try again or use your password.', 'wporlogin' );
                break;
        }

        if ( ! empty( $message ) ) {
            $errors->add( 'wporlogin_social_error', $message );
        }
        
        return $errors;
    }
}