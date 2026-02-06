<?php
/**
 * Maneja la lógica de visualización y plantillas del frontend.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/public
 */

class Wporlogin_Public_Template {

    /**
     * Intercepta la carga de plantillas de WordPress.
     * Si el usuario visita la página de login, carga el archivo del plugin correspondiente.
     *
     * @param string $template Ruta a la plantilla actual del tema.
     * @return string Ruta modificada a la plantilla.
     */
    public function load_custom_template( $template ) {
        
        $target_page_id = get_option( 'wporlogin_page_id' );

        // Verificamos si estamos en la página correcta
        if ( $target_page_id && is_page( $target_page_id ) ) {
            
            // 1. DETECTAR TIPO DE PLANTILLA
            // Si el personalizador pide 'register', 'recovery', etc., lo capturamos aquí.
            // Si no hay parámetro, por defecto es 'login'.
            $template_type = isset($_GET['template']) ? sanitize_key($_GET['template']) : 'login';

            // 2. CONSTRUIR LA RUTA DINÁMICA
            // Buscará: template-wporlogin-login.php, template-wporlogin-register.php, etc.
            $plugin_template = WPORLOGIN_PATH . 'templates/template-wporlogin-' . $template_type . '.php';

            // 3. VERIFICAR EXISTENCIA Y CARGAR
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            } else {
                // Fallback de seguridad: Si no existe la plantilla pedida (ej. no copiaste el archivo),
                // cargamos el login por defecto para que no dé error.
                $default_template = WPORLOGIN_PATH . 'templates/template-wporlogin-login.php';
                if ( file_exists( $default_template ) ) {
                    return $default_template;
                }
            }
        }

        return $template;
    }

    /**
     * Oculta la plantilla personalizada de los selectores del editor de páginas.
     *
     * @param array $post_templates Array de plantillas de página reconocidas por el tema.
     * @return array Array filtrado.
     */
    public function hide_from_editor( $post_templates ) {
        // Si tu plantilla tuviera un nombre de archivo específico reconocido por WP, lo quitaríamos aquí.
        // Por ahora, como inyectamos vía hook, esto es preventivo.
        return $post_templates;
    }
}