<?php

/**
 * La clase responsable de la lógica del Widget de Escritorio.
 * POO: Responsabilidad Única (Solo datos y registro).
 */
class Wporlogin_Admin_Dashboard {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    public function add_dashboard_widget() {
        // SEGURIDAD: Solo mostrar a usuarios que pueden gestionar opciones (Admins)
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        wp_add_dashboard_widget(
            'wporlogin_status_widget',
            '🛡️ WPOrLogin - System Status',
            array( $this, 'render_dashboard_widget' )
        );
    }

    public function render_dashboard_widget() {
        // 1. Recopilar Datos
        $stats   = $this->get_blocked_ips_count();
        $modules = $this->get_modules_status();

        // 2. Cargar Vista
        require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-dashboard-widget.php';
    }

    /**
     * Obtiene el conteo optimizado usando Transients (Caché).
     * Evita consultas pesadas a la BD en cada carga del escritorio.
     */
    private function get_blocked_ips_count() {
        // 1. Intentamos obtener el dato de la caché (Transient)
        $cached_count = get_transient( 'wporlogin_stats_count' );
        
        if ( false !== $cached_count ) {
            return (int) $cached_count;
        }

        // 2. Si no existe caché, consultamos la BD
        global $wpdb;
        $table_name = $wpdb->prefix . 'wporlogin_activity';
        
        // Verificación de seguridad por si la tabla no existe
        if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
            return 0;
        }

        $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        // 3. Guardamos en caché por 1 hora (3600 segundos)
        // Nota: Debes borrar este transient cuando insertes un nuevo bloqueo para que se actualice al instante.
        set_transient( 'wporlogin_stats_count', $count, 12 * HOUR_IN_SECONDS ); // Guardar por 12 horas es seguro

        return $count;
    }

    /**
     * Obtiene el estado de los 6 módulos principales.
     */
    private function get_modules_status() {
        $recaptcha_v = get_option('recaptcha_version_wporlogin', 'none');
        
        // Lógica para Redirect: Está activo si Login O Logout están habilitados
        $redirect_active = ( get_option('wporlogin_enable_login_redirect') || get_option('wporlogin_enable_logout_redirect') );

        return array(
            'social'    => (bool) get_option('wporlogin_google_enable'),
            'hide'      => get_option('wporlogin_hide_login_slug'),
            'limit'     => (bool) get_option('wporlogin_limit_enable'),
            'recaptcha' => ($recaptcha_v !== 'none') ? strtoupper($recaptcha_v) : false,
            'redirect'  => $redirect_active,
            'language'  => (bool) get_option('remove_language_wporlogin')
        );
    }
}