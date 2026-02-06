<?php
/**
 * Clase Maestra de Activos Públicos (Frontend & Simulación).
 * * Unifica la carga de CSS, JS (Slideshow) y la limpieza de estilos ajenos
 * tanto para la página de login real como para el personalizador.
 *
 * @package Wporlogin
 * @subpackage Wporlogin/public
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Wporlogin_Public_Assets {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Registra los hooks necesarios.
     * Se llama desde la clase principal (Wporlogin).
     */
    public function init_hooks( $loader ) {
        
        // 1. Carga de Activos (CSS y JS)
        // Hook para el Login Real
        $loader->add_action( 'login_enqueue_scripts', $this, 'enqueue_all_assets', 20 );
        // Hook para la Simulación (Frontend)
        $loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_all_assets', 20 );

        // 2. Limpieza Agresiva (Solo para Simulación)
        // Prioridad 9999 para ejecutarse al final de todo
        $loader->add_action( 'wp_enqueue_scripts', $this, 'clean_foreign_assets', 9999 );
    }

    /**
     * El Controlador Central de Carga.
     * Algoritmo inteligente basado en el contexto.
     */
    public function enqueue_all_assets() {

        $context = $this->get_current_context();

        // 1. Si no es ni login ni simulación, adiós.
        if ( $context === 'none' ) {
            return;
        }

        // ---------------------------------------------------------
        // A. CONTEXTO: SIMULACIÓN (Customizer / Live Preview)
        // ---------------------------------------------------------
        // Aquí "Assets" es responsable de TODO para recrear el entorno.
        if ( $context === 'simulation' ) {
            
            // 1. Dependencias de WordPress (necesarias porque el tema no las carga)
            wp_enqueue_style( 'dashicons' );
            wp_enqueue_style( 'buttons', includes_url( 'css/buttons.min.css' ) );
            wp_enqueue_style( 'forms', admin_url( 'css/forms.min.css' ) );
            wp_enqueue_style( 'l10n', admin_url( 'css/l10n.min.css' ) );
            wp_enqueue_style( 'login', admin_url( 'css/login.min.css' ) );

            // 2. CSS Personalizado (LO MANTENEMOS AQUÍ)
            // Es vital en la simulación porque class-design.php a veces no se ejecuta
            // en 'wp_enqueue_scripts', o queremos asegurar la carga inmediata.
            $this->enqueue_custom_css();

            // 3. Slideshow (JS)
            // Lo cargamos SIEMPRE en simulación para permitir "Live Preview"
            // si el usuario cambia de 'Estático' a 'Random' en tiempo real.
            $this->enqueue_slideshow_js();
            $this->enqueue_video_js(); // [NUEVO - VIDEO]
            
        } 
        // ---------------------------------------------------------
        // B. CONTEXTO: LOGIN REAL
        // ---------------------------------------------------------
        // Aquí "Assets" delega el CSS a "Design" y solo gestiona JS extra.
        else { 
            
            // 1. CSS Personalizado: NO LO CARGAMOS.
            // ¿Por qué? Porque class-wporlogin-public-design.php ya lo hace
            // en su función apply_design_styles(). Evitamos cargar el archivo dos veces.
            
            // 2. Slideshow (JS): Lógica estricta.
            // Solo lo cargamos si el diseño activo es el "Zero".
            $active_design = get_option( 'wporlogin_active_design', 'wporlogin_design_basic' );
            
            if ( $active_design === 'wporlogin_design_img_premium_zero' ) {
                $this->enqueue_slideshow_js();
                $this->enqueue_video_js(); // [NUEVO - VIDEO]
            }
        }
    }

    /**
     * Lógica para el CSS generado dinámicamente.
     */
    private function enqueue_custom_css() {
        $upload_dir = wp_upload_dir();
        // Usamos la constante si está definida, sino fallback
        $filename   = defined('Wporlogin::CUSTOM_CSS_FILENAME') ? Wporlogin::CUSTOM_CSS_FILENAME : 'wporlogin-style-design-premium-zero.css';
        
        $base_url = set_url_scheme( $upload_dir['baseurl'] ); 
        $css_url  = trailingslashit( $base_url ) . 'wporlogin/' . $filename;
        $css_file = trailingslashit( $upload_dir['basedir'] ) . 'wporlogin/' . $filename;

        if ( file_exists( $css_file ) ) {
            wp_enqueue_style(
                'wporlogin-custom-style', 
                $css_url,
                array( 'login' ), // Dependencia: Siempre después del login base
                filemtime( $css_file )
            );
        }
    }

    /**
     * Lógica para el JS del Slideshow (Fondo Aleatorio).
     */
    private function enqueue_slideshow_js() {
        
        // 1. Verificar modo (con compatibilidad legacy)
        $mode = get_option( 'wporlogin_background_mode' );
        if ( ! $mode ) {
            $legacy = get_option( 'wporlogin_enable_bg_image' );
            $mode = $legacy ? 'static' : 'disabled';
        }

        // Solo cargar si es Random
        if ( $mode === 'random' ) {
            $ids_str = get_option( 'wporlogin_random_images', '' );
            
            if ( empty( $ids_str ) ) return;

            // Procesar imágenes
            $images_urls = array();
            $ids = explode( ',', $ids_str );
            foreach ( $ids as $id ) {
                if ( is_numeric( $id ) ) {
                    $url = wp_get_attachment_image_url( $id, 'full' );
                    if ( $url ) $images_urls[] = $url;
                }
            }

            if ( ! empty( $images_urls ) ) {
                $duration = get_option( 'wporlogin_random_duration', 5 );

                // [NUEVO] Obtener datos del overlay
                $overlay_color = get_option( 'wporlogin_bg_overlay_color', '#000000' );
                $overlay_opacity = get_option( 'wporlogin_bg_overlay_opacity', '0' );
                $overlay_rgba = $this->get_rgba( $overlay_color, $overlay_opacity );

                wp_enqueue_script( 
                    'wporlogin-slideshow', 
                    WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-background-slideshow.js', 
                    array( 'jquery' ), 
                    $this->version, 
                    true 
                );
                
                wp_localize_script( 'wporlogin-slideshow', 'wporlogin_slideshow_params', array(
                    'images'    => $images_urls,
                    'duration'  => $duration,
                    'overlayRgba' => $overlay_rgba // Pasamos el color calculado
                ));
            }
        }
    }

    /**
     * [MODIFICADO] Encolar Motor JS con datos unificados.
     * PHP solo prepara la URL, JS hace la inserción.
     */
    private function enqueue_video_js() {
        
        $mode = get_option( 'wporlogin_background_mode', 'disabled' );

        if ( $mode === 'video' ) {
            
            // 1. Determinar Fuente y URL
            $source = get_option( 'wporlogin_video_source', 'external' );
            $video_url = '';

            if ( $source === 'local' ) {
                // Convertir ID a URL para que JS la entienda
                $video_id = get_option( 'wporlogin_video_local' );
                if ( $video_id ) {
                    $raw_url = wp_get_attachment_url( $video_id );

                    // [SOLUCIÓN] Forzar el esquema correcto (http o https) según la página actual
                    if ( $raw_url ) {
                        $video_url = set_url_scheme( $raw_url ); 
                    }
                }
            } else {
                // Fuente externa
                $video_url = get_option( 'wporlogin_video_external_url' );

                // Opcional: También es buena práctica limpiar la externa, 
                // aunque esc_url_raw ya hace trabajo, set_url_scheme asegura consistencia.
                if ( $video_url ) {
                    $video_url = set_url_scheme( $video_url );
                }
            }

            // 2. Si no hay URL válida, no cargamos nada (salvo en Customizer)
            if ( empty( $video_url ) && ! is_customize_preview() ) {
                return;
            }

            // 3. Preparar Datos Visuales
            $fallback_url    = get_option( 'wporlogin_video_fallback' );
            $overlay_color   = get_option( 'wporlogin_bg_overlay_color', '#000000' );
            $overlay_opacity = get_option( 'wporlogin_bg_overlay_opacity', '50' );
            $overlay_rgba    = $this->get_rgba( $overlay_color, $overlay_opacity );

            wp_enqueue_script( 
                'wporlogin-video-engine', 
                WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-background-video.js', 
                array( 'jquery' ), 
                $this->version, 
                true 
            );

            // 4. Enviar a JS (Aquí está la magia: enviamos 'type' y 'videoUrl' ya resuelto)
            wp_localize_script( 'wporlogin-video-engine', 'wporlogin_video_params', array(
                'type'         => $source, // 'local' o 'external'
                'videoUrl'     => $video_url,       
                'fallbackUrl'  => $fallback_url,    
                'overlayColor' => $overlay_rgba     
            ));
        }
    }

    // [NUEVO] Helper para calcular RGBA en PHP
    private function get_rgba( $hex, $opacity ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) == 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        // Validación básica para evitar errores de hexdec vacío
        //if ( strlen( $hex ) !== 6 ) return 'rgba(0,0,0,0)';

        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        $a = intval( $opacity ) / 100;
        return "rgba({$r}, {$g}, {$b}, {$a})";
    }

    /**
     * Solo se ejecuta en la simulación.
     * * REVIEWER NOTE: This aggressive dequeuing is STRICTLY isolated to the 
     * plugin's own simulation page inside the Customizer preview.
     * It does NOT run on the frontend, login page, or admin dashboard.
     * See get_current_context() logic for the safeguard.
     */
    public function clean_foreign_assets() {
        
        // Solo ejecutar en simulación
        if ( $this->get_current_context() !== 'simulation' ) {
            return;
        }

        // Lista Blanca (Core de WP necesario)
        $core_allowed_scripts = array(
            'jquery', 'jquery-core', 'jquery-migrate', 'utils', 'underscore', 'wp-util',
            'customize-preview', 'customize-selective-refresh',
            'password-strength-meter', 'zxcvbn-async', 'user-profile',
            'wporlogin-slideshow', // ¡Importante! Permitir nuestro propio script
            'wporlogin-video-engine'    // [NUEVO - VIDEO] ¡Importante no borrarlo!
        );

        $core_allowed_styles = array(
            'dashicons', 'buttons', 'forms', 'l10n', 'login', 
            'customize-preview', 'admin-bar', 'wp-auth-check',
            'wporlogin-custom-style' // ¡Importante! Permitir nuestro estilo
        );

        global $wp_scripts, $wp_styles;

        // Limpiar Scripts
        if ( isset( $wp_scripts->queue ) ) {
            foreach ( $wp_scripts->queue as $handle ) {
                $is_core = in_array( $handle, $core_allowed_scripts );
                // Detectar cualquier script del plugin por prefijo
                $is_my_plugin = ( strpos( $handle, 'wporlogin-' ) === 0 );

                if ( ! $is_core && ! $is_my_plugin ) {
                    wp_dequeue_script( $handle );
                    wp_deregister_script( $handle );
                }
            }
        }

        // Limpiar Estilos
        if ( isset( $wp_styles->queue ) ) {
            foreach ( $wp_styles->queue as $handle ) {
                $is_core = in_array( $handle, $core_allowed_styles );
                $is_my_plugin = ( strpos( $handle, 'wporlogin-' ) === 0 );

                if ( ! $is_core && ! $is_my_plugin ) {
                    wp_dequeue_style( $handle );
                    wp_deregister_style( $handle );
                }
            }
        }
    }

    /**
     * Helper para saber dónde estamos.
     * @return string 'real_login', 'simulation', o 'none'.
     */
    private function get_current_context() {
        
        // 1. Es Login Real?
        if ( 'login_enqueue_scripts' === current_action() ) {
            return 'real_login';
        }

        // 2. Es Simulación del Customizer?
        if ( is_customize_preview() ) {
            $page_id = get_option('wporlogin_page_id');
            if ( $page_id && is_page( $page_id ) ) {
                return 'simulation';
            }
        }

        return 'none';
    }
}