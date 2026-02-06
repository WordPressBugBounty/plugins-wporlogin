<?php

/**
 * Se encarga exclusivamente de la apariencia visual del login.
 *
 * Gestiona la inyección de CSS dinámico y la carga de hojas de estilo
 * para personalizar el formulario de acceso, registro y recuperación.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/public
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin_Public_Design {

    /**
     * El identificador único del plugin.
     *
     * @access private
     * @var    string    $plugin_name  El nombre (slug) del plugin.
     */
    private $plugin_name;

    /**
     * La versión actual del plugin.
     *
     * @access private
     * @var    string    $version      La versión actual del plugin.
     */
    private $version;

    /**
     * Inicializa la clase y asigna las propiedades principales.
     *
     * @param    string    $plugin_name    El nombre del plugin.
     * @param    string    $version        La versión del plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * ==============================================================
     * 1. HOOKS DE MODIFICACIÓN (Filtros de WP)
     * ==============================================================
     */

    /**
     * Elimina el selector de idioma.
     * COMPATIBILIDAD: Usamos la variable antigua 'remove_language_wporlogin'.
     */
    public function remove_language_dropdown() {
        // Recuperamos la opción histórica (1 = Ocultar)
        $is_hidden = get_option( 'remove_language_wporlogin', 0); 

        // Solo aplicamos el filtro destructivo (PHP) si NO estamos previsualizando.
        // Esto permite que en el Customizer el elemento exista para poder ocultarlo/mostrarlo con JS.
        if ( $is_hidden == 1 && ! is_customize_preview() ) {
            add_filter( 'login_display_language_dropdown', '__return_false' );
        }
    }

    /**
     * Personaliza el título del enlace del logotipo (Tooltip).
     *
     * Cambia el texto "Funciona con WordPress" por el título del sitio o uno personalizado.
     *
     * @since    1.0.0
     * @param    string    $title    Título original del enlace.
     * @return   string              Título modificado.
     */
    public function custom_login_header_title( $title ) {
        // [ACTUALIZADO] Usamos la nueva opción unificada
        $design = get_option( 'wporlogin_active_design', 'wporlogin_design_basic' );
        
        // Lógica: Si NO es básico (es decir, es Standard, Premium o Custom), permitimos título personalizado.
        if ( $design !== 'wporlogin_design_basic' ) {
            $custom_title = get_option( 'wporlogin_titulo_logotipo' );
            if ( $custom_title ) return esc_html( $custom_title );
        } else {
            // Comportamiento para Básico (o por defecto si no hay título custom)
            if ( isset( $_GET['action'] ) ) {
                $action = sanitize_text_field( $_GET['action'] );
                if ( $action === 'register' )     return __( 'Sign Up', 'wporlogin' );
                if ( $action === 'lostpassword' ) return __( 'Reset Password', 'wporlogin' );
            }
            return __( 'Log In', 'wporlogin' );
        }
        return $title;
    }

    /**
     * Personaliza la URL del enlace del logotipo.
     *
     * Cambia el enlace de wordpress.org por la URL del sitio actual.
     *
     * @since    1.0.0
     * @param    string    $url    URL original del enlace.
     * @return   string            URL modificada.
     */
    public function custom_login_header_url( $url ) {
        $custom_url = get_option( 'wporlogin_ruta_url_logotipo' );
        if ( $custom_url ) return esc_url( $custom_url );

        // [ACTUALIZADO] Verificamos la nueva opción unificada
        $design = get_option( 'wporlogin_active_design', 'wporlogin_design_basic' );

        // Si es diseño básico, usamos la URL del sitio
        if ( $design === 'wporlogin_design_basic' ) {
            return get_bloginfo( 'url' );
        }
        return $url; 
    }

    /**
     * ==============================================================
     * 2. GENERADORES DE CSS (Inyección de estilos Inline)
     * ==============================================================
     */

    /**
     * Inyecta variables CSS con los colores de marca configurados.
     *
     * @since    2.0.0
     */
    public function inject_brand_colors() {
        $color_main   = get_option( 'wporlogin_color_principal_marca' );
        $color_hover  = get_option( 'wporlogin_color_hover_marca' );
        $color_text   = get_option( 'wporlogin_color_principal_marca_text_submit' );
        ?>
        <style>
        :root {
            --wporlogin-color-principal-marca: <?php echo esc_attr( $color_main ); ?> !important;
            --wporlogin-color-hover-marca: <?php echo esc_attr( $color_hover ); ?> !important;
            --wporlogin-color-principal-marca-text-submit: <?php echo esc_attr( $color_text ); ?>;
        }
        </style>
        <?php 
    }

    /**
     * Inyecta estilos CSS dinámicos para el logotipo.
     *
     * @since    2.0.0
     */
    public function inject_logo_styles() {
        
        $design = get_option( 'wporlogin_active_design', 'wporlogin_design_basic' );

        // Solo aplica para Standard y Premium (excluye Básico y Custom)
        if ( $design !== 'wporlogin_design_basic' && $design !== 'wporlogin_design_img_premium_zero' ) {

            // 1. URL del Logo
            $plugin_logo = get_option("wporlogin_url_logotipo");
            if ( $plugin_logo ) {
                $final_url = $plugin_logo;
            } elseif ( has_custom_logo() ) {
                $image = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
                $final_url = $image[0];
            } else {
                $final_url = home_url( '/wp-admin/images/wordpress-logo.svg' );
            }

            // 2. MAGIA: SOLO PASAMOS LA URL (Sin medidas manuales)
            // Al no pasarle medidas, el Helper usará su lógica inteligente (250px/90px)
            // ignorando si la imagen original es de 5000px.
            $dimensions = Wporlogin_Helper::get_smart_logo_dimensions( $final_url );

            // 3. Posición
            $pos_idx = get_option( 'wporlogin_background_position_logotipo_select', 7 );
            $pos_map = [ 0=>'left top', 1=>'left center', 2=>'left bottom', 3=>'right top', 4=>'right center', 5=>'right bottom', 6=>'center top', 7=>'center center', 8=>'center bottom' ];
            $bg_pos  = isset($pos_map[$pos_idx]) ? $pos_map[$pos_idx] : 'center center';

            ?>
            <style>
            :root {
                --wporlogin-logo: url('<?php echo esc_url( $final_url ); ?>') !important;
                --wporlogin-width: <?php echo $dimensions['width']; ?> !important;
                --wporlogin-height: <?php echo $dimensions['height']; ?> !important;
                --background-position: <?php echo $bg_pos; ?> !important;
                --background-size: contain !important;
            }
            
            body.login div#login h1 a {
                width: var(--wporlogin-width);
                height: var(--wporlogin-height);
                background-size: var(--background-size);
                background-position: var(--background-position);
                margin: 0 auto 25px auto;
                display: block;
            }
            </style>
            <?php 
        }
    }

    /**
     * Inyecta la imagen de fondo personalizada.
     *
     * @since    2.0.0
     */
    public function inject_background_url() {
        // [ACTUALIZADO] Recuperamos la opción unificada
        $design = get_option( 'wporlogin_active_design', 'wporlogin_design_basic' );

        // Solo aplicamos fondo si NO es el diseño Básico.
        if ( $design !== 'wporlogin_design_basic' ) {
            $type = get_option( 'wporlogin_background_images' );
            $url  = false;

            if ( $type == 'wporlogin_free_images' ) {
                $url = get_option( 'wporlogin-background-free-image' );
            } elseif ( $type == 'wporlogin_my_images' ) {
                $url = get_option( 'wporlogin_url_img_fondo' );
            }

            if ( $url ) {
                // --- CORRECCIÓN VISUAL ---
                if ( strpos( $url, '/plugins/wporlogin/img/' ) !== false ) {
                    $url = str_replace( '/plugins/wporlogin/img/', '/plugins/wporlogin/assets/img/', $url );
                }
                
                echo "<style>:root { --wporlogin-img-fondo: url('" . esc_url( $url ) . "'); }</style>";
            }
        }
    }

    /**
     * ==============================================================
     * 3. ORQUESTADOR DE DISEÑO (Main Loader)
     * ==============================================================
     */

    /**
     * Aplica los estilos CSS correctos según el diseño seleccionado.
     * Centraliza la carga tanto de estilos estáticos (Assets) como dinámicos (Uploads).
     *
     * @since    2.0.0
     */
    public function apply_design_styles() {
        
        // 1. Obtener diseño activo
        $design = get_option( 'wporlogin_active_design', 'wporlogin_design_basic' ); 
        
        // Siempre quitamos el selector de idioma si está configurado
        $this->remove_language_dropdown();

        // -----------------------------------------------------------
        // CASO A: DISEÑO CUSTOM (ZERO) - Dinámico
        // -----------------------------------------------------------
        // Este es especial. No lo buscamos en el array de abajo porque 
        // vive en una carpeta diferente (uploads) y su nombre viene de la constante.
        if ( $design === 'wporlogin_design_img_premium_zero' ) {
            
            $upload_dir = wp_upload_dir();
            
            // ¡AQUÍ ESTÁ LA MAGIA DE POO! 
            // Usamos la constante que definimos en la clase principal.
            // Si cambias el nombre en class-wporlogin.php, se actualiza aquí solo.
            $filename = Wporlogin::CUSTOM_CSS_FILENAME; 

            // 1. Aseguramos que la URL use el protocolo correcto (http o https) según detecte WordPress
            $base_url = set_url_scheme( $upload_dir['baseurl'] ); 

            // 2. Construimos la URL final
            $css_url  = trailingslashit( $base_url ) . 'wporlogin/' . $filename;
            $css_file = trailingslashit( $upload_dir['basedir'] ) . 'wporlogin/' . $filename;

            // Verificamos si existe antes de cargarlo para evitar errores 404
            if ( file_exists( $css_file ) ) {
                wp_enqueue_style( 
                    'wporlogin-custom-style',   // ID único
                    $css_url, 
                    array(), 
                    filemtime( $css_file )      // Versionado automático (cache busting)
                );
            }
            return; // Terminamos aquí. No seguimos ejecutando código innecesario.
        }

        // -----------------------------------------------------------
        // CASO B: DISEÑOS ESTÁTICOS (Predefinidos)
        // -----------------------------------------------------------
        // Estos archivos viven dentro de tu plugin en /assets/css/
        // Nota: Quitamos el 'Zero' de aquí porque ya lo manejamos arriba.
        $static_styles_map = [
            'wporlogin_design_basic'             => 'wporlogin-style-design-basic.css',
            'wporlogin_design_standard'          => 'wporlogin-style-design-standard.css',
            'wporlogin_design_img_premium_one'   => 'wporlogin-style-design-premium-one.css',
            'wporlogin_design_img_premium_two'   => 'wporlogin-style-design-premium-two.css',
            'wporlogin_design_img_premium_three' => 'wporlogin-style-design-premium-three.css',
            'wporlogin_design_img_premium_four' => 'wporlogin-style-design-premium-four.css',
            'wporlogin_design_img_premium_five' => 'wporlogin-style-design-premium-five.css',
            'wporlogin_design_img_premium_six' => 'wporlogin-style-design-premium-six.css'
        ];

        // Si el diseño seleccionado está en la lista estática, lo cargamos
        if ( array_key_exists( $design, $static_styles_map ) ) {
            wp_enqueue_style( 
                'wporlogin-static-style', 
                WPORLOGIN_URL . 'assets/css/' . $static_styles_map[$design], 
                array(), 
                $this->version 
            );
        }
    }
}