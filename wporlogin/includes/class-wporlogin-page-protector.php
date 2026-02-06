<?php
/**
 * Protege la página del plugin contra borrados accidentales.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/includes
 */

class Wporlogin_Page_Protector {

    /**
     * Evita que la página configurada sea enviada a la papelera.
     *
     * @param int $post_id El ID del post que se intenta borrar.
     */
    public function prevent_deletion( $post_id ) {
        
        // 1. Permitir borrado si el plugin se está desactivando (Variable global definida en Deactivator)
        global $wporlogin_is_deactivating;
        if ( isset( $wporlogin_is_deactivating ) && $wporlogin_is_deactivating === true ) {
            return;
        }

        // 2. Obtener la página protegida
        $protected_page_id = get_option( 'wporlogin_page_id' );

        // 3. Comparar IDs
        if ( $protected_page_id && (int) $post_id === (int) $protected_page_id ) {
            
            // Establecer un mensaje de error temporal (Transient)
            set_transient( 'wporlogin_delete_error', true, 45 );

            // Redirigir y detener la acción
            wp_safe_redirect( admin_url( 'edit.php?post_type=page' ) );
            exit;
        }
    }

    /**
     * Muestra una notificación al usuario si intentó borrar la página.
     */
    public function show_warning_notice() {
        if ( get_transient( 'wporlogin_delete_error' ) ) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <strong><?php esc_html_e( 'Acción bloqueada:', 'wporlogin' ); ?></strong> 
                    <?php esc_html_e( 'La página de "Login Personalizado" es necesaria para que el plugin funcione y no puede ser eliminada manualmente.', 'wporlogin' ); ?>
                </p>
            </div>
            <?php
            delete_transient( 'wporlogin_delete_error' );
        }
    }

    /**
	 * Elimina la opción "Edición Rápida" (Quick Edit) solo para la página del plugin.
	 * Esto evita cambios accidentales de Slug o Título desde el listado.
	 *
	 * @param array   $actions Array de acciones (Editar, Papelera, Ver, etc.).
	 * @param WP_Post $post    El objeto del post actual.
	 * @return array  Array de acciones modificado.
	 */
	public function remove_quick_edit_link( $actions, $post ) {
		
		$protected_page_id = get_option( 'wporlogin_page_id' );

		// Verificamos si la fila actual corresponde a nuestra página
		if ( $protected_page_id && (int) $post->ID === (int) $protected_page_id ) {
			
			// Quitamos la "Edición Rápida"
			// 'inline hide-if-no-js' es la clave interna de WP para el Quick Edit
			if ( isset( $actions['inline hide-if-no-js'] ) ) {
				unset( $actions['inline hide-if-no-js'] );
			}

			// OPCIONAL: También podríamos cambiar el texto de "Editar" para avisar que va al personalizador
			// if ( isset( $actions['edit'] ) ) {
			//    $actions['edit'] = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post->ID ), __( 'Personalizar Diseño', 'wporlogin' ) );
			// }
		}

		return $actions;
	}
}