<?php

/**
 * Se encarga de ocultar la página del plugin de menús, selectores y listados.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/public
 */

class Wporlogin_Public_Visibility {


    public function hide_from_menu_query( $query ) {
        global $pagenow;

        // 1. Verificación rápida: Solo Admin
        if ( ! is_admin() ) {
            return;
        }

        // 2. Detectar pantalla de Menús.
        // A veces $pagenow no está disponible temprano, usamos get_current_screen si es posible, o fallback.
        $is_nav_menus = ( 'nav-menus.php' === $pagenow );
        
        // 3. Verificación de seguridad de la query
        // Nos aseguramos que es la query principal del metabox de páginas
        if ( $is_nav_menus && $query->get( 'post_type' ) === 'page' ) {
            
            // OPTIMIZACIÓN: Usar una propiedad de clase si ya cargaste el ID antes, 
            // si no, get_option es aceptable aquí porque solo ocurre en el admin.
            $page_id = get_option( 'wporlogin_page_id' );

            if ( $page_id ) {
                // Combinar con exclusiones existentes si las hubiera
                $current_excludes = $query->get( 'post__not_in' );
                if ( ! is_array( $current_excludes ) ) {
                    $current_excludes = array();
                }
                $current_excludes[] = $page_id;

                $query->set( 'post__not_in', $current_excludes );
                
                // RENDIMIENTO: Detener la paginación si no es necesaria para este metabox
                // (Opcional, pero ayuda en sitios con 10,000 páginas)
                // $query->set( 'posts_per_page', 50 ); 
            }
        }
    }

	/**
	 * 2. FRONTEND: Oculta la página del menú de navegación si ya fue añadida.
	 *
	 * @param array $items Lista de objetos del menú.
	 * @return array Lista filtrada.
	 */
	public function hide_from_nav_menu( $items ) {
		$page_id = get_option( 'wporlogin_page_id' );
		if ( ! $page_id ) {
			return $items;
		}

		foreach ( $items as $key => $item ) {
			if ( isset( $item->object_id ) && (int) $item->object_id === (int) $page_id ) {
				unset( $items[ $key ] );
			}
		}
		return $items;
	}

    /**
	 * 3. FRONTEND (Menú Automático / Fallback): 
	 * Este es el que soluciona tu problema actual.
	 * Se ejecuta cuando el tema lista las páginas porque no hay menú asignado.
	 *
	 * @param array $args Argumentos del menú de página.
	 * @return array Argumentos modificados con la exclusión.
	 */
	public function hide_from_fallback_menu( $args ) {
		$page_id = get_option( 'wporlogin_page_id' );
		
		if ( $page_id ) {
			// wp_page_menu acepta 'exclude' como string separado por comas o array.
			// Nos aseguramos de agregarlo correctamente.
			if ( ! empty( $args['exclude'] ) ) {
				$args['exclude'] .= ',' . $page_id;
			} else {
				$args['exclude'] = $page_id;
			}
		}
		
		return $args;
	}

	/**
	 * 3. LISTADOS: Oculta la página de wp_list_pages (Widgets antiguos).
	 *
	 * @param array $exclusions Array de IDs excluidos.
	 * @return array Array modificado.
	 */
	public function hide_from_page_list( $exclusions ) {
		$page_id = get_option( 'wporlogin_page_id' );
		if ( $page_id ) {
			$exclusions[] = $page_id;
		}
		return $exclusions;
	}

	/**
	 * 4. GLOBAL (CORREGIDO): Excluye la página de Búsquedas y Archivos,
	 * PERO permite el acceso directo para evitar el Error 404.
	 *
	 * @param WP_Query $query La consulta actual.
	 */
	public function exclude_from_queries( $query ) {
		
		// 1. Seguridad: No afectar al Admin ni a consultas secundarias
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		// 2. LA CORRECCIÓN:
		// Si la consulta es "singular" (is_page), significa que WordPress está intentando cargar
		// la página específica porque el usuario puso la URL. ¡Aquí NO debemos bloquearla!
		// Si la bloqueamos aquí, da 404.
		if ( $query->is_singular() || $query->is_page() ) {
			return;
		}

		// 3. Solo ocultar en lugares donde se muestran "listas" de contenidos:
		// - Home / Blog (is_home)
		// - Resultados de búsqueda (is_search)
		// - Archivos de fecha/autor (is_archive)
		// - Feeds RSS (is_feed)
		if ( $query->is_home() || $query->is_search() || $query->is_archive() || $query->is_feed() ) {
			
			$page_id = get_option( 'wporlogin_page_id' );

			if ( $page_id ) {
				$post__not_in = $query->get( 'post__not_in' );
				
				if ( ! is_array( $post__not_in ) ) {
					$post__not_in = array();
				}

				$post__not_in[] = $page_id;

				$query->set( 'post__not_in', $post__not_in );
			}
		}
	}

    /**
	 * 6. TEMAS MODERNOS (Bloques/FSE):
	 * Este es el filtro que te faltaba. Intercepta la función get_pages()
	 * que usan los bloques de navegación automática.
	 *
	 * @param array $pages Array de objetos de página encontrados.
	 * @return array Array filtrado.
	 */
	public function hide_from_get_pages( $pages ) {
		
		// ¡IMPORTANTE! No ocultar en el Admin, queremos verla en "Todas las páginas"
		if ( is_admin() ) {
			return $pages;
		}

		$page_id = get_option( 'wporlogin_page_id' );
		if ( ! $page_id ) {
			return $pages;
		}

		// Recorremos las páginas y quitamos la nuestra del array
		foreach ( $pages as $key => $page ) {
			if ( isset( $page->ID ) && (int) $page->ID === (int) $page_id ) {
				unset( $pages[ $key ] );
			}
		}

		// Reordenar índices (opcional pero recomendado)
		return array_values( $pages );
	}

    /**
     * 7. REST API (Editor de Bloques / FSE):
     * Oculta la página en el selector del Editor de Sitio y Bloques.
     * Esto evita que aparezca en el bloque "Lista de Páginas" y en la sidebar de Navegación automática.
     *
     * @param array           $args    Argumentos de la consulta.
     * @param WP_REST_Request $request La petición actual.
     * @return array Argumentos modificados.
     */
    public function hide_from_rest_api( $args, $request ) {
        $page_id = get_option( 'wporlogin_page_id' );
        
        if ( $page_id ) {
            // Inicializar post__not_in si no existe
            if ( ! isset( $args['post__not_in'] ) || ! is_array( $args['post__not_in'] ) ) {
                $args['post__not_in'] = array();
            }
            
            // Agregamos nuestro ID a la lista de excluidos
            $args['post__not_in'][] = (int) $page_id;
        }

        return $args;
    }

}