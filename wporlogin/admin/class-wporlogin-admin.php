<?php

/**
 * La clase responsable de definir todas las acciones y lógica del área de administración.
 *
 * Se encarga de la gestión de menús, la carga de recursos (scripts/styles) y la 
 * validación/registro de opciones (Settings API) del plugin.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/admin
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin_Admin {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    // -----------------------------------------------------------------
    //  GESTIÓN DE MENÚS Y SUBMENÚS
    // -----------------------------------------------------------------

    public function add_plugin_admin_menu() {

        $page_title = __( 'Plugin WPOrLogin', 'wporlogin' );
        $menu_title = __( 'WPOrLogin', 'wporlogin' );
        $capability = 'manage_options';
        $menu_slug  = 'wporlogin-plugin';
        $icon_url   = 'dashicons-unlock';

        // Menú principal
        add_menu_page( 
            $page_title, 
            $menu_title, 
            $capability, 
            $menu_slug, 
            array( $this, 'wporlogin_content_page_menu' ),
            $icon_url 
        );

        // Submenú Google reCAPTCHA
        add_submenu_page( 
            $menu_slug, 
            __( 'Google reCAPTCHA', 'wporlogin' ), 
            __( 'Google reCAPTCHA', 'wporlogin' ), 
            'manage_options', 
            'recaptcha-wporlogin-plugin', 
            array( $this, 'recaptcha_wporlogin_content_page_menu' )
        );

        // Submenú Remove Language
        add_submenu_page( 
            $menu_slug, 
            __( 'Remove Language', 'wporlogin' ), 
            __( 'Remove Language', 'wporlogin' ), 
            'manage_options', 
            'remove-language-plugin', 
            array( $this, 'remove_language_content_page_menu' )
        );

        // Submenú Redirects
        add_submenu_page( 
            $menu_slug, 
            __( 'Redirect', 'wporlogin' ), 
            __( 'Redirect', 'wporlogin' ), 
            'manage_options', 
            'redirects-wporlogin-plugin', 
            array( $this, 'redirects_wporlogin_content_page_menu' )
        );

        add_submenu_page( 
            'wporlogin-plugin', 
            __( 'Limit Login Attempts', 'wporlogin' ), 
            __( 'Limit Login', 'wporlogin' ),
            'manage_options', 
            'limit-login-wporlogin-plugin', 
            array( $this, 'limit_login_content_page_menu' )
        );
 
        // [NUEVO] Submenú Hide Login con etiqueta "NEW"
        $titulo   = __( 'Hide Login', 'wporlogin' );
        $etiqueta = __( 'NEW', 'wporlogin' );
        
        // Submenú Hide Login
        add_submenu_page( 
            'wporlogin-plugin', 
            __( 'Hide Login', 'wporlogin' ), 
            __( 'Hide Login', 'wporlogin' ),
            'manage_options', 
            'hide-login-wporlogin-plugin', 
            array( $this, 'hide_login_content_page_menu' )
        );

        // -----------------------------------------------------------
        // AGREGAMOS EL "NEW" ROJO AL SUBMENÚ "SOCIAL LOGIN"
        // -----------------------------------------------------------
        
        $social_title = __( 'Social Login', 'wporlogin' );
        $new_label    = __( 'NEW', 'wporlogin' );
        
        // Creamos el HTML para la etiqueta roja
        // Usamos #d63638 que es el rojo estándar de alertas de WordPress
        $menu_title_social_html = $social_title . ' <span style="background-color: #d63638; color: white; padding: 2px 5px; border-radius: 3px; font-size: 10px; font-weight: 600; vertical-align: middle; margin-left: 5px;">' . $new_label . '</span>';

        add_submenu_page( 
            'wporlogin-plugin', 
            __( 'Social Login', 'wporlogin' ), // Título de la pestaña del navegador (sin HTML)
            $menu_title_social_html,           // Título del menú (con HTML rojo)
            'manage_options', 
            'social-login-wporlogin-plugin', 
            array( $this, 'social_login_content_page_menu' )
        );

        // [NUEVO] Submenú Automatización (Zapier)
        /*$auto_title = __( 'Automation', 'wporlogin' );
        $new_label  = __( 'NEW', 'wporlogin' );
        $menu_title_auto_html = $auto_title . ' <span style="background-color: #2271b1; color: white; padding: 2px 5px; border-radius: 3px; font-size: 10px; font-weight: 600; vertical-align: middle; margin-left: 5px;">' . $new_label . '</span>';

        add_submenu_page( 
            'wporlogin-plugin', 
            __( 'Automation', 'wporlogin' ), 
            $menu_title_auto_html, 
            'manage_options', 
            'automation-wporlogin-plugin', 
            array( $this, 'automation_content_page_menu' )
        );*/

        // Hook para addons PRO
        if ( has_action( 'wporlogin_pro_menu_licencia' ) ) {
            do_action( 'wporlogin_pro_menu_licencia' );
        }
    }

    // -----------------------------------------------------------------
    //  CLASE DE UTILIDAD ESTATICA DE COLORES
    // -----------------------------------------------------------------

    public static function darken_color($hex, $percent) {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $factor = (100 - $percent) / 100;
        $r = max(0, min(255, round($r * $factor)));
        $g = max(0, min(255, round($g * $factor)));
        $b = max(0, min(255, round($b * $factor)));
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    public static function get_contrasting_text_color($hexColor) {
        $hexColor = ltrim($hexColor, '#');
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        $luminance = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
        return ($luminance > 128) ? '#000000' : '#ffffff';
    }

    // -----------------------------------------------------------------
    //  REGISTRO DE AJUSTES (SETTINGS API)
    // -----------------------------------------------------------------

    public function register_settings() {

        // 0. MIGRACIÓN
        $recaptcha_v2_enabled = get_option('recaptcha_v2_wporlogin', 0); 
        $recaptcha_version    = get_option('recaptcha_version_wporlogin', 'none');

        if ($recaptcha_version === 'none' && $recaptcha_v2_enabled == 1) {
            update_option('recaptcha_version_wporlogin', 'v2');
        }

        // 1. INICIALIZACIÓN
        if (isset($_POST['submit'])) { 
            delete_option('wporlogin_design_img_premium_backup'); 
        }

        add_option('wporlogin_date_5_review', date("Y-m-d H:i:s"));
        add_option('wporlogin_design', 'wporlogin_design_basic');
        add_option('wporlogin_width_logotipo_text', '200');
        add_option('wporlogin_background_position_logotipo_select', '7');
        add_option('wporlogin_background_size_logotipo_select', '2');
        add_option('wporlogin_background_images', 'wporlogin_free_images');
        add_option( 'activa_acceso_recaptcha_v2_wporlogin', '1' );
        add_option( 'activa_registro_recaptcha_v2_wporlogin', '1' );
        
        $default_backgrounds = Wporlogin::get_default_backgrounds();
        add_option('wporlogin_url_img_fondo', esc_url($default_backgrounds[0]));
        add_option('wporlogin_color_principal_marca', '#1a73e8');
        add_option('wporlogin-design-img-premium', 'wporlogin_design_img_premium_one');

        // 2. CÁLCULO DE COLORES
        $color_principal = get_option('wporlogin_color_principal_marca', '#1a73e8');
        $color_hover = self::darken_color($color_principal, 25);
        update_option('wporlogin_color_hover_marca', $color_hover);
        $color_text_submit = self::get_contrasting_text_color($color_principal);
        update_option('wporlogin_color_principal_marca_text_submit', $color_text_submit);

        // 3. REGISTRO DE GRUPOS

        // --- Grupo: Apariencia ---
        $main_group = 'wporlogin_custom_admin_settings_group';
        register_setting( $main_group, 'wporlogin_color_principal_marca');
        register_setting( $main_group, 'wporlogin_design');
        register_setting( $main_group, 'wporlogin-design-img-premium');
        register_setting( $main_group, 'wporlogin_url_logotipo');
        register_setting( $main_group, 'wporlogin_ruta_url_logotipo');
        register_setting( $main_group, 'wporlogin_titulo_logotipo');
        register_setting( $main_group, 'wporlogin_url_img_fondo');
        register_setting( $main_group, 'wporlogin_background_images');
        register_setting( $main_group, 'wporlogin-background-free-image');

        register_setting( $main_group, 'wporlogin_active_design');        

        // --- Grupo: reCAPTCHA ---
        $recaptcha_group = 'recaptcha_wporlogin_custom_admin_settings_group';
        register_setting( $recaptcha_group, 'recaptcha_v2_wporlogin');
        register_setting( $recaptcha_group, 'recaptcha_version_wporlogin');
        register_setting( $recaptcha_group, 'recaptcha_v2_site_key_wporlogin');
        register_setting( $recaptcha_group, 'recaptcha_v2_secret_key_wporlogin');
        register_setting( $recaptcha_group, 'recaptcha_v3_site_key_wporlogin');
        register_setting( $recaptcha_group, 'recaptcha_v3_secret_key_wporlogin');
        register_setting( $recaptcha_group, 'activa_acceso_recaptcha_v2_wporlogin');
        register_setting( $recaptcha_group, 'activa_registro_recaptcha_v2_wporlogin');
        register_setting( $recaptcha_group, 'activa_recuperar_recaptcha_wporlogin');

        // --- Grupo: Remove Language ---
        /*$lang_group = 'remove_language_wporlogin_custom_admin_settings_group';
        register_setting( $lang_group, 'remove_language_wporlogin');*/

        $lang_group = 'remove_language_wporlogin_custom_admin_settings_group';

        // 1. Registramos la opción con instrucciones específicas
        register_setting( 
            $lang_group, 
            'remove_language_wporlogin', 
            array(
                'type'              => 'integer',
                'sanitize_callback' => 'wporlogin_sanitize_checkbox_value' // Llamamos a la función de abajo
            )
        );

        /**
         * 2. Función auxiliar para convertir el valor del checkbox.
         * Si el checkbox viene marcado, devuelve 1.
         * Si el checkbox viene vacío (desmarcado), devuelve 0.
         */
        function wporlogin_sanitize_checkbox_value( $value ) {
            return ( isset( $value ) && $value == 1 ) ? 1 : 0;
        }
        
        // --- Grupo: Redirects ---
        $redirect_group = 'redirects_wporlogin_custom_admin_settings_group';
        register_setting( $redirect_group, 'wporlogin_login_redirect', array( 'sanitize_callback' => 'esc_url_raw' ) );
        register_setting( $redirect_group, 'wporlogin_logout_redirect', array( 'sanitize_callback' => 'esc_url_raw' ) );
        register_setting( $redirect_group, 'wporlogin_enable_admin_redirect', array( 'sanitize_callback' => 'absint' ));
        register_setting( $redirect_group, 'wporlogin_enable_login_redirect', array( 'sanitize_callback' => 'absint' ));
        register_setting( $redirect_group, 'wporlogin_enable_logout_redirect', array( 'sanitize_callback' => 'absint' ) );
        // [NUEVO] Opción Array para guardar reglas por rol
        register_setting( $redirect_group, 'wporlogin_redirect_roles_rules', array(
            'sanitize_callback' => array( $this, 'sanitize_redirect_rules' )
        ));

        // --- Grupo: Limit Login [NUEVO] ---
        $limit_group = 'wporlogin_limit_settings_group';
        register_setting( $limit_group, 'wporlogin_limit_enable' );
        register_setting( $limit_group, 'wporlogin_limit_max_retries', array('sanitize_callback' => 'absint') );
        register_setting( $limit_group, 'wporlogin_limit_lock_time', array('sanitize_callback' => 'absint') );
        register_setting( $limit_group, 'wporlogin_limit_message', array('sanitize_callback' => 'sanitize_textarea_field') );
        // [NUEVO] Campo Whitelist (Lista Blanca)
        register_setting( $limit_group, 'wporlogin_limit_whitelist', array(
            'sanitize_callback' => array( $this, 'sanitize_ip_whitelist' ) 
        ));
        
        // ---------------------------------------------------------
        // --- Grupo: Hide Login [NUEVO] ---
        // ---------------------------------------------------------
        $hide_login_group = 'wporlogin_hide_login_settings_group';

        // 1. URL de Acceso
        register_setting( $hide_login_group, 'wporlogin_hide_login_slug', array(
            'sanitize_callback' => 'sanitize_title' // Usamos sanitize_title para asegurar que sea un slug válido (sin espacios ni ñ)
        ));

        // 2. URL de Registro
        register_setting( $hide_login_group, 'wporlogin_hide_register_slug', array(
            'sanitize_callback' => 'sanitize_title'
        ));

        // 3. URL de Recuperar Contraseña
        register_setting( $hide_login_group, 'wporlogin_hide_recovery_slug', array(
            'sanitize_callback' => 'sanitize_title'
        ));

        // IMPORTANTE: Detectar si se acaban de guardar estas opciones para regenerar los enlaces permanentes (Flush Rules)
        // Esto evita el error 404 justo después de guardar.
        if ( isset( $_POST['option_page'] ) && $_POST['option_page'] === $hide_login_group ) {
            // Guardamos las opciones manualmente antes de hacer flush para que tome los nuevos valores
            if ( isset($_POST['wporlogin_hide_login_slug']) ) update_option('wporlogin_hide_login_slug', sanitize_title($_POST['wporlogin_hide_login_slug']));
            if ( isset($_POST['wporlogin_hide_register_slug']) ) update_option('wporlogin_hide_register_slug', sanitize_title($_POST['wporlogin_hide_register_slug']));
            if ( isset($_POST['wporlogin_hide_recovery_slug']) ) update_option('wporlogin_hide_recovery_slug', sanitize_title($_POST['wporlogin_hide_recovery_slug']));
            
            flush_rewrite_rules();
        }
        // [NUEVO] Opción para redirección de wp-admin
        register_setting( $hide_login_group, 'wporlogin_hide_wp_admin_redirect_slug', array(
            'sanitize_callback' => 'sanitize_text_field' // Usamos text_field para permitir slugs simples
        ));

        // --- Grupo: Social Login ---
        $social_group = 'wporlogin_social_settings_group';
        register_setting( $social_group, 'wporlogin_google_enable' );
        register_setting( $social_group, 'wporlogin_google_client_id' );
        register_setting( $social_group, 'wporlogin_google_client_secret' );
        // [NUEVO] Opción para activar/desactivar la sincronización de avatares
        register_setting( $social_group, 'wporlogin_social_avatar_sync' );
        register_setting( $social_group, 'wporlogin_social_only_register' );

    }

    // -----------------------------------------------------------------
    //  GESTIÓN DE RECURSOS (ASSETS)
    // -----------------------------------------------------------------

    public function enqueue_admin_assets($hook) {

        // Aviso de actualización
        if (get_option('delete_notice_wporlogin_condition') != 1) {        
            wp_register_script('update_notice_wporlogin', WPORLOGIN_URL . 'assets/js/update-notice.js', array('jquery', 'wp-i18n'), $this->version, true);
            wp_enqueue_script('update_notice_wporlogin');
            wp_localize_script( 'update_notice_wporlogin', 'notice_params_wporlogin', array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('my-ajax-nonce-wporlogin'),
                'action' => 'delete-notice-wp'
            ));
        }

        // Carga condicional de scripts
        $plugin_admin_hooks = array(
            'toplevel_page_wporlogin-plugin',
            'wporlogin_page_recaptcha-wporlogin-plugin',
            'wporlogin_page_remove-language-plugin',
            'wporlogin_page_limit-login-wporlogin-plugin' // Agregada la nueva página para que cargue scripts si fuera necesario
        );

        if (in_array($hook, $plugin_admin_hooks)) {
            wp_enqueue_media();
            wp_register_script('wporlogin_my_upload', WPORLOGIN_URL . 'assets/js/img-fondo.js', array('jquery', 'wp-i18n'), $this->version, true );
            wp_enqueue_script('wporlogin_my_upload'); 
            wp_set_script_translations('wporlogin_my_upload', 'wporlogin'); 
            
            wp_register_script('wporlogin-recaptcha-version', WPORLOGIN_URL . 'assets/js/wporlogin-recaptcha-version.js', array('jquery', 'wp-i18n'), $this->version, true);
            wp_enqueue_script('wporlogin-recaptcha-version');
        }

        // Color Picker (Solo principal)
        if ($hook === 'toplevel_page_wporlogin-plugin') {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            wp_add_inline_script('wp-color-picker', 'jQuery(document).ready(function($){ $(".wporlogin-color-picker").wpColorPicker(); });');
        }
    }

    // -----------------------------------------------------------------
    //  MÉTODOS DE RENDERIZADO (VIEW CALLBACKS)
    // -----------------------------------------------------------------

    public function wporlogin_content_page_menu() {
        
        // Variables necesarias para la vista principal
        $wporlogin_is_premium = apply_filters('wporlogin_is_premium', false);
        $default_backgrounds = Wporlogin::get_default_backgrounds();

        require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-display.php';
    }

    public function recaptcha_wporlogin_content_page_menu() {
        
        // Variable necesaria para la vista de reCAPTCHA
        $recaptcha_version = get_option('recaptcha_version_wporlogin', 'none');

        require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-recaptcha.php';
    }

    public function remove_language_content_page_menu() {
        require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-remove-language.php';
    }

    public function redirects_wporlogin_content_page_menu() {
        require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-redirects.php';
    }

    // Callback para Limit Login
    public function limit_login_content_page_menu() {

        // 1. Obtenemos los datos necesarios para la vista
        $blocked_ips_report = $this->get_blocked_ips_data();

        require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-limit-login.php';
    }

    public function hide_login_content_page_menu() {
        require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-hide-login.php';
    }

    // [NUEVO] Método Callback para cargar la vista
    public function social_login_content_page_menu() {
        require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-social.php';
    }

    // [NUEVO] Callback de Automatización
    public function automation_content_page_menu() {
        require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-automation.php';
    }

    /**
     * Procesa la acción de desbloquear una IP manualmente.
     * VERSIÓN SQL: Compatible con la tabla personalizada.
     */
    public function process_unlock_ip_action() {
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'limit-login-wporlogin-plugin' && isset( $_GET['action'] ) && $_GET['action'] === 'unlock_ip' ) {
            
            // 1. Seguridad
            if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wporlogin_unlock_ip' ) ) {
                wp_die( __( 'Security check failed.', 'wporlogin' ) );
            }
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'You do not have permission to perform this action.', 'wporlogin' ) );
            }

            // 2. Ejecución
            if ( isset( $_GET['ip'] ) ) {
                $raw_ip = sanitize_text_field( $_GET['ip'] );
                
                // Usamos el ID del usuario actual para que el mensaje sea único para él
                $user_id = get_current_user_id();

                if ( filter_var($raw_ip, FILTER_VALIDATE_IP) ) {
                    $ip_to_unlock = $raw_ip;
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'wporlogin_activity';
                    
                    // Actualizar DB
                    $result = $wpdb->update( 
                        $table_name, 
                        array( 'locked_until' => null, 'attempts' => 0 ), 
                        array( 'ip' => $ip_to_unlock ), 
                        array( '%s', '%d' ), 
                        array( '%s' )
                    );

                    // --- CAMBIO CLAVE: USAR TRANSIENT (VALOR INTERNO) ---
                    if ( $result === false ) {
                        // Error DB
                        set_transient( 'wporlogin_flash_error_' . $user_id, 'db_error', 60 );
                    } else {
                        // Éxito
                        set_transient( 'wporlogin_flash_success_' . $user_id, 'unlocked', 60 );
                        // [CORREGIDO] Invalidad caché para que el widget del dashboard se actualice
                        delete_transient( 'wporlogin_stats_count' );
                    }

                    // Redirigir a URL LIMPIA (Sin parámetros GET molestos)
                    $clean_url = admin_url( 'admin.php?page=limit-login-wporlogin-plugin' );
                    wp_safe_redirect( $clean_url );
                    exit;

                } else {
                    // IP Inválida
                    set_transient( 'wporlogin_flash_error_' . $user_id, 'invalid_ip', 60 );
                    
                    $clean_url = admin_url( 'admin.php?page=limit-login-wporlogin-plugin' );
                    wp_safe_redirect( $clean_url );
                    exit;
                }
            }
        }
    }

    /**
     * Sanitiza el array de reglas de redirección por rol.
     * Elimina roles vacíos y asegura que las URLs sean seguras.
     *
     * @param array $input El array crudo desde el formulario $_POST.
     * @return array El array limpio para guardar en BD.
     */
    public function sanitize_redirect_rules( $input ) {
        $clean_rules = array();
        
        if ( is_array( $input ) ) {
            foreach ( $input as $role => $url ) {
                // Limpiamos espacios en blanco
                $url = trim( $url );
                
                // Solo guardamos si hay algo escrito
                if ( ! empty( $url ) ) {
                    // esc_url_raw es la función correcta para guardar URLs en BD
                    $clean_rules[ $role ] = esc_url_raw( $url );
                }
            }
        }
        
        return $clean_rules;
    }

    /**
     * Método auxiliar privado para obtener el reporte de actividad.
     * MEJORADO: Ahora muestra historial completo (bloqueados y pasados).
     */
    private function get_blocked_ips_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wporlogin_activity';

        if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
            return [];
        }

        $now = current_time( 'mysql' );

        // CAMBIO EXPERTO: Quitamos el "WHERE locked_until > NOW()"
        // Queremos ver todo el historial de los últimos 30 días (que maneja el cron).
        $results = $wpdb->get_results( 
            "SELECT * FROM $table_name ORDER BY last_attempt DESC" 
        );

        $report_data = [];
        if ( ! empty( $results ) ) {
            foreach ( $results as $row ) {
                $is_blocked = false;
                $time_left_str = '-';
                $status_code = 'unlocked'; // unlocked, blocked, expired

                // Lógica de Estado
                if ( $row->locked_until ) {
                    $now_ts = current_time( 'timestamp' );
                    $unlock_ts = strtotime( $row->locked_until );
                    
                    if ( $unlock_ts > $now_ts ) {
                        // Está bloqueado actualmente
                        $is_blocked = true;
                        $status_code = 'blocked';
                        $seconds_left = $unlock_ts - $now_ts;
                        $minutes = ceil( $seconds_left / 60 );
                        $time_left_str = $minutes . ' ' . __( 'min', 'wporlogin' );
                    } else {
                        // Tuvo fecha de bloqueo, pero ya pasó (Expiró)
                        $status_code = 'expired';
                        $time_left_str = __( 'Time expired', 'wporlogin' );
                    }
                } else {
                    // locked_until es NULL (Desbloqueado manualmente o nunca bloqueado)
                    $status_code = 'unlocked';
                    $time_left_str = __( '-', 'wporlogin' );
                }

                $report_data[] = [
                    'ip'          => $row->ip,
                    'attempts'    => $row->attempts, // Útil mostrar esto también
                    'start_date'  => date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $row->last_attempt ) ),
                    'time_left'   => $time_left_str,
                    'status_code' => $status_code, // Para usar en la vista
                    'is_blocked'  => $is_blocked
                ];
            }
        }
        return $report_data;
    }

    /**
     * Limpia la lista blanca para guardar SOLO IPs válidas.
     * Esto evita errores de configuración por parte del usuario.
     */
    public function sanitize_ip_whitelist( $input ) {
        if ( empty( $input ) ) return '';

        $lines = preg_split( '/[\r\n,]+/', $input );
        $valid_ips = array();

        foreach ( $lines as $line ) {
            $ip = trim( $line );
            // Validamos que sea una IP real (IPv4 o IPv6)
            if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                $valid_ips[] = $ip;
            }
        }

        // Devolvemos la lista limpia unida por saltos de línea
        return implode( "\n", $valid_ips );
    }

    /**
     * AMNISTÍA: Al guardar la lista blanca, "limpiamos" el historial de esas IPs.
     * VERSIÓN CORRECTA: Usa UPDATE para mantener el registro en el reporte.
     */
    public function reset_counters_on_whitelist_save( $old_value, $new_value ) {
        
        // Si la nueva lista está vacía, no hacemos nada.
        if ( empty( $new_value ) ) {
            return;
        }

        // 1. Procesamos las IPs del textarea
        $ips_to_clear = preg_split( '/[\r\n,]+/', $new_value );
        $valid_ips = array();

        foreach ( $ips_to_clear as $ip ) {
            $ip = trim( $ip );
            if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                $valid_ips[] = $ip;
            }
        }

        if ( ! empty( $valid_ips ) ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'wporlogin_activity';

            // Preparamos los placeholders
            $placeholders = implode( ',', array_fill( 0, count( $valid_ips ), '%s' ) );

            // --- AQUÍ ESTÁ LA DIFERENCIA CLAVE ---
            // Usamos UPDATE en lugar de DELETE.
            // Esto pone el contador a 0 y quita el bloqueo, PERO mantiene la fila
            // para que aparezca en el reporte como "Unlocked".
            $sql = "UPDATE $table_name 
                    SET attempts = 0, locked_until = NULL 
                    WHERE ip IN ($placeholders)";

            // Ejecutamos
            $result = $wpdb->query( $wpdb->prepare( $sql, $valid_ips ) );
            
            // [CORREGIDO] Invalidad caché para que el widget del dashboard se actualice
            if ($result !== false) {
                delete_transient( 'wporlogin_stats_count' );
            }
        }
    }
}