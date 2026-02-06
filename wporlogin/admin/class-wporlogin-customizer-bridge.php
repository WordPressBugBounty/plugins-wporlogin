<?php

/**
 * Gestiona las redirecciones hacia el Personalizador (Customizer).
 *
 * Su objetivo es evitar que el administrador edite la página 'wporlogin'
 * usando el editor clásico o Gutenberg, y lo fuerce a usar el Personalizador visual.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/admin
 */

class Wporlogin_Customizer_Bridge {

	public function redirect_from_frontend() {
		global $post;
		$page_id = get_option( 'wporlogin_page_id' );

		// 1. Verificamos que sea nuestra página
		if ( isset( $post->ID ) && (int) $post->ID === (int) $page_id ) {

			// --- LA CORRECCIÓN CLAVE ---
			// Preguntamos: ¿Estamos en la vista previa del Personalizador?
			// Si es TRUE, hacemos 'return' para NO redirigir.
			// Esto permite que el iframe cargue tu plantilla HTML y tú puedas editar el CSS en vivo.
			if ( is_customize_preview() ) {
				return; 
			}

			// --- LÓGICA DE REDIRECCIÓN ---
			// Si llegamos aquí, NO estamos en el personalizador.

			// A. Si es el Admin (o alguien que puede editar temas)
			if ( current_user_can( 'edit_theme_options' ) ) {
				// Lo ayudamos redirigiéndolo al panel de edición
				$this->perform_redirect( $page_id );
			} 
			// B. Si es un visitante normal
			else {
				// Lo protegemos redirigiéndolo al inicio (o al login real)
				wp_safe_redirect( home_url() );
				exit;
			}
		}
	}

	/**
	 * Redirige al personalizador desde el BACKEND (Editor).
	 * Se ejecuta cuando un admin hace clic en "Editar" en el listado de páginas.
	 */
	public function redirect_from_editor() {
		global $pagenow;

		// Verificamos si estamos en la pantalla de edición de post
		if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {
			$post_id = intval( $_GET['post'] );
			
			// Si el post que intentan editar es nuestra página...
			$page_id = get_option( 'wporlogin_page_id' );
			
			if ( (int) $post_id === (int) $page_id ) {
				
				// Verificamos que no sea una acción de borrar (trash)
				$action = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : '';
				if ( 'trash' !== $action ) {
					$this->perform_redirect( $post_id );
				}
			}
		}
	}

	/**
	 * Lógica central de la redirección.
	 * * @param int $post_id ID de la página.
	 */
	private function perform_redirect( $post_id ) {
		
		// Evitar bucle infinito si ya estamos en el personalizador
		if ( is_customize_preview() ) {
			return;
		}

		$page_url = get_permalink( $post_id );
	
		// CORRECCIÓN: Usamos 'autofocus[panel]' en lugar de 'section'.
		// El ID del panel registrado en class-wporlogin-customizer.php es 'wporlogin_panel_login'.
		$redirect_url = admin_url( 'customize.php?autofocus[panel]=wporlogin_panel_login&url=' . urlencode( $page_url ) );

		wp_safe_redirect( $redirect_url );
		exit;
	}

}