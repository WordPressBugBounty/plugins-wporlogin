<?php

/**
 * La funcionalidad pública del módulo Limit Login Attempts.
 *
 * Responsabilidad: Proteger el sitio contra ataques de fuerza bruta.
 * NO debe encargarse de generar reportes HTML ni lógica administrativa.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/public
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin_Public_Limit_Login {

    private $plugin_name;
    private $version;
    private $table_name;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wporlogin_activity';
    }

    /**
     * Obtiene la IP del cliente de forma compatible con CDNs (Cloudflare, SiteGround).
     */
    private function get_client_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];

        // 1. Prioridad: Cloudflare
        // Si existe esta cabecera, es muy probable que venga de Cloudflare y es la IP real.
        if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        // 2. Estándar: X-Forwarded-For (SiteGround, AWS, Varnish, Nginx)
        // Esta cabecera suele ser una lista: "IP_REAL, PROXY_1, PROXY_2"
        elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            // Convertimos la lista en array
            $ip_list = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
            // Tomamos SIEMPRE la primera IP, que corresponde al cliente original
            $ip = trim( reset( $ip_list ) );
        }
        // 3. Otros posibles proxies (Opcional, pero X-Forwarded-For suele bastar)
        elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }

        // 4. VALIDACIÓN DE SEGURIDAD (CRÍTICO)
        // Nos aseguramos de que lo que capturamos parezca una IP real (IPv4 o IPv6).
        // Si un hacker mandó texto basura o código malicioso, esto lo limpia y vuelve a la IP del servidor.
        if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
            return $ip;
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Verifica si la IP actual está en la lista blanca.
     * * CORRECCIÓN DE SEGURIDAD: Se ha eliminado la validación por Nombre de Usuario.
     * Permitir whitelisting por username crea una vulnerabilidad de fuerza bruta.
     *
     * @return bool True si la IP está autorizada.
     */
    private function is_whitelisted() {
        $whitelist_raw = get_option( 'wporlogin_limit_whitelist', '' );
        
        if ( empty( $whitelist_raw ) ) {
            return false;
        }

        // Convertir textarea en array, normalizando saltos de línea y comas
        $whitelist = preg_split( '/[\r\n,]+/', $whitelist_raw );
        $current_ip = $this->get_client_ip();

        foreach ( $whitelist as $entry ) {
            $entry = trim( $entry );
            if ( empty( $entry ) ) continue;

            // Validación estricta: Solo comparamos si la entrada parece una IP
            // Esto evita errores si el admin escribió texto basura por accidente.
            if ( $entry === $current_ip ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hook: wp_authenticate_user
     * MEJORA POO: Uso estricto de zonas horarias de WP.
     */
    public function check_lockout( $user, $password ) {
        if ( get_option( 'wporlogin_limit_enable' ) != '1' ) return $user;
        if ( $this->is_whitelisted() ) return $user;

        $ip = $this->get_client_ip();
        global $wpdb;

        // CORRECCIÓN TIMEZONE: Usamos current_time('mysql') en lugar de NOW()
        // Esto asegura que la comparación use la hora configurada en WordPress (Ajustes > General)
        $now = current_time( 'mysql' );

        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT locked_until FROM {$this->table_name} WHERE ip = %s AND locked_until > %s",
            $ip,
            $now
        ));

        if ( $row ) {
            $default_msg = __( 'You have exceeded the maximum number of login attempts. Please try again later.', 'wporlogin' );
            $custom_msg  = get_option( 'wporlogin_limit_message', $default_msg );
            // Asegurar que no esté vacío
            if( empty(trim($custom_msg)) ) $custom_msg = $default_msg;

            return new WP_Error( 'wporlogin_locked', $custom_msg );
        }

        return $user;
    }

    /**
     * Hook: wp_login_failed
     * MEJORADO: Reinicia el contador si el bloqueo ya expiró.
     */
    public function handle_failed_login( $username = '' ) { // Añadido valor por defecto para evitar warnings
        if ( get_option( 'wporlogin_limit_enable' ) != '1' ) return;
        if ( $this->is_whitelisted() ) return;

        $ip = $this->get_client_ip();
        $max_retries = (int) get_option( 'wporlogin_limit_max_retries', 3 );
        $lock_time   = (int) get_option( 'wporlogin_limit_lock_time', 20 );
        
        global $wpdb;
        $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE ip = %s", $ip ) );
        
        $current_wp_time = current_time( 'mysql' ); 
        $current_timestamp = current_time( 'timestamp' ); // Timestamp numérico actual
        $result = false; // Inicializamos

        if ( $record ) {
            $current_attempts = $record->attempts;

            // Convertimos la fecha del último intento guardado a timestamp numérico
            $last_attempt_ts = strtotime( $record->last_attempt );
            
            // Calculamos cuántos minutos han pasado desde el último error
            $minutes_since_last = ( $current_timestamp - $last_attempt_ts ) / 60;

            // --- LÓGICA DE REINICIO MEJORADA ---
            
            // Caso 1: Estaba BLOQUEADO y el tiempo de castigo ya terminó.
            $reset_unlock = ( $record->locked_until && $record->locked_until < $current_wp_time );
            
            // Caso 2 (LO QUE FALTABA): NO estaba bloqueado, pero ha pasado mucho tiempo inactivo.
            // Si el usuario dejó pasar más tiempo que el "tiempo de bloqueo" (ej. 30 min) sin fallar,
            // asumimos que es una sesión nueva y le perdonamos los errores viejos.
            $reset_timeout = ( ! $record->locked_until && $minutes_since_last > $lock_time );

            if ( $reset_unlock || $reset_timeout ) {
                $current_attempts = 0;
            }
            // -----------------------------------

            $new_attempts = $current_attempts + 1;
            $locked_until = NULL; // Por defecto null, a menos que se bloquee abajo

            // Verificamos si ALCANZÓ el límite con este nuevo error
            if ( $new_attempts >= $max_retries ) {
                $locked_until = date( 'Y-m-d H:i:s', $current_timestamp + ( $lock_time * 60 ) );
            }

            // [BUG FIXED] Asignamos el retorno a $result
            $result = $wpdb->update(
                $this->table_name,
                array( 
                    'attempts'     => $new_attempts, 
                    'last_attempt' => $current_wp_time, 
                    'locked_until' => $locked_until // Actualizamos la fecha de bloqueo (o la limpiamos si no corresponde)
                ),
                array( 'ip' => $ip ),
                array( '%d', '%s', '%s' ),
                array( '%s' )
            );

        } else {
            // Primer registro de esta IP
            $locked_until = NULL;
            // Caso borde: Si el admin puso Max Retries = 1, se bloquea a la primera
            if ( $max_retries <= 1 ) {
                 $locked_until = date( 'Y-m-d H:i:s', $current_timestamp + ( $lock_time * 60 ) );
            }

            // [BUG FIXED] Asignamos el retorno a $result
            $result = $wpdb->insert(
                $this->table_name,
                array(
                    'ip'           => $ip,
                    'attempts'     => 1,
                    'last_attempt' => $current_wp_time,
                    'locked_until' => $locked_until
                ),
                array( '%s', '%d', '%s', '%s' )
            );
        }

        // Limpiamos caché del Dashboard si hubo cambios (Update devuelve int|false, Insert devuelve int|false)
        if ( $result !== false ) {
            $this->invalidate_stats_cache();
        }
    }

    /**
     * Hook: login_errors
     */
    public function show_remaining_attempts( $error ) {
        if ( get_option( 'wporlogin_limit_enable' ) != '1' ) return $error;
        if ( ! is_string( $error ) ) return $error;

        // Si la IP está en lista blanca, no mostramos advertencias de límites.
        if ( $this->is_whitelisted() ) {
            return $error;
        }

        $ip = $this->get_client_ip();
        global $wpdb;

        $record = $wpdb->get_row( $wpdb->prepare( "SELECT attempts, locked_until FROM {$this->table_name} WHERE ip = %s", $ip ) );
        if ( ! $record ) return $error;
        
        // Verificar bloqueo contra hora WP
        if ( $record->locked_until && strtotime($record->locked_until) > current_time('timestamp') ) {
            return $error; 
        }

        $max_retries = (int) get_option( 'wporlogin_limit_max_retries', 3 );
        $attempts    = (int) $record->attempts;
        $remaining   = $max_retries - $attempts;

        if ( $remaining < 0 ) $remaining = 0;

        if ( $remaining <= 1 ) {
            $warning = sprintf( 
                __( ' <strong>Warning</strong>: Only %d attempt remaining before lockout.', 'wporlogin' ), 
                $remaining 
            );
            return $error . ' <br/>' . $warning;
        }

        return $error;
    }

    /**
     * Hook: wp_login
     * MEJORADO: Coherencia total. No borra el registro, solo reinicia el contador.
     * La limpieza física se delega exclusivamente al Cron Job de 30 días.
     * 
     * * OPTIMIZACIÓN: Se eliminó el SELECT previo innecesario.
     */
    public function reset_counter_on_success( $user_login, $user ) {
        // 1. Obtenemos la IP
        $ip = $this->get_client_ip();

        // Validación de seguridad: Si la IP está vacía, no hacemos nada.
        if ( empty( $ip ) ) {
            return;
        }

        global $wpdb;

        $current_wp_time = current_time( 'mysql' );

        // 2. Ejecutamos UPDATE directo.
        // Si la IP no existe en la tabla (usuario limpio), devuelve 0 y no consume recursos.
        $result = $wpdb->update(
            $this->table_name,
            array( 
                'attempts'     => 0, 
                'locked_until' => null,        // Asegúrate que tu columna en BD acepte NULL
                'last_attempt' => $current_wp_time 
            ),
            array( 'ip' => $ip ),              // WHERE
            array( '%d', '%s', '%s' ),         // Formatos para: attempts, locked_until, last_attempt
            array( '%s' )                      // Formato para: ip
        );

        // 3. Gestión de caché inteligente
        // Solo invalidamos la caché si $result > 0 (es decir, si la IP existía y se reseteó).
        if ( $result !== false && $result > 0 ) {
            $this->invalidate_stats_cache();
        }
    }

    /**
     * Cron Job
     */
    public function purge_old_logs() {
        global $wpdb;
        $days_retention = 30; 
        
        // Usamos current_time('mysql') para consistencia
        $now = current_time( 'mysql' );
        
        $sql = "DELETE FROM {$this->table_name} 
                WHERE (locked_until IS NULL OR locked_until < %s) 
                AND last_attempt < DATE_SUB(%s, INTERVAL %d DAY)";

        // [BUG FIXED] Asignamos $result
        $result = $wpdb->query( $wpdb->prepare( $sql, $now, $now, $days_retention ) );

        // [NUEVO] Si borramos logs viejos, el contador baja, así que limpiamos caché.
        if ( $result !== false && $result > 0 ) {
            $this->invalidate_stats_cache();
        }
    }

    /**
     * Invalida la caché de estadísticas del Dashboard.
     * Se debe llamar cada vez que se inserta, borra o bloquea una IP.
     * @since 2.12.2
     */
    private function invalidate_stats_cache() {
        delete_transient( 'wporlogin_stats_count' );
    }
}