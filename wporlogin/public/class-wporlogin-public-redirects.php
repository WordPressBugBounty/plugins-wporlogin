<?php
/**
 * Políticas de redirección de acceso y cierre de sesión de WPOrLogin.
 *
 * Mantiene las opciones existentes. Prioridad de acceso:
 * 1. Módulo desactivado: destino del sistema/solicitado.
 * 2. Administrador protegido: escritorio.
 * 3. Regla explícita por rol (jerarquía estable para múltiples roles).
 * 4. Regla global configurada.
 * 5. Destino que WordPress o una integración ya habían solicitado.
 *
 * Los destinos configurados por el administrador se verifican mediante
 * wp_validate_redirect(). Un dominio externo se autoriza únicamente cuando
 * aparece en la URL seleccionada expresamente en las opciones del módulo.
 *
 * @package Wporlogin
 * @subpackage Wporlogin/public
 */
class Wporlogin_Public_Redirects {

    /** @var string */
    private $plugin_name;

    /** @var string */
    private $version;

    /**
     * Hosts aprobados por una regla administrativa durante ESTA petición.
     * No se persisten ni se obtienen de los parámetros del visitante.
     *
     * @var string[]
     */
    private static $allowed_external_hosts = array();

    /** @var bool */
    private static $host_filter_registered = false;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Filtra el destino del inicio de sesión tradicional.
     *
     * @param string           $redirect_to URL que el sistema iba a utilizar.
     * @param string           $requested_redirect_to URL solicitada en el login.
     * @param WP_User|WP_Error $user Resultado de la autenticación.
     * @return string
     */
    public function custom_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
        return self::get_calculated_redirect_url( $user, $redirect_to );
    }

    /**
     * Filtra el destino de logout, sin interrumpir wp_logout ni otros callbacks.
     * WordPress hace después el wp_safe_redirect() correspondiente.
     *
     * @param string  $redirect_to Destino que WordPress calculó.
     * @param string  $requested_redirect_to Destino solicitado por el usuario.
     * @param WP_User $user Usuario que cerró sesión.
     * @return string
     */
    public function custom_logout_redirect( $redirect_to, $requested_redirect_to = '', $user = null ) {
        if ( '1' !== (string) get_option( 'wporlogin_enable_logout_redirect' ) ) {
            return $redirect_to;
        }

        $configured_url = get_option( 'wporlogin_logout_redirect' );
        if ( ! is_string( $configured_url ) || '' === trim( $configured_url ) ) {
            return $redirect_to;
        }

        $validated_url = self::validate_configured_destination( $configured_url );
        return '' !== $validated_url ? $validated_url : $redirect_to;
    }

    /**
     * Calcula el destino de login para WordPress y Social Login.
     * No cambia el contrato estático utilizado por Google Login.
     *
     * @param WP_User|WP_Error $user Usuario autenticado.
     * @param string           $fallback_url Destino solicitado previamente.
     * @return string
     */
    public static function get_calculated_redirect_url( $user, $fallback_url = '' ) {
        if ( ! is_a( $user, 'WP_User' ) ) {
            return $fallback_url;
        }

        if ( '1' !== (string) get_option( 'wporlogin_enable_login_redirect' ) ) {
            return '' !== $fallback_url ? $fallback_url : admin_url();
        }

        // La casilla «Apply redirection to Administrators too» tiene prioridad
        // sobre las reglas por rol, también cuando existe una regla Administrator.
        if (
            user_can( $user, 'manage_options' ) &&
            '1' !== (string) get_option( 'wporlogin_enable_admin_redirect' )
        ) {
            return admin_url();
        }

        $role_rules = get_option( 'wporlogin_redirect_roles_rules', array() );
        if ( is_array( $role_rules ) && ! empty( $user->roles ) && is_array( $user->roles ) ) {
            foreach ( self::get_ordered_roles( $user->roles ) as $role ) {
                if ( ! isset( $role_rules[ $role ] ) || ! is_string( $role_rules[ $role ] ) ) {
                    continue;
                }

                $candidate = self::validate_configured_destination( $role_rules[ $role ] );
                if ( '' !== $candidate ) {
                    return $candidate;
                }
            }
        }

        $global_url = get_option( 'wporlogin_login_redirect' );
        if ( is_string( $global_url ) && '' !== trim( $global_url ) ) {
            $candidate = self::validate_configured_destination( $global_url );
            if ( '' !== $candidate ) {
                return $candidate;
            }
        }

        return '' !== $fallback_url ? $fallback_url : home_url();
    }

    /**
     * Orden fijo para usuarios con múltiples roles.
     * Una regla específica de un rol más privilegiado tiene prioridad; los roles
     * personalizados se ordenan alfabéticamente para evitar un orden accidental.
     * Un usuario de un solo rol mantiene exactamente su comportamiento anterior.
     *
     * @param string[] $roles Roles asignados.
     * @return string[]
     */
    private static function get_ordered_roles( $roles ) {
        $roles = array_values( array_unique( array_filter( $roles, 'is_string' ) ) );
        $priority = array(
            'administrator' => 0,
            'shop_manager'  => 1,
            'editor'        => 2,
            'author'        => 3,
            'contributor'   => 4,
            'customer'      => 5,
            'subscriber'    => 6,
        );

        usort( $roles, static function ( $left, $right ) use ( $priority ) {
            $left_rank  = isset( $priority[ $left ] ) ? $priority[ $left ] : 7;
            $right_rank = isset( $priority[ $right ] ) ? $priority[ $right ] : 7;
            return $left_rank === $right_rank ? strcmp( $left, $right ) : $left_rank - $right_rank;
        } );

        return $roles;
    }

    /**
     * Verifica exclusivamente URLs guardadas por el administrador.
     * Rechaza esquemas distintos de HTTP(S), URLs con credenciales y destinos
     * malformados; las rutas relativas son aceptadas por wp_validate_redirect.
     * Nunca utilizar este método para autorizar un `redirect_to` arbitrario.
     *
     * @param string $url URL administrativa configurada.
     * @return string URL segura, o cadena vacía si es inválida.
     */
    private static function validate_configured_destination( $url ) {
        $url = trim( $url );
        if ( '' === $url || 0 === strpos( $url, '//' ) || preg_match( '/[\x00-\x1F\x7F]/', $url ) ) {
            return '';
        }

        $url = esc_url_raw( $url, array( 'http', 'https' ) );
        if ( '' === $url ) {
            return '';
        }

        $parts = wp_parse_url( $url );
        if ( ! is_array( $parts ) || isset( $parts['user'] ) || isset( $parts['pass'] ) ) {
            return '';
        }

        // Para URLs absolutas, únicamente HTTP/HTTPS y hostname válido.
        if ( isset( $parts['scheme'] ) ) {
            if (
                ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ||
                empty( $parts['host'] )
            ) {
                return '';
            }
            self::allow_administratively_configured_host( strtolower( $parts['host'] ) );
        }

        // El filtro de hosts es deliberadamente temporal y limitado a este
        // destino administrativamente guardado. WordPress verifica la URL final.
        return wp_validate_redirect( $url, '' );
    }

    /**
     * Habilita únicamente el host externo de la URL elegida por el administrador.
     * El filtro se registra a petición, no de forma global desde plugins_loaded.
     *
     * @param string $host Nombre del host.
     * @return void
     */
    private static function allow_administratively_configured_host( $host ) {
        if ( '' === $host ) {
            return;
        }

        $site_host = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
        if ( $host === $site_host ) {
            return;
        }

        self::$allowed_external_hosts[ $host ] = true;
        if ( ! self::$host_filter_registered ) {
            add_filter( 'allowed_redirect_hosts', array( __CLASS__, 'filter_approved_redirect_hosts' ), 10, 2 );
            self::$host_filter_registered = true;
        }
    }

    /**
     * Añade a la lista de WordPress SOLO el host previamente aprobado.
     *
     * @param string[] $hosts Hosts permitidos por WordPress y otras extensiones.
     * @param string   $host Host de la URL sometida a validación.
     * @return string[]
     */
    public static function filter_approved_redirect_hosts( $hosts, $host ) {
        $host = strtolower( (string) $host );
        if ( isset( self::$allowed_external_hosts[ $host ] ) ) {
            $hosts[] = $host;
        }
        return array_values( array_unique( $hosts ) );
    }
}
