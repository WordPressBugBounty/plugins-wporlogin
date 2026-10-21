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
     * Rangos oficiales de los proxies de Cloudflare (IPv4 e IPv6).
     *
     * Fuente: https://www.cloudflare.com/ips-v4 y https://www.cloudflare.com/ips-v6
     * Revisados: 2026-10-19. Actualizar la lista si Cloudflare modifica sus rangos.
     * Solo confiamos en CF-Connecting-IP cuando REMOTE_ADDR está en esta lista.
     *
     * @var string[]
     */
    private const CLOUDFLARE_IP_RANGES = array(
        '173.245.48.0/20',
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '141.101.64.0/18',
        '108.162.192.0/18',
        '190.93.240.0/20',
        '188.114.96.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '162.158.0.0/15',
        '104.16.0.0/13',
        '104.24.0.0/14',
        '172.64.0.0/13',
        '131.0.72.0/22',
        '2400:cb00::/32',
        '2606:4700::/32',
        '2803:f800::/32',
        '2405:b500::/32',
        '2405:8100::/32',
        '2a06:98c0::/29',
        '2c0f:f248::/32',
    );

    /**
     * Resuelve la IP para bloqueos y lista blanca sin confiar en cabeceras arbitrarias.
     *
     * - Directo: REMOTE_ADDR (IP observada por el servidor).
     * - Cloudflare: CF-Connecting-IP solo si REMOTE_ADDR pertenece a Cloudflare.
     * - Otro proxy: X-Forwarded-For, únicamente cuando el administrador declara
     *   sus IP/CIDR confiables mediante WPORLOGIN_TRUSTED_PROXIES en wp-config.php.
     *   Se recorre la cadena de derecha a izquierda, nunca se toma la primera
     *   dirección sin validar la cadena de proxies.
     *
     * Si no se puede establecer el origen con seguridad, se conserva REMOTE_ADDR.
     * Los servidores que reescriben REMOTE_ADDR con la IP real no requieren ajustes.
     *
     * @return string IP válida, o cadena vacía si el servidor no proporciona una.
     */
    private function get_client_ip() {
        $remote_ip = isset( $_SERVER['REMOTE_ADDR'] ) && is_string( $_SERVER['REMOTE_ADDR'] )
            ? trim( $_SERVER['REMOTE_ADDR'] )
            : '';

        if ( ! filter_var( $remote_ip, FILTER_VALIDATE_IP ) ) {
            return '';
        }

        // CF-Connecting-IP solo es confiable si nos conecta una IP de Cloudflare.
        if ( $this->ip_in_ranges( $remote_ip, self::CLOUDFLARE_IP_RANGES ) ) {
            $cf_ip = isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) && is_string( $_SERVER['HTTP_CF_CONNECTING_IP'] )
                ? trim( $_SERVER['HTTP_CF_CONNECTING_IP'] )
                : '';

            // No usar X-Forwarded-For como respaldo: el origen de sus entradas
            // puede ser ambiguo. Si falta la cabecera esperada, usamos REMOTE_ADDR.
            return filter_var( $cf_ip, FILTER_VALIDATE_IP ) ? $cf_ip : $remote_ip;
        }

        // Otros proxies: deben configurarse explícitamente en wp-config.php.
        // Jamás confiar automáticamente en direcciones privadas (por ejemplo, 127.0.0.1).
        $trusted_proxies = defined( 'WPORLOGIN_TRUSTED_PROXIES' ) && is_array( WPORLOGIN_TRUSTED_PROXIES )
            ? WPORLOGIN_TRUSTED_PROXIES
            : array();

        if ( ! $this->ip_in_ranges( $remote_ip, $trusted_proxies ) ) {
            return $remote_ip;
        }

        $forwarded_for = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && is_string( $_SERVER['HTTP_X_FORWARDED_FOR'] )
            ? $_SERVER['HTTP_X_FORWARDED_FOR']
            : '';

        // Un límite conservador evita procesar cabeceras excesivamente largas.
        if ( '' === $forwarded_for || strlen( $forwarded_for ) > 2048 ) {
            return $remote_ip;
        }

        $chain = explode( ',', $forwarded_for );
        if ( count( $chain ) > 20 ) {
            return $remote_ip;
        }

        // El proxy inmediato es confiable; recorremos saltos anteriores desde
        // el más próximo hasta encontrar el primer origen NO confiable.
        for ( $index = count( $chain ) - 1; $index >= 0; $index-- ) {
            $candidate = trim( $chain[ $index ] );

            if ( ! filter_var( $candidate, FILTER_VALIDATE_IP ) ) {
                return $remote_ip;
            }

            if ( ! $this->ip_in_ranges( $candidate, $trusted_proxies ) ) {
                return $candidate;
            }
        }

        // Todos los saltos declarados eran proxies: no se infiere otro origen.
        return $remote_ip;
    }

    /**
     * Determina si una IP pertenece a alguna dirección o bloque CIDR confiable.
     * Compatible con IPv4, IPv6 y PHP 7.4; no utiliza servicios externos.
     *
     * @param string $ip     IP a comprobar.
     * @param array  $ranges Direcciones individuales o rangos CIDR.
     * @return bool
     */
    private function ip_in_ranges( $ip, $ranges ) {
        $ip_binary = @inet_pton( $ip );
        if ( false === $ip_binary || ! is_array( $ranges ) ) {
            return false;
        }

        foreach ( $ranges as $range ) {
            if ( ! is_string( $range ) ) {
                continue;
            }

            $parts          = explode( '/', trim( $range ), 2 );
            $network_binary = @inet_pton( $parts[0] );

            if ( false === $network_binary || strlen( $ip_binary ) !== strlen( $network_binary ) ) {
                continue;
            }

            $maximum_bits = strlen( $ip_binary ) * 8;
            $prefix_bits  = isset( $parts[1] ) ? $parts[1] : (string) $maximum_bits;

            // Rechazar prefijos vacíos, negativos, fuera de rango o mal escritos.
            if ( ! ctype_digit( (string) $prefix_bits ) || (int) $prefix_bits > $maximum_bits ) {
                continue;
            }

            $prefix_bits = (int) $prefix_bits;
            $whole_bytes = intdiv( $prefix_bits, 8 );
            $leftover    = $prefix_bits % 8;

            if ( $whole_bytes > 0 && substr( $ip_binary, 0, $whole_bytes ) !== substr( $network_binary, 0, $whole_bytes ) ) {
                continue;
            }

            if ( $leftover > 0 ) {
                $mask = ( 0xff << ( 8 - $leftover ) ) & 0xff;
                if ( ( ord( $ip_binary[ $whole_bytes ] ) & $mask ) !== ( ord( $network_binary[ $whole_bytes ] ) & $mask ) ) {
                    continue;
                }
            }

            return true;
        }

        return false;
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
        if ( '' === $ip ) {
            return $user;
        }
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
        if ( '' === $ip ) {
            return;
        }
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
                $locked_until = gmdate( 'Y-m-d H:i:s', $current_timestamp + ( $lock_time * 60 ) );
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
                 $locked_until = gmdate( 'Y-m-d H:i:s', $current_timestamp + ( $lock_time * 60 ) );
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
        if ( '' === $ip ) {
            return $error;
        }
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
                /* translators: %d: Remaining failed login attempts before the account or IP is locked. */
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
        
        // El nombre de la tabla procede del prefijo interno de WordPress;
        // todos los datos variables se envían mediante placeholders.
        $result = $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$this->table_name}
             WHERE (locked_until IS NULL OR locked_until < %s)
             AND last_attempt < DATE_SUB(%s, INTERVAL %d DAY)",
            $now,
            $now,
            $days_retention
        ) );

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