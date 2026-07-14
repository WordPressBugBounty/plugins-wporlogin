<?php

/**
 * Maneja la intercepción de URLs y la reescritura de enlaces.
 */
class Wporlogin_Public_Hide_Login {

    private $plugin_name;
    private $version;
    private $login_slug;
    private $register_slug;
    private $recovery_slug;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        $this->login_slug    = get_option('wporlogin_hide_login_slug');
        $this->register_slug = get_option('wporlogin_hide_register_slug');
        $this->recovery_slug = get_option('wporlogin_hide_recovery_slug');
    }

    public function init() {
        if ( empty($this->login_slug) && empty($this->register_slug) && empty($this->recovery_slug) ) {
            return;
        }

        // 1. INTERCEPTOR (Carga la página correcta)
        add_action( 'wp_loaded', array( $this, 'intercept_login_page' ) );

        // 2. FILTROS GENÉRICOS (Limpia URLs en redirecciones y correos)
        add_filter( 'site_url', array( $this, 'filter_site_url' ), 10, 4 );
        add_filter( 'network_site_url', array( $this, 'filter_site_url' ), 10, 3 );
        add_filter( 'wp_redirect', array( $this, 'filter_wp_redirect' ), 10, 2 );

        // 3. FILTRO VISUAL (El que soluciona tu problema del enlace "¿Olvidaste contraseña?")
        add_filter( 'lostpassword_url', array( $this, 'filter_lostpassword_url' ), 10, 2 );

        // 4. SEGURIDAD (Bloqueo)
        add_action( 'init', array( $this, 'block_wp_login' ) );

        // 5. [NUEVO] SEGURIDAD BACKEND (Bloqueo de /wp-admin/)
        // Esto evita que /wp-admin redirija al login personalizado, revelándolo.
        add_action( 'init', array( $this, 'block_dashboard_access' ) );
    }

    /**
     * Redirige a una página personalizada o 404 si entran a wp-admin.
     * Optimizado con estándares de "Major League Plugins".
     */
    public function block_dashboard_access() {
        
        // 1. Solo nos interesa el área de administración
        if ( ! is_admin() ) {
            return;
        }

        // 2. Si el usuario está logueado, permitir acceso inmediato (Rendimiento)
        if ( is_user_logged_in() ) {
            return;
        }

        // 3. EXCEPCIÓN VITAL: Permitir AJAX y admin-post.php
        // [MEJORA EXPERTA]: Agregada validación robusta para admin-post.php
        if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( isset( $_GET['action'] ) && $_GET['action'] == 'post-data' ) ) {
            return;
        }
        
        $script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        // [MEJORA EXPERTA]: Detectamos admin-post.php explícitamente
        if ( strpos( $script_name, 'admin-ajax.php' ) !== false || strpos( $script_name, 'admin-post.php' ) !== false ) {
            return;
        }

        // 4. Si NO hay login personalizado, no bloquear nada.
        if ( empty( $this->login_slug ) ) {
            return;
        }

        // 5. [MEJORA EXPERTA]: Hook para desarrolladores (Extensibilidad)
        // Permite que otros plugins soliciten acceso enviando 'true' a este filtro.
        if ( apply_filters( 'wporlogin_bypass_admin_block', false ) ) {
            return;
        }

        // 6. PREPARAR REDIRECCIÓN
        $redirect_slug = get_option('wporlogin_hide_wp_admin_redirect_slug');

        if ( empty( $redirect_slug ) ) {
            $redirect_slug = '404/'; // Por defecto busca la página /404 del tema
        }

        // 7. [MEJORA EXPERTA]: EVITAR CACHÉ (Crucial para WP Rocket/LiteSpeed)
        // Le decimos a WordPress y a los plugins de caché que NO guarden esta redirección.
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }
        nocache_headers();

        // 8. EJECUTAR REDIRECCIÓN
        wp_safe_redirect( home_url( $redirect_slug . '/' ) ); 
        exit(); 
    }

    /**
     * Lógica Híbrida para "¿Has olvidado tu contraseña?"
     * Garantiza que siempre devuelva una URL válida.
     */
    public function filter_lostpassword_url( $url, $redirect = '' ) {
        
        // 1. Prioridad: ¿El usuario configuró un slug específico (ej: 'recuperar')?
        if ( !empty($this->recovery_slug) ) {
            $new_url = home_url( '/' . $this->recovery_slug );
            
            // Si había una redirección pendiente, la mantenemos
            if ( !empty($redirect) ) {
                $new_url = add_query_arg( 'redirect_to', urlencode($redirect), $new_url );
            }
            return $new_url;
        }

        // 2. Respaldo Híbrido: Si está vacío, usamos el slug de Login + parametro.
        // Construimos la URL desde cero para evitar errores de reemplazo.
        if ( !empty($this->login_slug) ) {
            // Resultado: tudominio.com/acceso
            $new_url = home_url( '/' . $this->login_slug );
            // Agregamos el parámetro: tudominio.com/acceso?action=lostpassword
            $new_url = add_query_arg( 'action', 'lostpassword', $new_url );

            if ( !empty($redirect) ) {
                $new_url = add_query_arg( 'redirect_to', urlencode($redirect), $new_url );
            }
            return $new_url;
        }

        // 3. Si todo falla, devolvemos la original
        return $url;
    }

    /**
     * Interceptor: Carga wp-login.php con la acción correcta.
     */
    /**
     * Interceptor: Carga wp-login.php con la acción correcta.
     * [CORRECCIÓN MAESTRA]: Detecta si debe limpiar o mantener parámetros
     * basándose en si existe un slug personalizado o no.
     */
    public function intercept_login_page() {
        $request_uri = $_SERVER['REQUEST_URI'];
        $path = parse_url($request_uri, PHP_URL_PATH);
        $base_path = parse_url(home_url(), PHP_URL_PATH);
        
        if ( $base_path && $base_path !== '/' ) {
            $path = str_replace($base_path, '', $path);
        }
        $path = trim($path, '/');

        // 1. Detección de DÓNDE estamos físicamente (por URL)
        $is_on_login_slug    = ( !empty($this->login_slug) && $path === $this->login_slug );
        $is_on_register_slug = ( !empty($this->register_slug) && $path === $this->register_slug );
        $is_on_recovery_slug = ( !empty($this->recovery_slug) && $path === $this->recovery_slug );

        // 2. Detección de QUÉ estamos haciendo lógicamente (por parámetro action)
        $current_action = isset($_GET['action']) ? $_GET['action'] : '';

        if ( $is_on_login_slug || $is_on_register_slug || $is_on_recovery_slug ) {
            global $error, $interim_login, $action, $user_login, $pagenow;

            // --- [BLOQUE DE REDIRECCIÓN POR IDIOMA] ---
            if ( isset( $_GET['wp_lang'] ) && ! empty( $_GET['wp_lang'] ) ) {
                $lang = sanitize_text_field( $_GET['wp_lang'] );
                $languages = get_available_languages();
                
                if ( in_array( $lang, $languages ) || $lang === 'en_US' ) {
                    // A. Guardar Cookie
                    setcookie( 'wp_lang', $lang, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
                    
                    // B. Definir LISTA BLANCA BASE (Parámetros que siempre pasan)
                    $allowed_params = array( 'key', 'login', 'user_login', 'checkemail', 'error', 'updated', 'redirect_to', 'reauth' );
                    
                    // C. Lógica Inteligente para la URL Base y el parámetro 'action'
                    $redirect_base = home_url( '/' . $path ); // Por defecto, nos quedamos donde estamos

                    // CASO 1: Estamos en Registro
                    if ( $is_on_register_slug ) {
                        // Si estamos en un slug dedicado (/registro), NO necesitamos ?action=register
                        // $redirect_base ya es /registro
                    } elseif ( $current_action === 'register' && $is_on_login_slug ) {
                        // Si estamos en login (/acceso) pero queriendo registrar, Y no hay slug de registro...
                        // DEBEMOS MANTENER EL PARÁMETRO ACTION.
                        $allowed_params[] = 'action';
                    }

                    // CASO 2: Estamos en Recuperación
                    if ( $is_on_recovery_slug ) {
                        // Slug dedicado (/recuperar), URL limpia.
                    } elseif ( in_array($current_action, array('lostpassword', 'rp', 'resetpass')) && $is_on_login_slug ) {
                        // Estamos en login (/acceso) pero recuperando pass.
                        // DEBEMOS MANTENER EL PARÁMETRO ACTION.
                        $allowed_params[] = 'action';
                    }

                    // D. Construcción de URL
                    $final_params = array();
                    foreach ( $allowed_params as $param ) {
                        if ( isset( $_GET[ $param ] ) ) {
                            $final_params[ $param ] = $_GET[ $param ];
                        }
                    }

                    $redirect_to = add_query_arg( $final_params, $redirect_base );
                    wp_safe_redirect( $redirect_to );
                    exit;
                }
            }
            // ------------------------------------------

            // --- [APLICAR IDIOMA SI EXISTE COOKIE] ---
            if ( isset( $_COOKIE['wp_lang'] ) && ! empty( $_COOKIE['wp_lang'] ) ) {
                $cookie_lang = sanitize_text_field( $_COOKIE['wp_lang'] );
                $installed_langs = get_available_languages();
                if ( in_array( $cookie_lang, $installed_langs ) || $cookie_lang === 'en_US' ) {
                    switch_to_locale( $cookie_lang );
                }
            }

            // --- Configuración de Acciones Internas (Lógica original intacta) ---
            // Aquí determinamos qué mostrar al motor de WP
            $is_register_action = ( $is_on_register_slug || ( $is_on_login_slug && $current_action === 'register' ) );
            $is_recovery_action = ( $is_on_recovery_slug || ( $is_on_login_slug && in_array($current_action, array('lostpassword', 'rp', 'resetpass')) ) );

            if ( $is_register_action ) {
                $action = 'register';
                $_GET['action'] = 'register';
                if ( ! isset( $_GET['key'] ) ) $_REQUEST['action'] = 'register';
            } elseif ( $is_recovery_action ) {
                // Protección para resetpass (rp) que viene con key
                if ( in_array($current_action, array('rp', 'resetpass')) ) {
                    $action = $current_action;
                    $_GET['action'] = $current_action;
                    $_REQUEST['action'] = $current_action;
                } else {
                    $action = 'lostpassword';
                    $_GET['action'] = 'lostpassword';
                    $_REQUEST['action'] = 'lostpassword';
                }
            } else {
                if ( isset($_GET['action']) ) $action = $_GET['action'];
            }

            // --- Simulación de entorno ---
            $pagenow = 'wp-login.php';
            $_SERVER['PHP_SELF'] = '/wp-login.php'; 
            $_SERVER['SCRIPT_NAME'] = '/wp-login.php';
            $_SERVER['SCRIPT_FILENAME'] = ABSPATH . 'wp-login.php';

            require_once ABSPATH . 'wp-login.php';
            exit();
        }
    }

    /**
     * Filtro Site URL (Mantiene la lógica híbrida para el email)
     */
    public function replace_login_url( $url, $scheme = null ) {
        if ( strpos( $url, 'wp-login.php' ) !== false ) {
            
            // Registro
            if ( strpos( $url, 'action=register' ) !== false || strpos( $url, 'registration=disabled' ) !== false ) {
                if ( !empty($this->register_slug) ) return home_url( '/' . $this->register_slug );
                if ( !empty($this->login_slug) ) return str_replace( 'wp-login.php', $this->login_slug, $url );
            }

            // Recuperar
            if ( strpos( $url, 'action=lostpassword' ) !== false ) {
                if ( !empty($this->recovery_slug) ) return home_url( '/' . $this->recovery_slug );
                if ( !empty($this->login_slug) ) return str_replace( 'wp-login.php', $this->login_slug, $url );
            }

            // Email Link (Reset Password) -> Forzamos al Login Slug
            if ( strpos( $url, 'action=rp' ) !== false || strpos( $url, 'action=resetpass' ) !== false ) {
                if ( !empty($this->login_slug) ) return str_replace( 'wp-login.php', $this->login_slug, $url );
                return $url; 
            }

            // Login General
            if ( !empty($this->login_slug) ) {
                return str_replace( 'wp-login.php', $this->login_slug, $url );
            }
        }
        return $url;
    }

    // Wrappers
    public function filter_site_url( $url, $path, $scheme, $blog_id = null ) {
        return $this->replace_login_url( $url, $scheme );
    }
    public function filter_wp_redirect( $location, $status ) {
        return $this->replace_login_url( $location );
    }

    /**
     * Bloquea el acceso al archivo wp-login.php original.
     * MEJORA: Usa la misma lógica de redirección que wp-admin para consistencia.
     */
    public function block_wp_login() {
        
        // 1. CONDICIÓN PRINCIPAL: Solo actuar si hay un slug de login personalizado.
        if ( empty($this->login_slug) ) {
            return;
        }

        $request_uri = $_SERVER['REQUEST_URI'];
        
        // 2. Detectar si están intentando entrar a 'wp-login.php'
        if ( strpos( $request_uri, 'wp-login.php' ) !== false ) {

            // Excepciones permitidas (para que funcione el sitio)
            if ( strpos( $request_uri, $this->login_slug ) !== false ) return; // Permitir el nuevo slug
            if ( isset( $_GET['action'] ) && ( $_GET['action'] === 'logout' || $_GET['action'] === 'postpass' ) ) return;
            if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) return;
            if ( isset( $_GET['action'] ) && ( $_GET['action'] === 'rp' || $_GET['action'] === 'resetpass' ) ) return;

            // 3. OBTENER URL DE DESTINO (Consistencia con wp-admin)
            // Usamos la misma opción que creamos para wp-admin.
            $redirect_slug = get_option('wporlogin_hide_wp_admin_redirect_slug');

            if ( empty( $redirect_slug ) ) {
                $redirect_slug = '404/'; // Por defecto a la página 404 del tema
            }

            // 4. EVITAR CACHÉ (Nivel Experto)
            if ( ! defined( 'DONOTCACHEPAGE' ) ) {
                define( 'DONOTCACHEPAGE', true );
            }
            nocache_headers();

            // 5. REDIRIGIR
            wp_safe_redirect( home_url( $redirect_slug . '/' ) );
            exit();
        }
    }
}