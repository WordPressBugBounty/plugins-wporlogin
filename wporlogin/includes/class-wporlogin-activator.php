<?php

/**
 * Fired during plugin activation
 *
 * @link       https://oregoom.com/
 * @since      1.0.0
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/includes
 */

// Seguridad básica: Si este archivo es llamado directamente, abortar.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wporlogin
 * @subpackage Wporlogin/includes
 * @author     Oregoom <oregoom@gmail.com>
 */
class Wporlogin_Activator {

	/**
	 * Esta función se ejecuta al activar el plugin.
	 * * Realiza las siguientes tareas:
	 * 1. Crea la tabla de base de datos para limitar intentos de login.
	 * 2. Crea la página 'wporlogin' necesaria para el diseño personalizado.
	 * 3. Programa tareas de mantenimiento (Cron).
	 * 4. Actualiza la versión del plugin en las opciones.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		/**
		 * -----------------------------------------------------------
		 * 1. CREACIÓN DE LA TABLA EN BASE DE DATOS (Limit Login Attempts)
		 * -----------------------------------------------------------
		 */
		global $wpdb;
		
		$table_name      = $wpdb->prefix . 'wporlogin_activity';
		$charset_collate = $wpdb->get_charset_collate();

		// Estructura SQL. 
		// dbDelta es mágico: si la tabla existe, solo agrega columnas nuevas si faltan. No borra datos.
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			ip varchar(45) NOT NULL,
			attempts mediumint(9) DEFAULT 0 NOT NULL,
			last_attempt datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			locked_until datetime DEFAULT NULL,
			PRIMARY KEY  (id),
			KEY ip (ip)
		) $charset_collate;";

		// Necesario para usar dbDelta
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		// -----------------------------------------------------------
		// 2. CREACIÓN INTELIGENTE DE LA PÁGINA
		// -----------------------------------------------------------
		
		$page_id = 0;

		// PASO A: Buscar si ya existe NUESTRA página (buscamos por la "firma", no por el título)
		// Esto es clave: Buscamos una página que tenga nuestro meta key especial.
		$existing_pages = new WP_Query( array(
			'post_type'      => 'page',
			'post_status'    => 'any', // Incluso si está en la papelera
			'posts_per_page' => 1,
			'meta_key'       => '_wporlogin_is_system_page', // <--- LA FIRMA DEL EXPERTO
			'meta_value'     => '1',
			'fields'         => 'ids', // Solo queremos el ID para ser rápidos
			'no_found_rows'  => true,
		) );

		if ( ! empty( $existing_pages->posts ) ) {
			$page_id = $existing_pages->posts[0];
		}

		// PASO B: Si no encontramos NUESTRA página, creamos una nueva.
		if ( ! $page_id ) {
			
			// Intentamos crearla con el slug 'wporlogin'.
			// SI EL USUARIO YA TIENE UNA PÁGINA CON ESE NOMBRE:
			// WordPress automáticamente renombrará la nuestra a 'wporlogin-2'.
			// Esto es perfecto, porque no rompemos la página del usuario.
			
			$new_page_id = wp_insert_post( array(
				'post_title'     => 'WPOrLogin', // Título más genérico y limpio
				'post_name'      => 'wporlogin', // El slug deseado
				'post_content'   => '', // Contenido mínimo
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed',
			) );

			if ( ! is_wp_error( $new_page_id ) ) {
				$page_id = $new_page_id;
				
				// [IMPORTANTE] Le ponemos la firma para reconocerla en el futuro
				update_post_meta( $page_id, '_wporlogin_is_system_page', '1' );
			}
		}

		// PASO C: Guardar el ID final en las opciones
		if ( $page_id ) {
			// Si la página estaba en la papelera, la restauramos
			if ( 'trash' === get_post_status( $page_id ) ) {
				wp_publish_post( $page_id );
			}
			
			update_option( 'wporlogin_page_id', $page_id );
		}

		/**
		 * -----------------------------------------------------------
		 * 3. TAREAS DE MANTENIMIENTO Y VERSIÓN
		 * -----------------------------------------------------------
		 */

		// Guardamos la versión actual para control de actualizaciones futuras
		if ( defined( 'WPORLOGIN_VERSION' ) ) {
			update_option( 'wporlogin_version', WPORLOGIN_VERSION );
		}

		// Programar evento diario de limpieza (Cron) si no existe
		// Esto sirve para limpiar logs viejos de intentos de login
		if ( ! wp_next_scheduled( 'wporlogin_daily_maintenance_hook' ) ) {
			wp_schedule_event( time(), 'daily', 'wporlogin_daily_maintenance_hook' );
		}

	}

}
