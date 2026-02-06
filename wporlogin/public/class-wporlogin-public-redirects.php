<?php

/**
 * Gestiona las redirecciones de usuario (Login y Logout).
 * Implementa lógica jerárquica: Admin > Rol Específico > Global > Defecto.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/public
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin_Public_Redirects {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Filtra la URL de redirección tras el login.
     * Hook: apply_filters('login_redirect', ...)
     */
    public function custom_login_redirect( $redirect_to, $request, $user ) {
        // Usamos el método centralizado para evitar duplicar lógica
        return self::get_calculated_redirect_url( $user, $redirect_to );
    }

    /**
     * Ejecuta la redirección tras el logout.
     * Hook: do_action('wp_logout')
     */
    public function custom_logout_redirect() {
        $redirect_enabled = get_option( 'wporlogin_enable_logout_redirect' );
        $custom_redirect  = get_option( 'wporlogin_logout_redirect' );

        if ( $redirect_enabled == '1' && ! empty( $custom_redirect ) ) {
            // wp_safe_redirect protege contra redirecciones maliciosas a dominios no permitidos.
            // Si quieres permitir dominios externos, el usuario debe usar un filtro de WP 'allowed_redirect_hosts'
            // o podrías usar wp_redirect() si confías plenamente en el input del admin.
            // Por estándar de seguridad, usamos wp_safe_redirect() o wp_redirect() validado.
            
            wp_redirect( esc_url( $custom_redirect ) ); // Usamos wp_redirect para permitir salir del dominio (ej. a Google)
            exit;
        }
    }

    /**
     * Método ESTÁTICO y CENTRALIZADO para calcular la URL de destino.
     * MEJORA UX: Las reglas específicas por rol ahora tienen prioridad sobre el bloqueo general de admin.
     *
     * @param WP_User|WP_Error $user El objeto de usuario.
     * @param string $fallback_url URL por defecto.
     * @return string La URL final calculada.
     */
    public static function get_calculated_redirect_url( $user, $fallback_url = '' ) {
        
        // 1. Validación básica
        if ( ! is_a( $user, 'WP_User' ) ) {
            return $fallback_url;
        }

        // 2. Verificar si la redirección está habilitada globalmente
        if ( get_option( 'wporlogin_enable_login_redirect' ) != '1' ) {
            return empty($fallback_url) ? admin_url() : $fallback_url;
        }

        // -------------------------------------------------------------------------
        // CAMBIO CLAVE: Prioridad a las Reglas Específicas (Intención Explícita)
        // -------------------------------------------------------------------------
        
        // 3. BUSCAR REGLA POR ROL (Ahora va ANTES del bloqueo de admin)
        // Si el usuario configuró una URL para "Administrator", asumimos que sabe lo que hace.
        $role_rules = get_option( 'wporlogin_redirect_roles_rules', array() );
        
        if ( ! empty( $role_rules ) && ! empty( $user->roles ) ) {
            foreach ( $user->roles as $role ) {
                // Si existe una URL guardada y no está vacía para este rol
                if ( ! empty( $role_rules[ $role ] ) ) {
                    return esc_url( $role_rules[ $role ] ); // ¡Redirección Específica Encontrada!
                }
            }
        }

        // 4. SEGURIDAD ADMIN (Bloqueo de la URL Global)
        // Solo llegamos aquí si NO había una regla específica para el rol del admin.
        // Aquí sí aplicamos el "Freno de Mano" para protegerlo de la URL Global.
        if ( user_can( $user, 'manage_options' ) ) {
            $allow_admin_redirect = get_option( 'wporlogin_enable_admin_redirect' );
            if ( $allow_admin_redirect != '1' ) {
                return admin_url(); // Mandar al Dashboard
            }
        }

        // 5. URL GLOBAL (Default)
        // Solo se aplica si no hubo regla específica Y (no es admin O el admin permitió redirección global)
        $global_redirect = get_option( 'wporlogin_login_redirect' );
        if ( ! empty( $global_redirect ) ) {
            return esc_url( $global_redirect );
        }

        // 6. Fallback final
        return empty($fallback_url) ? home_url() : $fallback_url;
    }
}