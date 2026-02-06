<?php
/**
 * Clase que genera el archivo dynamic-login-style.css basado en las opciones del personalizador.
 *
 * @package WPORLogin\Customizer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Clase WPORLogin_Dynamic_CSS_Generator
 */
class WPORLogin_Dynamic_CSS_Generator {

    public static function init() {
        add_action( 'customize_save_after', array( __CLASS__, 'generate_dynamic_login_css' ) );
        add_action( 'update_option_remove_language_wporlogin', array( __CLASS__, 'generate_dynamic_login_css' ) );
    }

    public static function generate_dynamic_login_css() {
        $css_content = '';
        $css_content .= self::generate_background_styles();
        $css_content .= self::generate_authentication_styles();
        $css_content .= self::generate_form_styles();
        $css_content .= self::generate_input_fields_styles();
        $css_content .= self::generate_submit_button_styles();
        $css_content .= self::generate_links_styles();
        $css_content .= self::generate_language_switcher_styles();
        $css_content .= self::generate_privacy_styles();
        $css_content .= self::generate_messages_styles();
        $css_content .= self::generate_register_styles();
        $css_content .= self::generate_recovery_styles();
        $css_content .= self::generate_emailconfirm_styles();

        $upload_dir = wp_upload_dir();
        $css_dir = trailingslashit( $upload_dir['basedir'] ) . 'wporlogin/';

        if ( ! file_exists( $css_dir ) ) {
            wp_mkdir_p( $css_dir );
        }

        $css_file = $css_dir . Wporlogin::CUSTOM_CSS_FILENAME;
        file_put_contents( $css_file, $css_content );
    }

    /**
     * Genera los estilos CSS del fondo del login.
     *
     * Maneja la lógica de compatibilidad y los 3 modos de visualización:
     * 1. Static: Imagen fija con overlay (CSS puro).
     * 2. Random: Limpia el fondo para permitir que el JS muestre el slideshow detrás.
     * 3. Disabled: Muestra solo color sólido y overlay opcional.
     *
     * @return string Bloque CSS completo para body.login
     */
    private static function generate_background_styles() {
        
        // 1. Obtener Configuración Global
        $background_color = get_option( 'wporlogin_login_background_color', '#f0f0f1' );
        $overlay_color    = get_option( 'wporlogin_bg_overlay_color', '#000000' );
        $overlay_opacity  = get_option( 'wporlogin_bg_overlay_opacity', '0' );
        
        // Calcular RGBA para la sombra (Overlay)
        $rgba        = self::get_rgba_from_hex( $overlay_color, $overlay_opacity );

        // 2. Determinar el Modo (con compatibilidad para usuarios antiguos)
        $mode = get_option( 'wporlogin_background_mode', false );

        if ( $mode === false ) {
            // Si no existe la opción nueva, consultamos el checkbox antiguo (Migración al vuelo)
            $legacy_enabled = get_option( 'wporlogin_enable_bg_image', false );
            $mode = ( $legacy_enabled ) ? 'static' : 'disabled';
        }

        $css = "body.login {\n";        

        $gallery_image       = get_option( 'wporlogin_bg_gallery', '' );
        $custom_image        = get_option( 'wporlogin_login_background_image', '' );
        $background_repeat   = get_option( 'wporlogin_login_background_repeat', 'no-repeat' );
        $background_size     = get_option( 'wporlogin_login_background_size', 'cover' );
        $background_position = get_option( 'wporlogin_login_background_position', 'center center' );

        if ( $mode == "static"){

            $css .= "background-color: #000000;\n";

            $final_image_url = ( ! empty( $custom_image ) ) ? $custom_image : $gallery_image;

            if ( ! empty( $final_image_url ) ) {
                $css .= "background-image: linear-gradient({$rgba}, {$rgba}), url('" . esc_url( $final_image_url ) . "');\n";                
                $css .= "background-repeat: {$background_repeat};\n";
                $css .= "background-size: {$background_size};\n";
                $css .= "background-position: {$background_position};\n";
                $css .= "background-attachment: fixed;\n"; 
            } 
        } else if ( $mode == "disabled") {
            $css .= "background-color: {$background_color};\n";
            $css .= "background-image: linear-gradient({$rgba}, {$rgba});\n";
        } else {
            $css .= "background-color: #000000;\n";
            $css .= "background-image: none;\n";
        }

        $css .= "}\n\n";
        return $css;

    }

    private static function get_rgba_from_hex( $hex, $opacity_percent ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) == 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        $alpha = intval( $opacity_percent ) / 100;
        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }
    
    
    private static function generate_authentication_styles() {
        $bg_color       = get_option( 'wporlogin_auth_bg_color', '#f0f0f1' );
        $opacity        = get_option( 'wporlogin_auth_opacity', '1' );
        $border_radius  = get_option( 'wporlogin_auth_border_radius', '0px' );
        $padding_top    = get_option( 'wporlogin_auth_padding_top', '5%' );
        $padding_right  = get_option( 'wporlogin_auth_padding_right', '0px' );
        $padding_bottom = get_option( 'wporlogin_auth_padding_bottom', '0px' );
        $padding_left   = get_option( 'wporlogin_auth_padding_left', '0px' );
        $border_width   = get_option( 'wporlogin_auth_border_width', '0' );
        $border_style   = get_option( 'wporlogin_auth_border_style', 'solid' );
        $border_color   = get_option( 'wporlogin_auth_border_color', '#000000' );
        $center_content = get_option( 'wporlogin_auth_center_content', 'default' );
        $rgb = self::hex_to_rgb( $bg_color );

        $css  = "body.login #login {\n";
        $css .= "    background-color: rgba({$rgb}, {$opacity});\n";
        $css .= "    border-radius: {$border_radius};\n";
        $css .= "    padding-top: {$padding_top};\n";
        $css .= "    padding-right: {$padding_right};\n";
        $css .= "    padding-bottom: {$padding_bottom};\n";
        $css .= "    padding-left: {$padding_left};\n";
        $css .= "    border-width: {$border_width}px;\n";
        $css .= "    border-style: {$border_style};\n";
        $css .= "    border-color: {$border_color};\n";
        $css .= "}\n\n";

        if ( $center_content === 'center' ) {
            $css .= "body.login-action-login,\n";
            $css .= "body.login-action-register,\n";
            $css .= "body.login-action-lostpassword,\n";
            $css .= "body.login-action-confirm_admin_email {\n";
            $css .= "    display: flex;\n";
            $css .= "    flex-direction: column;\n";
            $css .= "    align-items: center;\n";
            $css .= "}\n\n";
            $css .= "body.login-action-confirm_admin_email #login {\n";
            $css .= "    margin-top: auto !important;\n";
            $css .= "}\n\n";
        }
        return $css;
    }

    private static function hex_to_rgb( $hex ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) === 3 ) {
            $hex = preg_replace( '/(.)/', '$1$1', $hex );
        }
        list( $r, $g, $b ) = sscanf( $hex, "%02x%02x%02x" );
        return "{$r}, {$g}, {$b}";
    }
    
    private static function generate_form_styles() {
        $logo_url              = esc_url_raw( get_option('wporlogin_logo_image', '') );
        $logo_visible          = get_option('wporlogin_logo_visible', 'block');
        $logo_width            = get_option('wporlogin_logo_width');
        $logo_height           = get_option('wporlogin_logo_height');
        
        if ( class_exists('Wporlogin_Helper') ) {
             $dimensions   = Wporlogin_Helper::get_smart_logo_dimensions( $logo_url, $logo_width, $logo_height );
             $logo_width  = $dimensions['width'];
             $logo_height = $dimensions['height'];
        }

        $logo_position         = get_option('wporlogin_logo_position', 'center top');
        $logo_background_size  = get_option('wporlogin_logo_background_size', 'contain');

        // 1. OBTENER DATOS DE FONDO
        $bg_color   = get_option('wporlogin_form_bg_color', '#ffffff');
        $opacity    = get_option('wporlogin_form_opacity', '1'); // [NUEVO]

        // 2. CALCULAR RGBA (Usando tu helper existente)
        // Nota: Asegúrate de que get_rgba_from_hex esté accesible (self:: o en Helper)
        $rgba_bg    = self::get_rgba_from_login_hex( $bg_color, $opacity );

        $box_shadow     = get_option( 'wporlogin_form_box_shadow', 'default' );
        $p_color = get_option('wporlogin_form_p_color', '#3c434a');        
        $border_width = get_option('wporlogin_form_border_width', '1');
        $border_style = get_option('wporlogin_form_border_style', 'solid');
        $border_color = get_option('wporlogin_form_border_color', '#c3c4c7');
        $border_radius = get_option('wporlogin_form_border_radius', '0px');
        $padding_left = get_option('wporlogin_form_padding_left', 24);
        $padding_top = get_option('wporlogin_form_padding_top', 26);
        $padding_right = get_option('wporlogin_form_padding_right', 24);
        $padding_bottom = get_option('wporlogin_form_padding_bottom', 26);
        $margin_top    = absint( get_option( 'wporlogin_form_margin_top', 24 ) );
        $margin_right  = absint( get_option( 'wporlogin_form_margin_right', 0 ) );
        $margin_bottom = absint( get_option( 'wporlogin_form_margin_bottom', 24 ) );
        $margin_left   = absint( get_option( 'wporlogin_form_margin_left', 0 ) );

        $css  = "body.login div#login form#loginform {\n";
        $css .= "    background-color: {$rgba_bg};\n";
        $css .= "    border-width: {$border_width}px;\n";
        $css .= "    border-style: {$border_style};\n";
        $css .= "    border-color: {$border_color};\n";
        $css .= "    border-radius: {$border_radius};\n";
        $css .= "    padding-left: {$padding_left}px;\n";
        $css .= "    padding-top: {$padding_top}px;\n";
        $css .= "    padding-right: {$padding_right}px;\n";
        $css .= "    padding-bottom: {$padding_bottom}px;\n";
        $css .= "    margin: {$margin_top}px {$margin_right}px {$margin_bottom}px {$margin_left}px;\n";
        $css .= ( $box_shadow === 'none' ) ? "    box-shadow: none;\n" : "    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);\n";
        $css .= "}\n\n";
        
        $css .= "body.login div#login h1 { display: {$logo_visible}; }\n";
        
        if ( $logo_url ) {
            $css .= "body.login div#login h1 a {\n";
            $css .= "    background-image: url('{$logo_url}');\n";
            $css .= "    background-repeat: no-repeat;\n";
            $css .= "    background-position: {$logo_position};\n";
            $css .= "    background-size: {$logo_background_size};\n";
            $css .= "    text-indent: -9999px;\n";
            $css .= "    display: {$logo_visible};\n";
            $css .= "    width: {$logo_width};\n";
            $css .= "    height: {$logo_height};\n";
            $css .= "}\n";
        }

        $css .= "body.login div#login form#loginform p,\n";
        $css .= "body.login div#login form#loginform div {\n";
        $css .= "    color: {$p_color};\n";
        $css .= "}\n\n";
        return $css;
    }
    
    private static function generate_input_fields_styles() {
        $text_color    = get_option('wporlogin_input_color', '#000000');
        $bg_color      = get_option('wporlogin_input_bgcolor', '#ffffff');
        $border_color  = get_option('wporlogin_input_color_borde', '#8c8f94');
        $border_radius = get_option('wporlogin_input_radio', '4px');

        $css  = "body.login div#login form#loginform input[type=\"text\"],\n";
        $css .= "body.login div#login form#loginform input[type=\"password\"] {\n";
        $css .= "    color: {$text_color};\n";
        $css .= "    background-color: {$bg_color};\n";
        $css .= "    border-color: {$border_color};\n";
        $css .= "    border-radius: {$border_radius};\n";
        $css .= "}\n\n";
        return $css;
    }

    private static function generate_submit_button_styles() {
        $button_width_config = get_option('wporlogin_submit_button_full_width', 'auto|content-box|0');
        $padding             = get_option('wporlogin_submit_button_padding', '0 12px');
        $font_size           = get_option('wporlogin_submit_button_font_size', '13px');
        $text_color          = get_option('wporlogin_submit_button_color', '#ffffff');
        $bg_color            = get_option('wporlogin_submit_button_bgcolor', '#135e96');
        $border_color        = get_option('wporlogin_submit_button_color_borde', '#135e96');
        $border_radius       = get_option('wporlogin_submit_button_radio', '3px');
        $styles = explode('|', $button_width_config);
        $width       = isset($styles[0]) ? $styles[0] : 'auto';
        $box_sizing  = isset($styles[1]) ? $styles[1] : 'content-box';

        $css  = "body.login div#login form#loginform input#wp-submit {\n";
        $css .= "    width: {$width};\n";
        $css .= "    box-sizing: {$box_sizing};\n";
        $css .= "    padding: {$padding};\n";
        $css .= "    font-size: {$font_size};\n";
        $css .= "    color: {$text_color};\n";
        $css .= "    background-color: {$bg_color};\n";
        $css .= "    border-color: {$border_color};\n";
        $css .= "    border-radius: {$border_radius};\n";
        $css .= "}\n\n";
        return $css;
    }

    private static function generate_links_styles() {
        $link_color = get_option('wporlogin_link_color', '#50575e');
        $link_align = get_option('wporlogin_link_align', 'left');
        $css  = "body.login div#login p#nav a, body.login div#login p#backtoblog a {\n";
        $css .= "    color: {$link_color};\n";
        $css .= "}\n\n";
        $css .= "body.login div#login p#nav, body.login div#login p#backtoblog {\n";
        $css .= "    text-align: {$link_align};\n";
        $css .= "}\n\n";
        return $css;
    }

    private static function generate_language_switcher_styles() {
        $is_hidden = get_option('remove_language_wporlogin', 0);
        $display = ( $is_hidden == 1 ) ? 'none' : 'block';
        $css  = ".language-switcher {\n";
        $css .= "    display: {$display};\n";
        $css .= "}\n\n";
        return $css;
    }

    private static function generate_privacy_styles() {
        $privacy_link_color = get_option('wporlogin_privacy_color', '#2271b1');
        $privacy_margin_top = get_option('wporlogin_privacy_margin_top', 39);
        $privacy_margin_bottom = get_option('wporlogin_privacy_margin_bottom', 26);
        $css  = "body.login div#login div#login_error p a,\n";
        $css .= "body.login div#login div#login_error ul li a,\n";
        $css .= "body.login div#login div.privacy-policy-page-link a.privacy-policy-link {\n";
        $css .= "    color: {$privacy_link_color};\n";
        $css .= "}\n\n";
        $css .= "body.login div#login div.privacy-policy-page-link {\n";
        $css .= "    margin-top: {$privacy_margin_top}px;\n";
        $css .= "    margin-bottom: {$privacy_margin_bottom}px;\n";
        $css .= "}\n\n";
        return $css;
    }
    
    private static function generate_messages_styles() {
        $error_visible             = get_option('wporlogin_error_visible', 'none');
        $border_left_color          = get_option('wporlogin_color_border_left_error', '#d63638');
        $background_color           = get_option('wporlogin_color_bg_error', '#ffffff');
        $text_color                 = get_option('wporlogin_color_parrafo_error', '#3c434a');
        $link_color                 = get_option('wporlogin_color_link_error', '#2271b1');
        $css  = "body.login div#login div#login_error {\n";
        $css .= "    display: {$error_visible};\n";
        $css .= "    border-left: 4px solid {$border_left_color};\n";
        $css .= "    background-color: {$background_color};\n";
        $css .= "}\n\n";
        $css .= "body.login div#login div#login_error p,\n";
        $css .= "body.login div#login div#login_error ul li {\n";
        $css .= "    color: {$text_color};\n";
        $css .= "}\n\n";
        $css .= "body.login div#login div#login_error p a,\n";
        $css .= "body.login div#login div#login_error ul li a {\n";
        $css .= "    color: {$link_color};\n";
        $css .= "}\n\n";
        return $css;
    }
    
    private static function generate_register_styles() {
        $bg_color                  = get_option('wporlogin_register_bg_color', '#ffffff');
        $opacity                    = get_option('wporlogin_register_opacity', '1');

        $border_color               = get_option('wporlogin_register_border_color', '#c3c4c7');
        $p_color                    = get_option('wporlogin_register_p_color', '#3c434a');
        $submit_button_color        = get_option('wporlogin_register_submit_button_color', '#ffffff');
        $submit_button_bgcolor      = get_option('wporlogin_register_submit_button_bgcolor', '#135e96');
        $submit_button_border_color = get_option('wporlogin_register_submit_button_color_borde', '#135e96');        
        $border_style               = get_option('wporlogin_register_border_style', 'solid');
        $border_radius              = get_option('wporlogin_register_radio', '0px');
        $border_width               = absint( get_option('wporlogin_register_border_width', 1) );
        $padding_left               = absint( get_option('wporlogin_register_padding_left', 24) );
        $padding_top                = absint( get_option('wporlogin_register_padding_top', 26) );
        $padding_right              = absint( get_option('wporlogin_register_padding_right', 24) );
        $padding_bottom             = absint( get_option('wporlogin_register_padding_bottom', 26) );

        $button_width_config        = get_option('wporlogin_register_submit_button_full_width', 'auto|content-box');
        // Prevenir error si explode falla
        $parts = explode('|', $button_width_config);
        $button_width = isset($parts[0]) ? $parts[0] : 'auto';
        $button_box_sizing = isset($parts[1]) ? $parts[1] : 'content-box';

        $button_padding             = get_option('wporlogin_register_submit_button_padding', '0 12px');
        $button_font_size           = get_option('wporlogin_register_submit_button_font_size', '13px');
        $button_border_radius       = get_option('wporlogin_register_submit_button_radio', '3px');

        // 2. CALCULAR EL COLOR RGBA (La Solución)
        // Usamos self:: porque el método es estático en la misma clase
        $rgba_bg = self::get_rgba_from_login_hex( $bg_color, $opacity );

        $css  = "body.login div#login form#registerform {\n";
        $css .= "    background-color: {$rgba_bg};\n";
        $css .= "    border-color: {$border_color};\n";
        $css .= "    border-style: {$border_style};\n";
        $css .= "    border-radius: {$border_radius};\n";
        $css .= "    border-width: {$border_width}px;\n";
        $css .= "    padding-left: {$padding_left}px;\n";
        $css .= "    padding-top: {$padding_top}px;\n";
        $css .= "    padding-right: {$padding_right}px;\n";
        $css .= "    padding-bottom: {$padding_bottom}px;\n";
        $css .= "}\n\n";

        $css .= "body.login div#login form#registerform p label,\n";
        $css .= "body.login div#login form#registerform p#reg_passmail {\n";
        $css .= "    color: {$p_color};\n";
        $css .= "}\n\n";

        $css .= "body.login div#login form#registerform p.submit input#wp-submit {\n";
        $css .= "    color: {$submit_button_color};\n";
        $css .= "    background-color: {$submit_button_bgcolor};\n";
        $css .= "    border-color: {$submit_button_border_color};\n";
        $css .= "    width: {$button_width};\n";
        $css .= "    box-sizing: {$button_box_sizing};\n";
        // Fix para centrar si es full width
        if ( $button_width === '100%' ) {
            $css .= "    float: none;\n";
            $css .= "    display: block;\n";
        }
        $css .= "    padding: {$button_padding};\n";
        $css .= "    font-size: {$button_font_size};\n";
        $css .= "    border-radius: {$button_border_radius};\n";
        $css .= "}\n\n";

        return $css;
    }

    /**
     * Convierte HEX a RGBA.
     * Helper reutilizable para cualquier parte del plugin.
     */
    private static function get_rgba_from_login_hex( $hex, $opacity ) {
        // Limpieza básica
        $hex = ltrim( $hex, '#' );
        
        // Si no hay color, devolver transparente
        if ( empty( $hex ) ) {
            return 'transparent';
        }

        // Soporte para HEX cortos (#FFF)
        if ( strlen( $hex ) == 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        // Convertir pares a decimales
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );

        // Asegurar que la opacidad sea un número válido
        // Si viene vacía, asumimos 1 (sólido)
        $alpha = ( is_numeric( $opacity ) ) ? $opacity : 1;

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }

    private static function generate_recovery_styles() {

        // 1. OBTENER VALORES
        $bg_color                   = get_option('wporlogin_recovery_bg_color', '#ffffff');
        $opacity                    = get_option('wporlogin_recovery_opacity', '1'); // Obtener opacidad

        $border_color              = get_option('wporlogin_recovery_border_color', '#c3c4c7');
        $p_color                   = get_option('wporlogin_recovery_p_color', '#3c434a');
        $submit_color              = get_option('wporlogin_recovery_submit_button_color', '#ffffff');
        $submit_bgcolor            = get_option('wporlogin_recovery_submit_button_bgcolor', '#135e96');
        $submit_border_color       = get_option('wporlogin_recovery_submit_button_color_borde', '#135e96');

        $border_style              = get_option('wporlogin_recovery_border_style', 'solid');
        $border_radius             = get_option('wporlogin_recovery_radio', '0px');
        $border_width              = absint( get_option('wporlogin_recovery_border_width', 1) );

        $padding_left              = absint( get_option('wporlogin_recovery_padding_left', 24) );
        $padding_top               = absint( get_option('wporlogin_recovery_padding_top', 26) );
        $padding_right             = absint( get_option('wporlogin_recovery_padding_right', 24) );
        $padding_bottom            = absint( get_option('wporlogin_recovery_padding_bottom', 26) );

        /*$button_width_config       = get_option('wporlogin_recovery_submit_button_full_width', 'auto|content-box');
        list($button_width, $button_box_sizing) = explode('|', $button_width_config);*/
        $button_width_config        = get_option('wporlogin_recovery_submit_button_full_width', 'auto|content-box');
        $parts = explode('|', $button_width_config);
        $button_width = isset($parts[0]) ? $parts[0] : 'auto';
        $button_box_sizing = isset($parts[1]) ? $parts[1] : 'content-box';

        $button_padding            = get_option('wporlogin_recovery_submit_button_padding', '0 12px');
        $button_font_size          = get_option('wporlogin_recovery_submit_button_font_size', '13px');
        $button_border_radius      = get_option('wporlogin_recovery_submit_button_radio', '3px');

        // 2. CALCULAR FONDO INTELIGENTE (RGBA)
        // Usamos el helper get_rgba_from_hex (asegúrate de que esté en la clase)
        $rgba_bg = self::get_rgba_from_login_hex( $bg_color, $opacity );

        // 3. GENERAR CSS
        $css  = "body.login div#login form#lostpasswordform {\n";
        $css .= "    background-color: {$rgba_bg};\n";
        $css .= "    border-color: {$border_color};\n";
        $css .= "    border-style: {$border_style};\n";
        $css .= "    border-radius: {$border_radius};\n";
        $css .= "    border-width: {$border_width}px;\n";
        $css .= "    padding-left: {$padding_left}px;\n";
        $css .= "    padding-top: {$padding_top}px;\n";
        $css .= "    padding-right: {$padding_right}px;\n";
        $css .= "    padding-bottom: {$padding_bottom}px;\n";
        $css .= "}\n\n";

        $css .= "body.login div#login form#lostpasswordform p label {\n";
        $css .= "    color: {$p_color};\n";
        $css .= "}\n\n";

        $css .= "body.login div#login form#lostpasswordform p.submit input#wp-submit {\n";
        $css .= "    color: {$submit_color};\n";
        $css .= "    background-color: {$submit_bgcolor};\n";
        $css .= "    border-color: {$submit_border_color};\n";
        $css .= "    width: {$button_width};\n";
        $css .= "    box-sizing: {$button_box_sizing};\n";
        // Fix visual para full width
        if ( $button_width === '100%' ) {
            $css .= "    float: none;\n";
            $css .= "    display: block;\n";
        }
        $css .= "    padding: {$button_padding};\n";
        $css .= "    font-size: {$button_font_size};\n";
        $css .= "    border-radius: {$button_border_radius};\n";
        $css .= "}\n\n";

        return $css;
    }
    
    private static function generate_emailconfirm_styles() {
        // 1. OBTENER VALORES
        $form_bg       = get_option('wporlogin_emailconfirm_bg_color', '#ffffff');
        $opacity       = get_option('wporlogin_emailconfirm_opacity', '1'); // Opacidad
        
        // 2. CALCULAR FONDO (RGBA Fix)
        // Usa el helper de la clase principal
        $rgba_bg       = self::get_rgba_from_login_hex( $form_bg, $opacity );

            $form_border       = get_option('wporlogin_emailconfirm_border_color', '#c3c4c7');
            $h1_color          = get_option('wporlogin_emailconfirm_h1_color', '#50575e');
            $p_color           = get_option('wporlogin_emailconfirm_p_color', '#3c434a');
            $link_color        = get_option('wporlogin_emailconfirm_link_color', '#135e96');

            $btn1_text         = get_option('wporlogin_emailconfirm_button_one_color', '#0a4b78');
            $btn1_bg           = get_option('wporlogin_emailconfirm_button_one_bgcolor', '#2271b1');
            $btn1_border       = get_option('wporlogin_emailconfirm_button_one_color_borde', '#0a4b78');

            $btn2_text         = get_option('wporlogin_emailconfirm_button_two_color', '#ffffff');
            $btn2_bg           = get_option('wporlogin_emailconfirm_button_two_bgcolor', '#135e96');
            $btn2_border       = get_option('wporlogin_emailconfirm_button_two_color_borde', '#135e96');

            $border_style      = get_option('wporlogin_emailconfirm_border_style', 'solid');
            $form_radius       = get_option('wporlogin_emailconfirm_radio', '0px');
            $btn1_radius       = get_option('wporlogin_emailconfirm_button_one_radio', '3px');
            $btn2_radius       = get_option('wporlogin_emailconfirm_button_two_radio', '3px');
            $border_width      = absint( get_option('wporlogin_emailconfirm_border_width', 1) );
            $padding_left      = absint( get_option('wporlogin_emailconfirm_padding_left', 24) );
            $padding_top       = absint( get_option('wporlogin_emailconfirm_padding_top', 26) );
            $padding_right     = absint( get_option('wporlogin_emailconfirm_padding_right', 24) );
            $padding_bottom    = absint( get_option('wporlogin_emailconfirm_padding_bottom', 26) );
            $h1_font_size      = get_option('wporlogin_emailconfirm_h1_size', '27px');

            $css  = "body.login div#login form.admin-email-confirm-form {\n";
            $css .= "    background-color: {$rgba_bg};\n";
            $css .= "    border-color: {$form_border};\n";
            $css .= "    border-style: {$border_style};\n";
            $css .= "    border-radius: {$form_radius};\n";
            $css .= "    border-width: {$border_width}px;\n";
            $css .= "    padding-left: {$padding_left}px;\n";
            $css .= "    padding-top: {$padding_top}px;\n";
            $css .= "    padding-right: {$padding_right}px;\n";
            $css .= "    padding-bottom: {$padding_bottom}px;\n";
            $css .= "}\n\n";

            $css .= "body.login div#login form.admin-email-confirm-form h1 {\n";
            $css .= "    color: {$h1_color};\n";
            $css .= "    font-size: {$h1_font_size};\n";
            $css .= "}\n\n";

            $css .= "body.login div#login form.admin-email-confirm-form p.admin-email__details {\n";
            $css .= "    color: {$p_color};\n";
            $css .= "}\n\n";

            $css .= "body.login div#login form.admin-email-confirm-form p.admin-email__details a,\n";
            $css .= "body.login div#login form.admin-email-confirm-form div.admin-email__actions-secondary a {\n";
            $css .= "    color: {$link_color};\n";
            $css .= "}\n\n";

            $css .= "body.login div#login form.admin-email-confirm-form div.admin-email__actions-primary a {\n";
            $css .= "    color: {$btn1_text};\n";
            $css .= "    background-color: {$btn1_bg};\n";
            $css .= "    border-color: {$btn1_border};\n";
            $css .= "    border-radius: {$btn1_radius};\n";
            $css .= "}\n\n";

            $css .= "body.login div#login form.admin-email-confirm-form div.admin-email__actions-primary input#correct-admin-email {\n";
            $css .= "    color: {$btn2_text};\n";
            $css .= "    background-color: {$btn2_bg};\n";
            $css .= "    border-color: {$btn2_border};\n";
            $css .= "    border-radius: {$btn2_radius};\n";
            $css .= "}\n\n";

            return $css;
    }

}