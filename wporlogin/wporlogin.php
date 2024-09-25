<?php
/**
* Plugin Name: WPOrLogin - Customize WordPress login
* Plugin URI: https://oregoom.com/wporlogin/
* Description: Plugin to customize the WordPress login.
* Version: 2.8.6
* Author: Oregoom
* Author URI:https://oregoom.com/wporlogin/
* License: GPL2
* Text Domain: wporlogin
* Domain Path: /languages/
*/	
define("VERSIONWPORLOGIN", "2.8.6");



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

require_once plugin_dir_path(__FILE__) .'includes/wporloginpage.php';
require_once plugin_dir_path(__FILE__) .'includes/remove-language-wporlogin.php';


function wporlogin_insert_script_upload(){
    
    wp_enqueue_media();//PARA ABRIR LA BIBLIOTEMA MEDIOS
    
    //REGISTRAR EL SCRIPT JS
    wp_register_script('wporlogin_my_upload', plugin_dir_url( __FILE__ ).'js/img-fondo.js', array('jquery', 'wp-i18n'), VERSIONWPORLOGIN, true );
    wp_enqueue_script('wporlogin_my_upload'); //MOSTRAR EL SCRIPT JS EN ADMIN
    
    wp_set_script_translations('wporlogin_my_upload', 'wporlogin');//Cargar Translation

    //Avisos descartables
    wp_register_script('update_notice_wporlogin', plugins_url() . '/wporlogin/js/update-notice.js', array('jquery'), VERSIONWPORLOGIN, true);
    wp_enqueue_script('update_notice_wporlogin');

    wp_localize_script( 'update_notice_wporlogin', 'notice_params_wporlogin', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('my-ajax-nonce-wporlogin'),
        'action' => 'delete-notice-wp'
    ));
    
} 
add_action("admin_enqueue_scripts", "wporlogin_insert_script_upload");





////////////////////////////////////////////////////////////////////////////////
// WP LOGIN: AGREGAR NUEVO LOGO EN LUGAR DE WORDPRESS
////////////////////////////////////////////////////////////////////////////////

function wporlogin_page_login() {

    ////////////////////////////////////////////////////////////////////////////
    //              WP LOGIN: DISEÑO ESTANDAR AND PREMIUM (LOGO and BACKGROUND-IMAGE)
    //              Version: 1.0
    ////////////////////////////////////////////////////////////////////////////
    

    //FUNCION PARA OPTENER IMG DE FONDO
    function wporlogin_url_img_fondo_css(){

        //COMPROBANDO SI LA VARIABLE ES NULO O VACIO
        if( get_option('wporlogin_background_images') ){

            if(get_option('wporlogin_background_images') == 'wporlogin_free_images'){

                ////////////////////////////////////////////////////////////////////////////
                //           WP LOGIN: VARIABLE DE IMAGEN DE FONDO PARA CSS
                ////////////////////////////////////////////////////////////////////////////
                ?> <style>
                    :root{
                        --wporlogin-img-fondo: url(<?php echo esc_html(get_option('wporlogin-background-free-image')); ?>); /*URL DE IMAGEN GRATIS*/
                    }
                </style> <?php

            } elseif ( get_option('wporlogin_background_images') == 'wporlogin_my_images' ){

                if(get_option('wporlogin_url_img_fondo')){ //URL DEL MY IMAGEN

                    ////////////////////////////////////////////////////////////////////////////
                    //           WP LOGIN: VARIABLE DE IMAGEN DE FONDO PARA CSS
                    ////////////////////////////////////////////////////////////////////////////
                    ?> <style>
                    :root{
                        --wporlogin-img-fondo: url(<?php echo esc_html(get_option("wporlogin_url_img_fondo")); ?>);
                    }
                    </style> <?php

                }

            }

        }
    }


    //FUNCION PARA OPTENER URl DE LOGOTIPO Y ANCHO/ALTO
    // Pasarlo como variable a css, a todos los diseños
    function wporlogin_url_logo_css(){

        if(get_option("wporlogin_url_logotipo")){

            ?><style>
            :root{

                --wporlogin-logo: url(<?php echo esc_url(get_option('wporlogin_url_logotipo')); ?>)!important;

                /*--wporlogin-width: 200px;*/
                <?php if(get_option('wporlogin_width_logotipo_text')){
                    echo '--wporlogin-width: ' . esc_html(get_option('wporlogin_width_logotipo_text')) . 'px !important;';
                } else {
                    echo '--wporlogin-width: 84px;';
                } ?>

                /*--wporlogin-height: 84px;*/
                <?php if(get_option('wporlogin_height_logotipo_text')){
                    echo '--wporlogin-height: ' . esc_html(get_option('wporlogin_height_logotipo_text')) . 'px !important;';
                } else {
                    echo '--wporlogin-height: 84px;';
                } ?>

                /*BACKGROUND POSITION*/
                <?php if(get_option('wporlogin_background_position_logotipo_select')){

                    switch(esc_html(get_option('wporlogin_background_position_logotipo_select'))){
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

                /*BACKGROUND SIZE*/
                <?php if(get_option('wporlogin_background_size_logotipo_select')){

                    switch(esc_html(get_option('wporlogin_background_size_logotipo_select'))){
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
    
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );    
            /*
                *  CONDICIÓN PARA SABER SI EL TEMA YA LO TIENE
                *  ASIGNADO UN LOGO
                * 
                *  SI ES VERDAD, ENTONCES MUESTRA EL LOGO ASIGNADO
                *  SI ES FALSO, MUESTRA EL LOGO DE WORDPRESS
                */
            if( has_custom_logo() ){
                ?><style>
                :root{
                    --wporlogin-logo: url(<?php echo esc_url( $image[0] ); ?>)!important;
                    --wporlogin-width: 200px;
                    --wporlogin-height: auto;
                    --background-size: 200px !important;
                }                    
                </style><?php
            } else {
                ?><style>
                :root{
                    --wporlogin-logo: url(<?php echo home_url( '/wp-admin/images/wordpress-logo.svg?ver=20131107' ); ?>);
                    --wporlogin-width: 84px;
                    --wporlogin-height: 84px;
                    --background-size: 84px !important;
                }                    
                </style><?php
            }
        }

    }



    //remove language
    function remove_language_wporlogin_css(){

        if( get_option("remove_language_wporlogin") == 1 ){ //CUANDO MENÚ LANGUAJE ELIMINADO

            //function yes_recaptcha_active_login_and_register_wporlogin_style() -> includes/remove-language-wporlogin.php
            yes_recaptcha_active_login_and_register_wporlogin_style();
            yes_remove_language_wporlogin_style_lostpassword();
            yes_remove_language_wporlogin_style_login();

        } else { //CUANDO MENÚ LANGUAJE "NO" ELIMINADO

            //function not_remove_language_wporlogin_style() -> includes/remove-language-wporlogin.php
            not_remove_language_wporlogin_style_login();
            not_remove_language_wporlogin_style_register();
            not_remove_language_wporlogin_style_lostpassword();

            not_remove_language_wporlogin_style_login_min_height();
            not_remove_language_wporlogin_style_register_min_height();
            not_remove_language_wporlogin_style_lostpassword_min_height();

        }

    }

    
    ////////////////////////////////////////////////////////////////////////////
    // WP LOGIN: CONDICIÓN PARA APLICAR CSS A PAGE LOGIN
    ////////////////////////////////////////////////////////////////////////////

    if (get_option('wporlogin_design') == 'wporlogin_design_basic'){

        //function remove_language_wporlogin_css()
        remove_language_wporlogin_css();

        wp_enqueue_style( 'wporlogin-style-design-basic', plugin_dir_url( __FILE__ ). 'css/wporlogin-style-design-basic.css', array(), VERSIONWPORLOGIN, false );

    } elseif (get_option('wporlogin_design') == 'wporlogin_design_standard'){

        //function wporlogin_url_img_fondo_css()
        wporlogin_url_img_fondo_css();
        //function wporlogin_url_logo_css()
        wporlogin_url_logo_css();
        //function remove_language_wporlogin_css()
        remove_language_wporlogin_css();

        wp_enqueue_style( 'wporlogin-style-design-standard', plugin_dir_url( __FILE__ ). 'css/wporlogin-style-design-standard.css', array(), VERSIONWPORLOGIN, false );
        
    } elseif (get_option('wporlogin_design') == 'wporlogin_design_premium'){

        if(get_option('wporlogin-design-img-premium')) {

            if(get_option('wporlogin-design-img-premium') == 'wporlogin_design_img_premium_one'){

                //function wporlogin_url_img_fondo_css()
                wporlogin_url_img_fondo_css();
                //function wporlogin_url_logo_css()
                wporlogin_url_logo_css();
                //function remove_language_wporlogin_css()
                remove_language_wporlogin_css();

                wp_enqueue_style( 'wporlogin-style-design-premium-one', plugin_dir_url( __FILE__ ). 'css/wporlogin-style-design-premium-one.css', array(), VERSIONWPORLOGIN, false );

            } elseif (get_option('wporlogin-design-img-premium') == 'wporlogin_design_img_premium_two'){

                //function wporlogin_url_img_fondo_css()
                wporlogin_url_img_fondo_css();
                //function wporlogin_url_logo_css()
                wporlogin_url_logo_css();
                //function remove_language_wporlogin_css()
                remove_language_wporlogin_css();

                wp_enqueue_style( 'wporlogin-style-design-premium-two', plugin_dir_url( __FILE__ ). 'css/wporlogin-style-design-premium-two.css', array(), VERSIONWPORLOGIN, false );

            } elseif (get_option('wporlogin-design-img-premium') == 'wporlogin_design_img_premium_three'){

                //function wporlogin_url_img_fondo_css()
                wporlogin_url_img_fondo_css();
                //function wporlogin_url_logo_css()
                wporlogin_url_logo_css();
                //function remove_language_wporlogin_css()
                remove_language_wporlogin_css();

                wp_enqueue_style( 'wporlogin-style-design-premium-three', plugin_dir_url( __FILE__ ). 'css/wporlogin-style-design-premium-three.css', array(), VERSIONWPORLOGIN, false );

            }

        }
        
    }  
    

    //Cuando esta activo Google reCAPTCHA
    if(get_option( 'recaptcha_v2_wporlogin' ) == 1){
        
        //script de ReCaptcha de Google
        //version 2.5
        wp_register_script('recaptcha_login', 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit');
        wp_enqueue_script('recaptcha_login');

    }



    
}
add_action('login_enqueue_scripts', 'wporlogin_page_login');




// define the login_footer callback 
function wporlogin_add_menu_login_footer() { 

///////////////////////////////////////////////////////////////////////////////////////
//         WP LOGIN: Si el Diseño es Premium 
///////////////////////////////////////////////////////////////////////////////////////
    if(get_option('wporlogin_design') == 'wporlogin_design_premium'){ ?>

        <div class="login_oregoom">

            Desarrollado por <a href="https://oregoom.com/wporlogin/" target="_black" class="text-info">Oregoom.com</a>

        </div> <?php 
    
    }

}
add_action( 'login_footer', 'wporlogin_add_menu_login_footer', 10, 2 ); 





///////////////////////////////////////////////////////////////////////////////////////
//         WP LOGIN: CAMBIAR LA URL POR DEFECTO DEL LOGO - versión 1.0
///////////////////////////////////////////////////////////////////////////////////////

function wporlogin_mostrar_ruta_url_logotipo( $url ) {

    if(get_option("wporlogin_ruta_url_logotipo") ){

        return esc_url(get_option('wporlogin_ruta_url_logotipo'));

    } else {

        if(get_option('wporlogin_design') == 'wporlogin_design_basic'){

            //return home_url();
            return get_bloginfo( 'url' );

        } else {

            return $url;

        }

    }

}
add_filter('login_headerurl', 'wporlogin_mostrar_ruta_url_logotipo', 10, 1); 






function wporlogin_mostrar_logotipo_title() {
    
    ////////////////////////////////////////////////////////////////////////////
    // WP LOGIN: CONDICIÓN PARA APLICAR CSS A PAGE LOGIN
    ////////////////////////////////////////////////////////////////////////////
    
    $wporlogin_design = get_option('wporlogin_design', false);
    
    if( ($wporlogin_design != false) && ( ($wporlogin_design == 'wporlogin_design_standard') || ($wporlogin_design == 'wporlogin_design_premium') ) ){ //Cuando existe diseño y es estandar/premium  
        
        if(get_option("wporlogin_titulo_logotipo")){
            
            return esc_html(get_option('wporlogin_titulo_logotipo'));
            
        }
        
    } else { //cuando el diseño es Default/básico imprime lo siguiente
    
        if(isset($_GET['action'])){
            
            $wporlogin_action = $_GET['action'];
            
            if($wporlogin_action == 'register'){
                
                return __('Sign Up', 'wporlogin');
                
            } else {
                
                if($wporlogin_action == 'lostpassword'){
                    
                    return __('Reset Password', 'wporlogin');  
                    
                } else {
                    
                    return __('Log In', 'wporlogin');
                    
                }
                
            }
        } else {
            
            return __('Log In', 'wporlogin');
            
        }
    } 
    
}
add_filter('login_headertext', 'wporlogin_mostrar_logotipo_title');




/**
 * Cargar traducciones de texto
 *
 * @since	1.0.0
 *
 */
function wporlogin_textdomain() {
    load_plugin_textdomain( 'wporlogin', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
//Cargar traducciones de texto
add_action( 'plugins_loaded', 'wporlogin_textdomain' );






//Formulario reCaptcha de Google
//FORMULARIO DE LOGIN

//version 2.5
//fuente 1: https://stackoverflow.com/questions/54032404/how-to-integrate-recaptcha-into-default-login-form-comment-in-wordpress
//fuente 2: https://stackoverflow.com/questions/54382995/wordpress-filter-wp-authenticate-user-not-getting-user-data
add_action('login_form','add_captcha_to_login'); // Login Form Hook
function add_captcha_to_login(){

    if(get_option( 'recaptcha_v2_wporlogin' ) == 1){ 
        
        if( get_option('recaptcha_v2_site_key_wporlogin') && get_option('recaptcha_v2_secret_key_wporlogin') ){

            if(get_option('activa_acceso_recaptcha_v2_wporlogin') == 1){
            
                $sitekey = get_option('recaptcha_v2_site_key_wporlogin'); ?>

                <script type="text/javascript">
                    var onloadCallback = function() {
                        grecaptcha.render('html_element', {
                            'sitekey' : '<?php echo $sitekey; ?>'
                        });
                    };
                </script><?php
            
            
                //Captcha Display Code ?>    
                <div id="html_element" style="margin-bottom: 10px;"></div><?php

            }

        }

    }

}



//Comprobando reCaptcha de Google
//FORMULARIO DE LOGIN


//version 2.5
add_filter('wp_authenticate_user', 'captcha_login_check', 10, 2); // Check Login Hook
function captcha_login_check($user, $password){

    if(get_option( 'recaptcha_v2_wporlogin' ) == 1){

        if( get_option('recaptcha_v2_site_key_wporlogin') && get_option('recaptcha_v2_secret_key_wporlogin') ){ 

            if( get_option('activa_acceso_recaptcha_v2_wporlogin') == 1 ){

/*                //Captcha Check Code
                if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){ 
                    // Google reCAPTCHA API secret key 
                    $secretKey = get_option('recaptcha_v2_secret_key_wporlogin'); 
                    
                    // Verify the reCAPTCHA response 
                    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']); 
                    
                    // Decode json data 
                    $responseData = json_decode($verifyResponse); 
                    
                    // If reCAPTCHA response is valid 
                    if($responseData->success){ 
                        // Posted form data 
                        return $user;

                    }else{ 

                        return new WP_Error("Captcha Invalid", __("<strong>ERROR</strong>: La verificación del robot falló, inténtelo de nuevo."));

                    } 
                    
                }else{

                    return new WP_Error("Captcha Invalid", __("<strong>ERROR</strong>: Por favor Marque la casilla reCAPTCHA.")); 

                }*/
                // Verificar si el reCAPTCHA se ha completado
                $recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

                // Si no hay respuesta de reCAPTCHA, mostrar un mensaje de error
                if (empty($recaptcha_response)) {
                    return new WP_Error('invalid_captcha', __('Por favor, completa la verificación de reCAPTCHA.', 'text_domain'));
                }

                // Hacer una solicitud a Google reCAPTCHA para verificar la respuesta
                $recaptcha_secret = get_option('recaptcha_v2_secret_key_wporlogin'); // Reemplaza esto con tu clave secreta de reCAPTCHA
                $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
                    'body' => array(
                        'secret' => $recaptcha_secret,
                        'response' => $recaptcha_response
                    )
                ));

                // Verificar si hubo un error en la solicitud
                if (is_wp_error($response)) {
                    return new WP_Error('recaptcha_error', __('Error al verificar reCAPTCHA. Por favor, inténtalo de nuevo.', 'text_domain'));
                }

                // Obtener el cuerpo de la respuesta
                $response_body = wp_remote_retrieve_body($response);

                // Decodificar la respuesta JSON
                $recaptcha_data = json_decode($response_body);

                // Verificar si la respuesta de reCAPTCHA es válida
                if (!$recaptcha_data || !$recaptcha_data->success) {
                    return new WP_Error('invalid_captcha', __('La verificación de reCAPTCHA ha fallado. Por favor, inténtalo de nuevo.', 'text_domain'));
                }

                return $user;

            } else {

                return $user;

            }

        } else {

            return $user;
            
        }

    } else { 

        return $user;

    }    
    

}




//Formulario de Registro WP
//
//Formulario reCaptcha de Google
//version 2.5
//fuente 1: https://stackoverflow.com/questions/54032404/how-to-integrate-recaptcha-into-default-login-form-comment-in-wordpress
//fuente 2: https://stackoverflow.com/questions/54382995/wordpress-filter-wp-authenticate-user-not-getting-user-data
add_action('register_form','add_captcha_to_register'); // Register Form Hook
function add_captcha_to_register(){

    if(get_option( 'recaptcha_v2_wporlogin' ) == 1){ 
        
        if( get_option('recaptcha_v2_site_key_wporlogin') && get_option('recaptcha_v2_secret_key_wporlogin') ){
            
            if(get_option('activa_registro_recaptcha_v2_wporlogin') == 1){

                $sitekey = get_option('recaptcha_v2_site_key_wporlogin'); ?>

                <script type="text/javascript">
                    var onloadCallback = function() {
                        grecaptcha.render('html_element', {
                            'sitekey' : '<?php echo $sitekey; ?>'
                        });
                    };
                </script><?php        
            
                //Captcha Display Code ?>    
                <div id="html_element" style="margin-bottom: 10px;"></div><?php

            }

        }

    }

}




//Comprobando reCaptcha de Google
//FORMULARIO DE REGISTRO

//version 2.5
add_filter('registration_errors', 'recaptcha_user_register_check', 10, 3); // Check register user Hook
function recaptcha_user_register_check($errors, $sanitized_user_login, $user_email){

    if(get_option( 'recaptcha_v2_wporlogin' ) == 1){

        if( get_option('recaptcha_v2_site_key_wporlogin') && get_option('recaptcha_v2_secret_key_wporlogin') ){ 

            if(get_option('activa_registro_recaptcha_v2_wporlogin') == 1){

                //Captcha Check Code
                if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){ 
                    // Google reCAPTCHA API secret key 
                    $secretKey = get_option('recaptcha_v2_secret_key_wporlogin'); 
                    
                    // Verify the reCAPTCHA response 
                    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']); 
                    
                    // Decode json data 
                    $responseData = json_decode($verifyResponse); 
                    
                    // If reCAPTCHA response is valid 
                    if($responseData->success){ 
                        // Posted form data 
                        return $errors;

                    }else{ 

                        $errors->add( 'captcha_error', __( '<strong>Error</strong>: La verificación del robot falló, inténtelo de nuevo.', 'my_textdomain' ) );
                        return $errors;

                    } 
                    
                }else{

                    $errors->add( 'captcha_error', __( '<strong>Error</strong>: Por favor Marque la casilla reCAPTCHA.', 'my_textdomain' ) );
                    return $errors;

                }
                
            } else {

                return $errors;

            }

        } else {

            return $errors;
            
        }

    } else { 

        return $errors;

    }    

}



function wporlogin_admin_notice__success() {

    $wporlogin_fecha_initial = get_option('wporlogin_date_5_review'); //Fecha Inicial - Installer Plugin WPOrLogin

    $wporlogin_date1_inicial = new DateTime($wporlogin_fecha_initial);
    $wporlogin_date2_actual = new DateTime();
    
    $wporlogin_diff = $wporlogin_date1_inicial->diff($wporlogin_date2_actual);

    //IMPRIMIR para ver los formatos
    //var_dump($wporlogin_diff);

    $number_days_plugin_wporlogin = $wporlogin_diff->days;

    if($number_days_plugin_wporlogin >= 15 ){ ?>

        <div class="notice notice-success is-dismissible jpum-notice delete_notice_wporlogin" style="padding-bottom: 10px;">

            <p>
                <a target="_blank" href="https://es.wordpress.org/plugins/wporlogin/" title="Plugin WPOrLogin">
                    <img class="logo" src="https://oregoom.com/wp-content/uploads/2022/01/icon-wporlogin.png" style="background-color: #fff; float: right; margin-left: 10px; width: 75px; padding: 0.25em; border: 1px solid #ccc;">
                </a>
                <strong>
                    ¡Hola! Ya has estado usando el plugin WPOrLogin en tu sitio durante 2 semanas - Esperamos que te haya sido útil. Si estás disfrutando del plugin, ¿te importaría valorarlo con 5 estrellas para ayudar a hacer correr la voz? <br>
                    <!--~<a target="_blank" href="https://oregoom.com/wporlogin/" title="Plugin WPOrLogin">Oregoom</a>-->

                </strong>
            </p>
            <ul>
                <li>
                    <a class="jpum-dismiss" target="_blank" href="https://wordpress.org/support/plugin/wporlogin/reviews/?filter=5" data-reason="am_now">
                        <strong>Vale, te lo mereces</strong>
                    </a>
                </li>
            </ul>

        </div> <?php

    }        

}
if( get_option('delete_notice_wporlogin_condition') != 1 ){
    add_action( 'admin_notices', 'wporlogin_admin_notice__success' );
}


add_action( 'wp_ajax_delete-notice-wp', 'delete_notice_wporlogin' );
function delete_notice_wporlogin() {

    $nonce_wporlogin_ajax = sanitize_text_field( $_POST['nonce'] );

    if( wp_verify_nonce ( $nonce_wporlogin_ajax, 'my-ajax-nonce-wporlogin') ){

        update_option( 'delete_notice_wporlogin_condition', '1' );
        echo 'Excelente';

    } else {

        //update_option( 'delete_notice_wporlogin_condition', '1' );
        die('Error de nonce');

    }

    wp_die();

}





function wporlogin_admin_notice_warning(){   
    
    //echo "<pre>";
    //print_r($_GET);
    //echo "</pre>";

    global $pagenow;

    if ( ( 'admin.php' === $pagenow && ( 'wporlogin-plugin' === $_GET['page']  ||  'recaptcha-wporlogin-plugin' === $_GET['page'] ||  'remove-language-plugin' === $_GET['page'] ) ) && isset( $_GET[ 'settings-updated' ] ) == true ) {
        ?>
        
        <div class="notice notice notice-warning">

            <p><strong>IMPORTANTE:</strong> Si no puedes ver los cambios que has realizado en tu sitio web, puede que necesites forzar a la página a cargar desde el servidor con <strong>Ctrl + f5</strong> o limpiar la caché de WordPress.</p>
            <p><a target="_blank" href="https://raiolanetworks.es/blog/borrar-cache-wordpress/" title="Borrar la cache de WordPress">Más información aquí</a></p>

        </div> <?php
    } 

}
add_action('admin_notices', 'wporlogin_admin_notice_warning' );