<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://oregoom.com/
 * @since      1.0.0
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/includes
 */

// Seguridad básica
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Se dispara durante la desactivación del plugin.
 *
 * Esta clase define todo el código necesario para ejecutarse durante la desactivación del plugin.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/includes
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin_Deactivator {

	/**
	 * Ejecuta las tareas de limpieza necesarias al desactivar el plugin.
	 *
	 * Tareas:
	 * 1. Eliminar tareas programadas (Cron Jobs).
	 * 2. Eliminar la página generada automáticamente (limpieza visual).
	 * * Nota: NO borramos la tabla de la base de datos ni las opciones de configuración general
	 * para que el usuario no pierda sus datos si decide reactivar el plugin después.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		
		// 1. Borrar el cron job (Importante para rendimiento)
		// Detiene la tarea de limpieza diaria de la base de datos.
		wp_clear_scheduled_hook( 'wporlogin_daily_maintenance_hook' );

		// 2. PRIMERO obtenemos el ID y luego borramos la opción de la BD.
		// Al hacer esto, "desarmamos" al Protector, porque ya no sabrá qué ID proteger.
		$page_id = get_option( 'wporlogin_page_id' );
		delete_option( 'wporlogin_page_id' );

		// 3. AHORA sí borramos la página física.
		// Como ya borramos la opción en el paso anterior, el Protector (si se activa) 
		// leerá 'false' y permitirá el borrado sin bloquearnos.
		// Solo eliminamos una página que realmente pertenezca a WPOrLogin.
		// Comprobar el tipo y la marca interna evita borrar otra página
		// si el ID almacenado apunta a un contenido diferente.
		if (
			$page_id &&
			'page' === get_post_type( $page_id ) &&
			'1' === (string) get_post_meta( $page_id, '_wporlogin_is_system_page', true ) &&
			get_post_status( $page_id )
		) {
			wp_delete_post( $page_id, true );
		}

	}

}