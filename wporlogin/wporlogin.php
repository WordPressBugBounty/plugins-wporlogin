<?php
/**
 * Plugin Name: WPOrLogin - Personalizar el inicio de sesión de WordPress
 * Plugin URI: https://oregoom.com/wporlogin/
 * Description: Plugin para personalizar la página de inicio de sesión de WordPress.
 * Version: 2.9.2
 * Author: Oregoom
 * Author URI: https://oregoom.com/wporlogin/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wporlogin
 * Domain Path: /languages/
 */
define("VERSIONWPORLOGIN", "2.9.2");

// Definición de las imágenes de fondo
define('WPORLOGINBACKGROUNDIMAGE', array(
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-0.jpg', 
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-1.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-2.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-3.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-4.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-5.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-6.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-7.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-8.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-9.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-10.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-11.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-12.jpg',
    plugin_dir_url( __FILE__ ).'img/wporlogin-img-fondo-13.jpg'
));

// Definición de constantes para URLs y paths del plugin
define( 'WPORLOGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPORLOGIN_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Inclusión de archivos necesarios
require_once plugin_dir_path(__FILE__) .'includes/wporloginpage.php';
require_once plugin_dir_path(__FILE__) .'includes/remove-language-wporlogin.php';

// Función para insertar scripts en el área de administración
function wporlogin_insert_script_upload($hook){

    //error_log($hook); // Esto enviará el valor del hook al archivo de registro de errores de WordPress

    // Verificar si estamos en la página de configuración de Google reCAPTCHA
    if ($hook == 'toplevel_page_wporlogin-plugin' || $hook == 'wporlogin_page_recaptcha-wporlogin-plugin' || $hook == 'wporlogin_page_remove-language-plugin') {
  
        wp_enqueue_media(); // Para abrir la biblioteca de medios
        
        // Registrar el script JS para la subida de imágenes de fondo
        wp_register_script('wporlogin_my_upload', plugin_dir_url( __FILE__ ).'js/img-fondo.js', array('jquery', 'wp-i18n'), VERSIONWPORLOGIN, true );
        wp_enqueue_script('wporlogin_my_upload'); // Cargar el script en el área de administración
        
        wp_set_script_translations('wporlogin_my_upload', 'wporlogin'); // Cargar traducciones

        // Registrar el script para avisos descartables
        wp_register_script('update_notice_wporlogin', plugins_url( 'js/update-notice.js', __FILE__ ), array('jquery', 'wp-i18n'), VERSIONWPORLOGIN, true);
        wp_enqueue_script('update_notice_wporlogin');

        // Parámetros localizados para el script de avisos
        wp_localize_script( 'update_notice_wporlogin', 'notice_params_wporlogin', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('my-ajax-nonce-wporlogin'),
            'action' => 'delete-notice-wp'
        ));

        // Registrar y cargar el script de selección de versión de reCAPTCHA solo en la página de configuración
        wp_register_script('wporlogin-recaptcha-version', plugin_dir_url(__FILE__) . 'js/wporlogin-recaptcha-version.js', array('jquery', 'wp-i18n'), VERSIONWPORLOGIN, true);
        wp_enqueue_script('wporlogin-recaptcha-version');
    }
    
} 
add_action("admin_enqueue_scripts", "wporlogin_insert_script_upload");






////////////////////////////////////////////////////////////////////////////////
// WP LOGIN: AGREGAR NUEVO LOGO EN LUGAR DE WORDPRESS
////////////////////////////////////////////////////////////////////////////////

function wporlogin_page_login() {

    ////////////////////////////////////////////////////////////////////////////
    // WP LOGIN: DISEÑO ESTÁNDAR Y PREMIUM (LOGO y IMAGEN DE FONDO)
    // Versión: 1.0
    ////////////////////////////////////////////////////////////////////////////
    
    // FUNCIÓN PARA OBTENER LA IMAGEN DE FONDO
    function wporlogin_url_img_fondo_css() {

        // COMPROBANDO SI LA VARIABLE NO ES NULA O VACÍA
        if ( get_option('wporlogin_background_images') ) {

            if (get_option('wporlogin_background_images') == 'wporlogin_free_images') {

                ////////////////////////////////////////////////////////////////////////////
                // WP LOGIN: VARIABLE DE IMAGEN DE FONDO PARA CSS
                ////////////////////////////////////////////////////////////////////////////
                ?>
                <style>
                    :root {
                        --wporlogin-img-fondo: url(<?php echo esc_url(get_option('wporlogin-background-free-image')); ?>); /*URL DE IMAGEN GRATIS*/
                    }
                </style>
                <?php

            } elseif (get_option('wporlogin_background_images') == 'wporlogin_my_images') {

                if (get_option('wporlogin_url_img_fondo')) { // URL DE LA IMAGEN PERSONALIZADA

                    ////////////////////////////////////////////////////////////////////////////
                    // WP LOGIN: VARIABLE DE IMAGEN DE FONDO PARA CSS
                    ////////////////////////////////////////////////////////////////////////////
                    ?>
                    <style>
                    :root {
                        --wporlogin-img-fondo: url(<?php echo esc_url(get_option('wporlogin_url_img_fondo')); ?>);
                    }
                    </style>
                    <?php

                }

            }

        }
    }



    // FUNCIÓN PARA OBTENER URL DE LOGOTIPO Y ANCHO/ALTO
// Pasarlo como variable a CSS en todos los diseños
function wporlogin_url_logo_css() {

    if (get_option("wporlogin_url_logotipo")) {

        ?><style>
        :root {
            --wporlogin-logo: url(<?php echo esc_url(get_option('wporlogin_url_logotipo')); ?>)!important;

            /*--wporlogin-width: 200px;*/
            <?php 
            if (get_option('wporlogin_width_logotipo_text')) {
                echo '--wporlogin-width: ' . intval(get_option('wporlogin_width_logotipo_text')) . 'px !important;';
            } else {
                echo '--wporlogin-width: 84px;';
            } ?>

            /*--wporlogin-height: 84px;*/
            <?php 
            if (get_option('wporlogin_height_logotipo_text')) {
                echo '--wporlogin-height: ' . intval(get_option('wporlogin_height_logotipo_text')) . 'px !important;';
            } else {
                echo '--wporlogin-height: 84px;';
            } ?>

            /* POSICIÓN DE FONDO */
            <?php 
            if (get_option('wporlogin_background_position_logotipo_select')) {
                switch (esc_html(get_option('wporlogin_background_position_logotipo_select'))) {
                    case 0:
                        echo '--background-position: left top !important;';
                        break;
                    case 1:
                        echo '--background-position: left center !important;';
                        break;
                    case 2:
                        echo '--background-position: left bottom !important;';
                        break;
                    case 3:
                        echo '--background-position: right top !important;';
                        break;
                    case 4:
                        echo '--background-position: right center !important;';
                        break;
                    case 5:
                        echo '--background-position: right bottom !important;';
                        break;
                    case 6:
                        echo '--background-position: center top !important;';
                        break;
                    case 7:
                        echo '--background-position: center center !important;';
                        break;
                    case 8:
                        echo '--background-position: center bottom !important;';
                        break;
                }
            } else {
                echo '--background-position: left top !important;';
            } ?>

            /* TAMAÑO DE FONDO */
            <?php 
            if (get_option('wporlogin_background_size_logotipo_select')) {
                switch (esc_html(get_option('wporlogin_background_size_logotipo_select'))) {
                    case 0:
                        echo '--background-size: inherit !important;';
                        break;
                    case 1:
                        echo '--background-size: cover !important;';
                        break;
                    case 2:
                        echo '--background-size: contain !important;';
                        break;
                }
            } else {
                echo '--background-size: inherit !important;';
            } ?>

        }
        </style><?php 

    } else {

        // OBTENER LOGOTIPO DEL TEMA
        $custom_logo_id = get_theme_mod('custom_logo');
        $image = wp_get_attachment_image_src($custom_logo_id, 'full');    

        // SI EL TEMA TIENE UN LOGOTIPO ASIGNADO
        if (has_custom_logo()) {
            ?><style>
            :root {
                --wporlogin-logo: url(<?php echo esc_url($image[0]); ?>)!important;
                --wporlogin-width: 200px;
                --wporlogin-height: auto;
                --background-size: 200px !important;
            }
            </style><?php
        } else {
            // LOGOTIPO POR DEFECTO DE WORDPRESS
            ?><style>
            :root {
                --wporlogin-logo: url(<?php echo esc_url(home_url('/wp-admin/images/wordpress-logo.svg?ver=20131107')); ?>);
                --wporlogin-width: 84px;
                --wporlogin-height: 84px;
                --background-size: 84px !important;
            }
            </style><?php
        }
    }
}




   // Función para eliminar o mantener el menú de idiomas en las páginas de login, registro y recuperación de contraseña.
function remove_language_wporlogin_css() {

    // Verifica si la opción 'remove_language_wporlogin' está activada
    if (get_option("remove_language_wporlogin") == 1) { 
        // CUANDO EL MENÚ DE IDIOMA ESTÁ ELIMINADO
        yes_recaptcha_active_login_and_register_wporlogin_style();
        yes_remove_language_wporlogin_style_lostpassword();
        yes_remove_language_wporlogin_style_login();

    } else { 
        // CUANDO EL MENÚ DE IDIOMA NO ESTÁ ELIMINADO

        not_remove_language_wporlogin_style_login();
        not_remove_language_wporlogin_style_register();
        not_remove_language_wporlogin_style_lostpassword();

        // Aplicar estilos de altura mínima cuando no se elimina el menú de idioma
        not_remove_language_wporlogin_style_login_min_height();
        not_remove_language_wporlogin_style_register_min_height();
        not_remove_language_wporlogin_style_lostpassword_min_height();
    }
}


    
    ////////////////////////////////////////////////////////////////////////////
// WP LOGIN: CONDICIÓN PARA APLICAR CSS A LA PÁGINA DE LOGIN
////////////////////////////////////////////////////////////////////////////

$design = get_option('wporlogin_design'); // Obtener el diseño seleccionado
$premium_design = get_option('wporlogin-design-img-premium'); // Obtener el diseño premium si está seleccionado

if ($design === 'wporlogin_design_basic') {

    // Aplicar configuraciones comunes como eliminar menú de idiomas
    remove_language_wporlogin_css();

    // Cargar el estilo básico
    wp_enqueue_style('wporlogin-style-design-basic', plugin_dir_url(__FILE__) . 'css/wporlogin-style-design-basic.css', array(), VERSIONWPORLOGIN, false);

} elseif ($design === 'wporlogin_design_standard') {

    // Aplicar configuraciones comunes
    wporlogin_url_img_fondo_css();
    wporlogin_url_logo_css();
    remove_language_wporlogin_css();

    // Cargar el estilo estándar
    wp_enqueue_style('wporlogin-style-design-standard', plugin_dir_url(__FILE__) . 'css/wporlogin-style-design-standard.css', array(), VERSIONWPORLOGIN, false);

} elseif ($design === 'wporlogin_design_premium') {

    // Verificar si el usuario ha seleccionado un diseño premium
    if ($premium_design) {

        // Aplicar configuraciones comunes
        wporlogin_url_img_fondo_css();
        wporlogin_url_logo_css();
        remove_language_wporlogin_css();

        // Determinar qué diseño premium se ha seleccionado y cargar el estilo correspondiente
        switch ($premium_design) {
            case 'wporlogin_design_img_premium_one':
                wp_enqueue_style('wporlogin-style-design-premium-one', plugin_dir_url(__FILE__) . 'css/wporlogin-style-design-premium-one.css', array(), VERSIONWPORLOGIN, false);
                break;

            case 'wporlogin_design_img_premium_two':
                wp_enqueue_style('wporlogin-style-design-premium-two', plugin_dir_url(__FILE__) . 'css/wporlogin-style-design-premium-two.css', array(), VERSIONWPORLOGIN, false);
                break;

            case 'wporlogin_design_img_premium_three':
                wp_enqueue_style('wporlogin-style-design-premium-three', plugin_dir_url(__FILE__) . 'css/wporlogin-style-design-premium-three.css', array(), VERSIONWPORLOGIN, false);
                break;

            default:
                // Si no hay diseño premium seleccionado, se podría cargar uno predeterminado o hacer algo diferente
                break;
        }
    }
}

    
    
///////////////////////////////////////////////////////////////////////////////
/////////// CÓDIGO PARA MANTENER COMPATIBLE CON LA VERSIÓN 2.8.6  /////////////
///////////////////////////////////////////////////////////////////////////////

// Recuperar el valor del checkbox de reCAPTCHA v2 (para compatibilidad con versiones anteriores)
$recaptcha_v2_enabled = get_option('recaptcha_v2_wporlogin', 0); // Si no está configurado, retorna 0

// Recuperar la versión de reCAPTCHA seleccionada (v2, v3, o ninguna)
$recaptcha_version = get_option('recaptcha_version_wporlogin', 'none');

// Si 'recaptcha_version_wporlogin' no está configurado y reCAPTCHA v2 está activado, usar v2 como versión por defecto
if ($recaptcha_version === 'none' && $recaptcha_v2_enabled == 1) {
    $recaptcha_version = 'v2';
    update_option('recaptcha_version_wporlogin', 'v2'); // Actualizar la opción a v2 para futuras referencias
}

//////////////////////////////////////////////////////////////////////////////
///////////////      FIN DEL CÓDIGO DE COMPROBACIÓN      /////////////////////
//////////////////////////////////////////////////////////////////////////////

// Manejo de la carga condicional de scripts para reCAPTCHA v2 y v3
if ($recaptcha_version === 'v2') {
    // Verificar si el usuario ha configurado correctamente las claves de reCAPTCHA v2
    $recaptcha_v2_site_key = esc_html(get_option('recaptcha_v2_site_key_wporlogin'));
    $recaptcha_v2_secret_key = esc_html(get_option('recaptcha_v2_secret_key_wporlogin'));

    // Si las claves están configuradas, proceder con la carga del script
    if (!empty($recaptcha_v2_site_key) && !empty($recaptcha_v2_secret_key)) {
        wp_register_script('recaptcha_v2', 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit', array(), VERSIONWPORLOGIN, true);
        wp_enqueue_script('recaptcha_v2');
    } else {
        // Si las claves no están configuradas, mostrar una advertencia en el backend
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>' . __('Google reCAPTCHA v2 está seleccionado, pero las claves del sitio o secretas no están configuradas.', 'wporlogin') . '</p></div>';
        });
    }
} elseif ($recaptcha_version === 'v3') {
    // Verificar si el usuario ha configurado correctamente las claves de reCAPTCHA v3
    $recaptcha_v3_site_key = esc_html(get_option('recaptcha_v3_site_key_wporlogin'));

    if (!empty($recaptcha_v3_site_key)) {
        wp_register_script('recaptcha_v3', 'https://www.google.com/recaptcha/api.js?render=' . $recaptcha_v3_site_key, array(), VERSIONWPORLOGIN, true);
        wp_enqueue_script('recaptcha_v3');
    } else {
        // Si las claves no están configuradas, mostrar una advertencia en el backend
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>' . __('Google reCAPTCHA v3 está seleccionado, pero la clave del sitio no está configurada.', 'wporlogin') . '</p></div>';
        });
    }
}
}
add_action('login_enqueue_scripts', 'wporlogin_page_login');








// define the login_footer callback 
function wporlogin_add_menu_login_footer() { 

    ///////////////////////////////////////////////////////////////////////////////////////
    //         WP LOGIN: Si el Diseño es Premium 
    ///////////////////////////////////////////////////////////////////////////////////////
        if (get_option('wporlogin_design') == 'wporlogin_design_premium') { ?>
    
            <div class="login_oregoom">
    
                <?php echo sprintf(__('Desarrollado por <a href="%s" target="_blank" class="text-info">Oregoom.com</a>', 'wporlogin'), esc_url('https://oregoom.com/wporlogin/')); ?>
    
            </div> <?php 
        }
    }
    //add_action( 'login_footer', 'wporlogin_add_menu_login_footer', 10, 2 ); 
    
    
    
    ///////////////////////////////////////////////////////////////////////////////////////
    //         WP LOGIN: CAMBIAR LA URL POR DEFECTO DEL LOGO - versión 1.0
    ///////////////////////////////////////////////////////////////////////////////////////
    
    function wporlogin_mostrar_ruta_url_logotipo( $url ) {
    
        if (get_option("wporlogin_ruta_url_logotipo")) {
    
            return esc_url(get_option('wporlogin_ruta_url_logotipo'));
    
        } else {
    
            if (get_option('wporlogin_design') == 'wporlogin_design_basic') {
    
                //return home_url();
                return get_bloginfo('url'); // Obtener la URL del sitio
    
            } else {
    
                return $url; // Retorna la URL por defecto
            }
        }
    }
    add_filter('login_headerurl', 'wporlogin_mostrar_ruta_url_logotipo', 10, 1);
    


    function wporlogin_mostrar_logotipo_title() {

        ////////////////////////////////////////////////////////////////////////////
        // WP LOGIN: CONDICIÓN PARA APLICAR CSS A PAGE LOGIN
        ////////////////////////////////////////////////////////////////////////////
    
        $wporlogin_design = get_option('wporlogin_design', false);
    
        if ($wporlogin_design && ($wporlogin_design === 'wporlogin_design_standard' || $wporlogin_design === 'wporlogin_design_premium')) {
            
            // Si el logotipo tiene un título personalizado
            if (get_option("wporlogin_titulo_logotipo")) {
                return esc_html(get_option('wporlogin_titulo_logotipo'));
            }
    
        } else {
            // Si es diseño básico o default, verifica la acción en la URL
            if (isset($_GET['action'])) {
                $wporlogin_action = sanitize_text_field($_GET['action']); // Sanitiza la entrada de acción
    
                if ($wporlogin_action === 'register') {
                    return __('Sign Up', 'wporlogin');
                } elseif ($wporlogin_action === 'lostpassword') {
                    return __('Reset Password', 'wporlogin');
                }
            }
    
            // Por defecto, muestra "Log In"
            return __('Log In', 'wporlogin');
        }
    }
    add_filter('login_headertext', 'wporlogin_mostrar_logotipo_title');

    

/**
 * Cargar traducciones de texto
 *
 * @since 1.0.0
 */
function wporlogin_textdomain() {
    load_plugin_textdomain('wporlogin', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'wporlogin_textdomain');





//Formulario reCaptcha de Google
//FORMULARIO DE LOGIN

add_action('login_form', 'wporlogin_add_captcha_to_login'); // Hook para el formulario de login
function wporlogin_add_captcha_to_login() {

    ///////////////////////////////////////////////////////////////////////////////
    /////////// CÓDIGO PARA MANTENER COMPATIBLE CON LA VERSIÓN 2.8.6  /////////////
    ///////////////////////////////////////////////////////////////////////////////

    // Recuperar el valor de 'recaptcha_v2_wporlogin' desde la base de datos, o asignar un valor por defecto (0 en este caso).
    $recaptcha_v2_enabled = get_option('recaptcha_v2_wporlogin', 0); // Si no existe la opción, devuelve 0.

    // Recupera el valor de 'recaptcha_version_wporlogin' desde la base de datos.
    $recaptcha_version = get_option('recaptcha_version_wporlogin', 'none'); // El valor por defecto será 'none' si no hay una opción guardada.

    // Si el valor de 'recaptcha_version_wporlogin' no está configurado, pero 'recaptcha_v2_wporlogin' está habilitado.
    if ($recaptcha_version === 'none' && $recaptcha_v2_enabled == 1) {
        $recaptcha_version = 'v2'; // Ajustar la versión como v2 si v2 está habilitado.
        update_option('recaptcha_version_wporlogin', 'v2'); // Actualizar la versión en la base de datos.
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///////////////      FIN DEL CÓDIGO DE COMPROBACIÓN      ///////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    // reCAPTCHA v2
    if ($recaptcha_version === 'v2' && get_option('recaptcha_v2_site_key_wporlogin') && get_option('recaptcha_v2_secret_key_wporlogin')) {
        // Verificar si reCAPTCHA v2 está habilitado para el formulario de acceso
        if (get_option('activa_acceso_recaptcha_v2_wporlogin') == 1) {
            $sitekey = get_option('recaptcha_v2_site_key_wporlogin'); ?>

            <script type="text/javascript">
                var onloadCallback = function() {
                    grecaptcha.render('wporlogin_html_element', {
                        'sitekey': '<?php echo $sitekey; ?>'
                    });
                };
            </script>

            <!-- Renderizar reCAPTCHA v2 -->
            <div id="wporlogin_html_element" style="margin-bottom: 10px;"></div>
            <?php
        }
    }

    // reCAPTCHA v3
    elseif ($recaptcha_version === 'v3' && get_option('recaptcha_v3_site_key_wporlogin') && get_option('recaptcha_v3_secret_key_wporlogin')) {
        // Verificar si reCAPTCHA v3 está habilitado para el formulario de acceso
        if (get_option('activa_acceso_recaptcha_v2_wporlogin') == 1) {

            // Renderiza el script de reCAPTCHA v3
            $sitekey_v3 = get_option('recaptcha_v3_site_key_wporlogin'); ?>
            <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_html($sitekey_v3); ?>"></script>
            <script>
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?php echo esc_html($sitekey_v3); ?>', {action: 'login'}).then(function(token) {
                        var recaptchaResponse = document.getElementById('wporlogin-g-recaptcha-response');
                        recaptchaResponse.value = token;
                    });
                });
            </script>
            <input type="hidden" name="wporlogin-g-recaptcha-response" id="wporlogin-g-recaptcha-response"> <?php
        }
    }
}


// Comprobando reCaptcha de Google
// FORMULARIO DE LOGIN
add_filter('wp_authenticate_user', 'wporlogin_captcha_login_check', 10, 2); // Hook para verificar el reCAPTCHA en el login

function wporlogin_captcha_login_check($user, $password) {

    // Recuperar la versión de reCAPTCHA y si reCAPTCHA v2 está habilitado.
    $recaptcha_version = get_option('recaptcha_version_wporlogin', 'none');
    $recaptcha_v2_enabled = get_option('recaptcha_v2_wporlogin', 0);

    // Si la versión no está configurada y v2 está habilitado, forzamos la versión v2
    if ($recaptcha_version === 'none' && $recaptcha_v2_enabled == 1) {
        $recaptcha_version = 'v2';
        update_option('recaptcha_version_wporlogin', 'v2');
    }

    // Si no se ha configurado una versión o si no está activado, retornamos el usuario sin hacer validación
    if ($recaptcha_version === 'none') {
        return $user;
    }

    // Verificar si reCAPTCHA está habilitado para el formulario de acceso
    if (get_option('activa_acceso_recaptcha_v2_wporlogin') != 1) {
        return $user; // Si no está activado, no hacemos validación
    }

    // Obtener la respuesta del formulario
    $recaptcha_response = isset($_POST['wporlogin-g-recaptcha-response']) ? sanitize_text_field($_POST['wporlogin-g-recaptcha-response']) : '';

    // Si no hay respuesta de reCAPTCHA, mostrar un mensaje de error
    if (empty($recaptcha_response)) {
        return new WP_Error('invalid_captcha', __('Por favor, completa la verificación de reCAPTCHA.', 'wporlogin'));
    }

    // Configuración de las URLs y claves de reCAPTCHA
    $recaptcha_verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $remote_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);

    // Preparación de la solicitud según la versión de reCAPTCHA
    $recaptcha_secret = '';
    $request_body = [];

    if ($recaptcha_version === 'v2') {
        $recaptcha_secret = get_option('recaptcha_v2_secret_key_wporlogin');
    } elseif ($recaptcha_version === 'v3') {
        $recaptcha_secret = get_option('recaptcha_v3_secret_key_wporlogin');
    }

    // Si no hay clave secreta, retornamos el usuario sin validación
    if (empty($recaptcha_secret)) {
        return $user;
    }

    // Cuerpo de la solicitud para la verificación de reCAPTCHA
    $request_body = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $remote_ip // Incluimos la IP del usuario para mayor seguridad
    ];

    // Hacer la solicitud a la API de Google reCAPTCHA
    $response = wp_remote_post($recaptcha_verify_url, [
        'body' => $request_body,
        'timeout' => 10 // Tiempo límite de la solicitud para evitar bloqueos largos
    ]);

    // Verificar si hubo un error en la solicitud HTTP
    if (is_wp_error($response)) {
        return new WP_Error('recaptcha_error', __('Error al verificar reCAPTCHA. Por favor, inténtalo de nuevo.', 'wporlogin'));
    }

    // Obtener el cuerpo de la respuesta y decodificar JSON
    $response_body = wp_remote_retrieve_body($response);
    $recaptcha_data = json_decode($response_body, true);

    // Verificar si la respuesta de reCAPTCHA es válida y si fue exitosa
    if (!isset($recaptcha_data['success']) || !$recaptcha_data['success']) {
        return new WP_Error('invalid_captcha', __('La verificación de reCAPTCHA ha fallado. Por favor, inténtalo de nuevo.', 'wporlogin'));
    }

    // Verificación adicional para reCAPTCHA v3 (basado en la puntuación)
    if ($recaptcha_version === 'v3' && isset($recaptcha_data['score'])) {
        $score = floatval($recaptcha_data['score']);
        if ($score < 0.5) { // Puedes ajustar este valor según el nivel de seguridad requerido
            return new WP_Error('low_recaptcha_score', __('La verificación de reCAPTCHA ha fallado. Por favor, inténtalo de nuevo.', 'wporlogin'));
        }
    }

    // Si todo es válido, retornamos el usuario sin errores
    return $user;
}


// Formulario de Registro WP
//
// Formulario reCaptcha de Google
// versión 2.5
add_action('register_form', 'wporlogin_add_captcha_to_register'); // Hook para el formulario de registro
function wporlogin_add_captcha_to_register() {

    ///////////////////////////////////////////////////////////////////////////////
    /////////// CÓDIGO PARA MANTENER COMPATIBLE CON LA VERSIÓN 2.8.6  /////////////
    ///////////////////////////////////////////////////////////////////////////////

    // Recuperar el valor de 'recaptcha_v2_wporlogin' desde la base de datos, o asignar un valor por defecto (0 en este caso).
    $recaptcha_v2_enabled = get_option('recaptcha_v2_wporlogin', 0); // Si no existe la opción, devuelve 0.

    // Recupera el valor de 'recaptcha_version_wporlogin' desde la base de datos.
    $recaptcha_version = get_option('recaptcha_version_wporlogin', 'none'); // El valor por defecto será 'none' si no hay una opción guardada.

    // Si el valor de 'recaptcha_version_wporlogin' no está configurado, pero 'recaptcha_v2_wporlogin' está habilitado.
    if ($recaptcha_version === 'none' && $recaptcha_v2_enabled == 1) {
        $recaptcha_version = 'v2'; // Ajustar la versión como v2 si v2 está habilitado.
        update_option('recaptcha_version_wporlogin', 'v2'); // Actualizar la versión en la base de datos.
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///////////////      FIN DEL CÓDIGO DE COMPROBACIÓN      ///////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    // reCAPTCHA v2
    if ($recaptcha_version === 'v2' && get_option('recaptcha_v2_site_key_wporlogin') && get_option('recaptcha_v2_secret_key_wporlogin')) {
        // Verificar si reCAPTCHA v2 está habilitado para el formulario de registro
        if (get_option('activa_registro_recaptcha_v2_wporlogin') == 1) {
            $sitekey = get_option('recaptcha_v2_site_key_wporlogin'); ?>

            <script type="text/javascript">
                var onloadCallback = function() {
                    grecaptcha.render('wporlogin_html_element_register', {
                        'sitekey': '<?php echo esc_html($sitekey); ?>'
                    });
                };
            </script>

            <!-- Renderizar reCAPTCHA v2 en el formulario de registro -->
            <div id="wporlogin_html_element_register" style="margin-bottom: 10px;"></div>
            <?php
        }
    }

    // reCAPTCHA v3
    elseif ($recaptcha_version === 'v3' && get_option('recaptcha_v3_site_key_wporlogin') && get_option('recaptcha_v3_secret_key_wporlogin')) {
        // Verificar si reCAPTCHA v3 está habilitado para el formulario de registro
        if (get_option('activa_registro_recaptcha_v2_wporlogin') == 1) {

            // Renderiza el script de reCAPTCHA v3
            $sitekey_v3 = get_option('recaptcha_v3_site_key_wporlogin'); ?>
            <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_html($sitekey_v3); ?>"></script>
            <script>
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?php echo esc_html($sitekey_v3); ?>', {action: 'register'}).then(function(token) {
                        var recaptchaResponse = document.getElementById('wporlogin-g-recaptcha-response-register');
                        recaptchaResponse.value = token;
                    });
                });
            </script>
            <input type="hidden" name="wporlogin-g-recaptcha-response" id="wporlogin-g-recaptcha-response-register"> 
            <?php
        }
    }
}



// Comprobando reCaptcha de Google
// FORMULARIO DE REGISTRO

add_filter('registration_errors', 'wporlogin_captcha_register_check', 10, 3); // Hook para verificar el reCAPTCHA en el registro

function wporlogin_captcha_register_check($errors, $sanitized_user_login, $user_email) {

    ///////////////////////////////////////////////////////////////////////////////
    /////////// CÓDIGO PARA MANTENER COMPATIBLE CON LA VERSIÓN 2.8.6  /////////////
    ///////////////////////////////////////////////////////////////////////////////

    // Recuperar la versión de reCAPTCHA y si reCAPTCHA v2 está habilitado.
    $recaptcha_version = get_option('recaptcha_version_wporlogin', 'none');
    $recaptcha_v2_enabled = get_option('recaptcha_v2_wporlogin', 0);

    // Si la versión no está configurada y v2 está habilitado, forzamos la versión v2
    if ($recaptcha_version === 'none' && $recaptcha_v2_enabled == 1) {
        $recaptcha_version = 'v2';
        update_option('recaptcha_version_wporlogin', 'v2');
    }

    // Si no se ha configurado una versión o si no está activado, retornamos los errores sin hacer validación
    if ($recaptcha_version === 'none') {
        return $errors;
    }

    // Verificar si reCAPTCHA está habilitado para el formulario de registro
    if (get_option('activa_registro_recaptcha_v2_wporlogin') != 1) {
        return $errors; // Si no está activado, no hacemos validación
    }

    // Obtener la respuesta del formulario
    $recaptcha_response = isset($_POST['wporlogin-g-recaptcha-response']) ? sanitize_text_field($_POST['wporlogin-g-recaptcha-response']) : '';

    // Si no hay respuesta de reCAPTCHA, agregar un error
    if (empty($recaptcha_response)) {
        $errors->add('invalid_captcha', __('Por favor, completa la verificación de reCAPTCHA.', 'wporlogin'));
        return $errors;
    }

    // Configuración de las URLs y claves de reCAPTCHA
    $recaptcha_verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $remote_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);

    // Preparación de la solicitud según la versión de reCAPTCHA
    $recaptcha_secret = '';
    $request_body = [];

    if ($recaptcha_version === 'v2') {
        $recaptcha_secret = get_option('recaptcha_v2_secret_key_wporlogin');
    } elseif ($recaptcha_version === 'v3') {
        $recaptcha_secret = get_option('recaptcha_v3_secret_key_wporlogin');
    }

    // Si no hay clave secreta, retornamos los errores sin validación
    if (empty($recaptcha_secret)) {
        return $errors;
    }

    // Cuerpo de la solicitud para la verificación de reCAPTCHA
    $request_body = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $remote_ip // Incluimos la IP del usuario para mayor seguridad
    ];

    // Hacer la solicitud a la API de Google reCAPTCHA
    $response = wp_remote_post($recaptcha_verify_url, [
        'body' => $request_body,
        'timeout' => 10 // Tiempo límite de la solicitud para evitar bloqueos largos
    ]);

    // Verificar si hubo un error en la solicitud HTTP
    if (is_wp_error($response)) {
        $errors->add('recaptcha_error', __('Error al verificar reCAPTCHA. Por favor, inténtalo de nuevo.', 'wporlogin'));
        return $errors;
    }

    // Obtener el cuerpo de la respuesta y decodificar JSON
    $response_body = wp_remote_retrieve_body($response);
    $recaptcha_data = json_decode($response_body, true);

    // Verificar si la respuesta de reCAPTCHA es válida y si fue exitosa
    if (!isset($recaptcha_data['success']) || !$recaptcha_data['success']) {
        $errors->add('invalid_captcha', __('La verificación de reCAPTCHA ha fallado. Por favor, inténtalo de nuevo.', 'wporlogin'));
        return $errors;
    }

    // Verificación adicional para reCAPTCHA v3 (basado en la puntuación)
    if ($recaptcha_version === 'v3' && isset($recaptcha_data['score'])) {
        $score = floatval($recaptcha_data['score']);
        if ($score < 0.5) { // Puedes ajustar este valor según el nivel de seguridad requerido
            $errors->add('low_recaptcha_score', __('La verificación de reCAPTCHA ha fallado. Por favor, inténtalo de nuevo.', 'wporlogin'));
            return $errors;
        }
    }

    // Si todo es válido, retornamos los errores sin cambios (es decir, sin errores de reCAPTCHA)
    return $errors;
}


// Mostrar reCAPTCHA en el formulario de recuperación de contraseña
add_action('lostpassword_form', 'wporlogin_add_recaptcha_to_password_reset_form');

function wporlogin_add_recaptcha_to_password_reset_form() {

    if (get_option('activa_recuperar_recaptcha_wporlogin') == 1) {
        $recaptcha_version = get_option('recaptcha_version_wporlogin', 'none');
        
        // Mostrar reCAPTCHA v2
        if ($recaptcha_version === 'v2' && get_option('recaptcha_v2_site_key_wporlogin')) {
            $sitekey = get_option('recaptcha_v2_site_key_wporlogin'); ?>
            <script type="text/javascript">
                var onloadCallback = function() {
                    grecaptcha.render('wporlogin_recaptcha_password_reset', {
                        'sitekey': '<?php echo esc_html($sitekey); ?>'
                    });
                };
            </script>
            <div id="wporlogin_recaptcha_password_reset" style="margin-bottom: 10px;"></div>
        <?php
        // Mostrar reCAPTCHA v3
        } elseif ($recaptcha_version === 'v3' && get_option('recaptcha_v3_site_key_wporlogin')) {
            $sitekey_v3 = get_option('recaptcha_v3_site_key_wporlogin'); ?>
            <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_html($sitekey_v3); ?>"></script>
            <script>
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?php echo esc_html($sitekey_v3); ?>', {action: 'password_reset'}).then(function(token) {
                        var recaptchaResponse = document.getElementById('wporlogin-g-recaptcha-response');
                        recaptchaResponse.value = token;
                    });
                });
            </script>
            <input type="hidden" name="wporlogin-g-recaptcha-response" id="wporlogin-g-recaptcha-response">
        <?php }
    }
}

// Validar reCAPTCHA en el formulario de recuperación de contraseña
add_action('lostpassword_post', 'wporlogin_validate_recaptcha_on_password_reset', 10, 1);

function wporlogin_validate_recaptcha_on_password_reset($validation_errors) {

    if (get_option('activa_recuperar_recaptcha_wporlogin') == 1) {
        $recaptcha_version = get_option('recaptcha_version_wporlogin', 'none');
        $recaptcha_response = isset($_POST['wporlogin-g-recaptcha-response']) ? sanitize_text_field($_POST['wporlogin-g-recaptcha-response']) : '';
        
        // Verificar si la respuesta de reCAPTCHA está vacía
        if (empty($recaptcha_response)) {
            $validation_errors->add('recaptcha_error', __('Por favor, completa la verificación de reCAPTCHA.', 'wporlogin'));
            return $validation_errors;
        }

        // Verificar la respuesta en Google
        $recaptcha_secret = '';
        if ($recaptcha_version === 'v2') {
            $recaptcha_secret = get_option('recaptcha_v2_secret_key_wporlogin');
        } elseif ($recaptcha_version === 'v3') {
            $recaptcha_secret = get_option('recaptcha_v3_secret_key_wporlogin');
        }

        if (empty($recaptcha_secret)) {
            $validation_errors->add('recaptcha_error', __('Error de configuración de reCAPTCHA.', 'wporlogin'));
            return $validation_errors;
        }

        // Hacer la solicitud de validación a Google
        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
            'body' => array(
                'secret' => $recaptcha_secret,
                'response' => $recaptcha_response,
                'remoteip' => sanitize_text_field($_SERVER['REMOTE_ADDR'])
            )
        ));

        // Verificar si hubo un error en la solicitud
        if (is_wp_error($response)) {
            $validation_errors->add('recaptcha_error', __('Error al verificar reCAPTCHA. Por favor, inténtalo de nuevo.', 'wporlogin'));
            return $validation_errors;
        }

        // Decodificar la respuesta JSON
        $response_body = wp_remote_retrieve_body($response);
        $recaptcha_data = json_decode($response_body);

        // Si la respuesta de reCAPTCHA no es válida, agregamos un error
        if (!$recaptcha_data || !$recaptcha_data->success) {
            $validation_errors->add('recaptcha_error', __('La verificación de reCAPTCHA ha fallado. Por favor, inténtalo de nuevo.', 'wporlogin'));
        }

        // Validar la puntuación de reCAPTCHA v3
        if ($recaptcha_version === 'v3' && isset($recaptcha_data->score)) {
            if ($recaptcha_data->score < 0.5) {
                $validation_errors->add('recaptcha_error', __('La puntuación de reCAPTCHA v3 es muy baja. Inténtalo de nuevo.', 'wporlogin'));
            }
        }
    }

    return $validation_errors;
}






function wporlogin_admin_notice__success() {

    $wporlogin_fecha_initial = get_option('wporlogin_date_5_review'); //Fecha Inicial - Instalación del Plugin WPOrLogin

    $wporlogin_date1_inicial = new DateTime($wporlogin_fecha_initial);
    $wporlogin_date2_actual = new DateTime();
    
    $wporlogin_diff = $wporlogin_date1_inicial->diff($wporlogin_date2_actual);

    // Calcular la diferencia en días entre la fecha de instalación y la fecha actual
    $number_days_plugin_wporlogin = $wporlogin_diff->days;

    // Mostrar notificación si han pasado más de 15 días
    if($number_days_plugin_wporlogin >= 15 ){ ?>

        <div class="notice notice-success is-dismissible jpum-notice delete_notice_wporlogin" style="padding-bottom: 10px;">

            <p>
                <a target="_blank" href="https://es.wordpress.org/plugins/wporlogin/" title="Plugin WPOrLogin">
                    <img class="logo" src="https://oregoom.com/wp-content/uploads/2022/01/icon-wporlogin.png" style="background-color: #fff; float: right; margin-left: 10px; width: 75px; padding: 0.25em; border: 1px solid #ccc;">
                </a>
                <strong>
                    <?php _e('¡Hola! Ya has estado usando el plugin WPOrLogin en tu sitio durante 2 semanas - Esperamos que te haya sido útil. Si estás disfrutando del plugin, ¿te importaría valorarlo con 5 estrellas para ayudar a hacer correr la voz?', 'wporlogin'); ?>
                </strong>
            </p>
            <ul>
                <li>
                    <a class="jpum-dismiss" target="_blank" href="https://wordpress.org/support/plugin/wporlogin/reviews/?filter=5" data-reason="am_now">
                        <strong><?php _e('Vale, te lo mereces', 'wporlogin'); ?></strong>
                    </a>
                </li>
            </ul>

        </div> <?php

    }        

}

// Mostrar el aviso si no se ha desactivado previamente
if( get_option('delete_notice_wporlogin_condition') != 1 ){
    add_action( 'admin_notices', 'wporlogin_admin_notice__success' );
}

// Acción AJAX para eliminar la notificación
add_action( 'wp_ajax_delete-notice-wp', 'delete_notice_wporlogin' );
function delete_notice_wporlogin() {

    // Sanitizar el valor del nonce
    $nonce_wporlogin_ajax = sanitize_text_field( $_POST['nonce'] );

    // Verificar el nonce para mayor seguridad
    if( wp_verify_nonce( $nonce_wporlogin_ajax, 'my-ajax-nonce-wporlogin') ){

        // Actualizar la opción en la base de datos para eliminar la notificación
        update_option( 'delete_notice_wporlogin_condition', '1' );
        _e('Excelente', 'wporlogin');

    } else {

        // Si falla la verificación del nonce, mostrar error
        die(__('Error de nonce', 'wporlogin'));

    }

    // Terminar el proceso de la solicitud AJAX
    wp_die();
}

// Mostrar aviso importante en el panel de administración
function wporlogin_admin_notice_warning(){   
    
    global $pagenow;

    // Mostrar el aviso si se ha actualizado la configuración en las páginas específicas del plugin
    if ( ( 'admin.php' === $pagenow && ( 'wporlogin-plugin' === $_GET['page'] || 'recaptcha-wporlogin-plugin' === $_GET['page'] || 'remove-language-plugin' === $_GET['page'] ) ) && isset( $_GET[ 'settings-updated' ] ) == true ) {
        ?>
        
        <div class="notice notice-warning">

            <p><strong><?php _e('IMPORTANTE: ', 'wporlogin'); ?></strong><?php _e('Si no puedes ver los cambios que has realizado en tu sitio web, puede que necesites forzar a la página a cargar desde el servidor con ', 'wporlogin'); ?><strong>Ctrl + f5</strong><?php _e(' o limpiar la caché de WordPress.', 'wporlogin'); ?></p>
            <p><a target="_blank" href="https://raiolanetworks.es/blog/borrar-cache-wordpress/" title="<?php _e('Borrar la caché de WordPress', 'wporlogin'); ?>"><?php _e('Más información aquí', 'wporlogin'); ?></a></p>

        </div> 
        <?php
    } 

}
add_action('admin_notices', 'wporlogin_admin_notice_warning' );

// Incluir el contenido del formulario reCAPTCHA
include(WPORLOGIN_PLUGIN_PATH . 'includes/templates/recaptcha-settings-template-form.php');
