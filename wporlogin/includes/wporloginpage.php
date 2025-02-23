<?php
if(!defined('ABSPATH'))exit;

/*
 * Función para agregar un Menú y Página del Plugin WPOrLogin
 * en el Admin de WordPress
 */
function wporlogin_add_admin_menu_page(){
    
    //PD: https://codex.wordpress.org/Adding_Administration_Menus
    
    $page_title = __('Plugin WPOrLogin', 'wporlogin');         //Título de la página
    $menu_title = __('WPOrLogin', 'wporlogin');                //Título para Menú
    $capability = 'manage_options';                            //Capacidad - manage_option => Administrar opción
    $menu_slug = 'wporlogin-plugin';                           //El nombre del slug para referirse a este menú
    $function = 'wporlogin_content_page_menu';                 //La función que muestra el contenido de la página del menú.
    $icon_url = 'dashicons-unlock';                            //La url del icono que se utilizará para este menú.
    
    add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url);
    add_submenu_page( $menu_slug, __('Google reCAPTCHA', 'wporlogin'), __('Google reCAPTCHA', 'wporlogin'), 'manage_options', 'recaptcha-wporlogin-plugin', 'recaptcha_wporlogin_content_page_menu');
    add_submenu_page( $menu_slug, __('Remove Language', 'wporlogin'), __('Remove Language', 'wporlogin'), 'manage_options', 'remove-language-plugin', 'remove_language_content_page_menu');
    add_submenu_page( $menu_slug, __('Redirect', 'wporlogin'), __('Redirect', 'wporlogin'), 'manage_options', 'redirects-wporlogin-plugin', 'redirects_wporlogin_content_page_menu');
    //add_submenu_page( $menu_slug, __('Configure URLs', 'wporlogin'), __('Configure URLs', 'wporlogin'), 'manage_options', 'configure-urls-wporlogin-plugin', 'configure_urls_wporlogin_content_page_menu');

}
add_action('admin_menu','wporlogin_add_admin_menu_page');




function remove_language_content_page_menu(){
    ?>
    
    <div class="wrap"> 
    
        <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
            <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
        </div>

        <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
        
            <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Remove language selector', 'wporlogin'); ?></strong></h1>  
        
            <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('Hide or remove the language selector from the login page.', 'wporlogin'); ?></p>
    
        
            <?php settings_errors(); // Muestra los mensajes de éxito o de error cuando se envía el formulario ?>

            <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
            
            <?php 
            // Para proteger formularios
            wp_nonce_field(basename(__FILE__), 'remove_language_form_nonce'); 
            ?>
            
            <?php settings_fields( 'remove_language_wporlogin_custom_admin_settings_group' ); ?>
            <?php do_settings_sections( 'remove_language_wporlogin_custom_admin_settings_group' ); ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
                        
                <!-- CONTENIDO reCAPTCHA de Google -->
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">

                    <div style="border-bottom: 1px solid #e5e7e8; padding-bottom: 15px; padding-top: 10px;">
                        <span><?php _e('Need help? ', 'wporlogin'); ?><a href="#" target="_blank"><?php _e('Watch the video', 'wporlogin'); ?></a></span>
                    </div>
                                    
                    <table class="form-table" role="presentation">

                        <tbody>

                            <!-- Google reCAPTCHA -->
                            <tr>
                                <th scope="row">
                                    <label for="remove_language_wporlogin"><?php _e('Delete', 'wporlogin'); ?></label>
                                </th>
                                <td> 
                                    <input name="remove_language_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option( 'remove_language_wporlogin' ) ); ?> id="remove_language_wporlogin" />
                                    <label for="remove_language_wporlogin"><?php _e('Remove the dropdown language selector from the login page.', 'wporlogin'); ?></label>
                                </td>
                            </tr>

                        </tbody>
                    </table> 
                </div>

                <!-- FIN del contenido de Google reCAPTCHA -->
                
                <!-- COMIENZO BOTÓN DE DONACIÓN CON PAYPAL --><?php

                // Incluir la plantilla HTML desde el directorio de plantillas
                include(WPORLOGIN_PLUGIN_PATH . 'includes/templates/wporlogin-paypal-done.php'); ?>

                <!-- FIN DEL BOTÓN DE DONACIÓN CON PAYPAL -->
                
            </div>
            
            <?php submit_button(); ?>
            
        </form>

                
        </div>

    </div> <?php
}





/*
 * Función para agregar contenido HTML en la página reCAPTCHA del Plugin WPOrLogin
 */
function recaptcha_wporlogin_content_page_menu(){

    // Incluir la plantilla HTML desde el directorio de plantillas
    include(WPORLOGIN_PLUGIN_PATH . 'includes/templates/recaptcha-settings-template.php');

}

/*
 * Función para agregar contenido HTML en la página del Plugin WPOrLogin
 */
function wporlogin_content_page_menu() {     
    ?>

<style type="text/css">
    
    .wporlogin_container_select_option{
        width: 90% !important; 
        margin-left: auto !important; 
        margin-right: auto !important; 
        text-align: center !important;
        /*padding-bottom: 30px !important;*/
    }
    

    .wporlogin_container_select_option label {  
        font-family: 'Roboto', sans-serif !important;
        cursor: pointer !important;
        margin: 0 10px 0 10px !important;
    }

</style>

<div class="wrap"> 
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
    </div>
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">    
        
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Appearance', 'wporlogin'); ?></strong></h1>  
        
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('<strong>WPOrLogin</strong> allows you to customize the appearance of the WordPress login page.', 'wporlogin'); ?></p>
    
        <?php settings_errors(); // Muestra los mensajes de éxito o de error cuando se envía el formulario ?>
                
        <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
            
            <?php 
            // Protección del formulario
            wp_nonce_field(basename(__FILE__), 'wporlogin_form_nonce'); 
            ?>
            
            <?php settings_fields( 'wporlogin_custom_admin_settings_group' ); ?>
            <?php do_settings_sections( 'wporlogin_custom_admin_settings_group' ); ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
                
                <div style="width: 90%; margin-left: auto; margin-right: auto; text-align: center;">
                    <h2 style="font-size: 20px; font-family: 'Roboto', sans-serif; color: #0073AA;"><?php _e('Select an option', 'wporlogin'); ?></h2>
                </div>    

                <?php
                /*
                 * BEGIN: Seleccionar Opción Diseño WPOrLogin
                 */
                ?>

                <div class="wporlogin_container_select_option" style="display: flex; justify-content: center;">
                    
                    <div>
                        <div style="margin-bottom: 5px;">
                            <label for="wporlogin_design_basic">
                                <input <?php checked( 'wporlogin_design_basic', get_option( 'wporlogin_design' ) ); ?> type="radio" id="wporlogin_design_basic" class="wporlogin-option-input wporlogin-radio" name="wporlogin_design" value="wporlogin_design_basic">
                                <?php _e('Basic', 'wporlogin'); ?>
                            </label>
                        </div>                        
                        <div id="wporlogin-container-basic-triangulo" style="width: 0; height: 0; border-right: 15px solid transparent; border-bottom: 15px solid #AEB6BF; border-left: 15px solid transparent; margin-left: auto; margin-right: auto; <?php if(get_option('wporlogin_design') != 'wporlogin_design_basic'){ echo 'display: none;';} ?>"></div>
                    </div>           
                        
                    <div>
                        <div style="margin-bottom: 5px;">
                            <label for="wporlogin_design_standard">
                                <input <?php checked( 'wporlogin_design_standard', get_option( 'wporlogin_design' ) ); ?> type="radio" id="wporlogin_design_standard" class="wporlogin-option-input wporlogin-radio" name="wporlogin_design" value="wporlogin_design_standard">
                                <?php _e('Standard', 'wporlogin'); ?>
                            </label>
                        </div>
                        <div id="wporlogin-container-standard-triangulo" style="width: 0; height: 0; border-right: 15px solid transparent; border-bottom: 15px solid #AEB6BF; border-left: 15px solid transparent; margin-left: auto; margin-right: auto; <?php if(get_option('wporlogin_design') != 'wporlogin_design_standard'){ echo 'display: none;';} ?>"></div>
                    </div>
                                

                    <div>
                        <div style="margin-bottom: 5px;">
                            <label for="wporlogin_design_premium">
                                <input <?php checked( 'wporlogin_design_premium', get_option( 'wporlogin_design' ) ); ?> type="radio" id="wporlogin_design_premium" class="wporlogin-option-input wporlogin-radio" name="wporlogin_design" value="wporlogin_design_premium">
                                <?php _e('Premium', 'wporlogin'); ?>
                            </label>
                        </div>
                        <div id="wporlogin-container-premium-triangulo" style="width: 0; height: 0; border-right: 15px solid transparent; border-bottom: 15px solid #AEB6BF; border-left: 15px solid transparent; margin-left: auto; margin-right: auto; <?php if(get_option('wporlogin_design') != 'wporlogin_design_premium'){ echo 'display: none;';} ?>"></div>
                    </div>                    

                </div>
                
                <?php  // END: Seleccionar Opción Diseño WPOrLogin ?>    
                
                <!-- BEGIN DISEÑO BÁSICO -->                
                <div style="background-color: #AEB6BF; padding-top: 50px; padding-bottom: 50px; <?php if(get_option('wporlogin_design') != 'wporlogin_design_basic'){ echo 'display: none'; } ?>" class="wporlogin-container-design" id="wporlogin-container-basic">
                    
                    <div style="text-align: center;">
                        <img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'../img/wporlogin-design-basic.jpg'); ?>" style="box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); max-width: 100%; height: auto;">
                    </div>
                    
                </div><!-- END DISEÑO BÁSICO -->

                <!-- BEGIN DISEÑO ESTÁNDAR -->                
                <div style="background-color: #AEB6BF; padding-top: 50px; padding-bottom: 50px; <?php if(get_option('wporlogin_design') != 'wporlogin_design_standard'){ echo 'display: none'; } ?>"  class="wporlogin-container-design" id="wporlogin-container-standard">
                    
                    <div style="text-align: center;">
                        <img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'../img/wporlogin-design-standard.jpg'); ?>" style="box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); max-width: 100%; height: auto;">
                    </div>
                    
                </div><!-- END DISEÑO ESTÁNDAR -->

                <!-- BEGIN DISEÑO PREMIUM -->                
                <div style="background-color: #AEB6BF; padding-top: 50px; padding-bottom: 50px; <?php if(get_option('wporlogin_design') != 'wporlogin_design_premium'){ echo 'display: none'; } ?>"  class="wporlogin-container-design" id="wporlogin-container-premium">
                    
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-around;">

                        <div style="position: relative; margin-bottom: 15px;">
                            <input <?php checked( 'wporlogin_design_img_premium_one', get_option( 'wporlogin-design-img-premium' ) ); ?> value="wporlogin_design_img_premium_one" name="wporlogin-design-img-premium" id="wporlogin-design-img-premium-one" type="radio" style="position: absolute; margin: 10px 0 0 10px;">
                            <img onclick="wporloginimgclick('wporlogin-design-img-premium-one')" id="wporlogin-design-img-premium-one" src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'../img/wporlogin-design-premium-one.jpg'); ?>" style="margin-bottom: 10px; max-width: 100%; height: auto; cursor: pointer;">
                        </div>

                        <div style="position: relative; margin-bottom: 15px;">
                            <input <?php checked( 'wporlogin_design_img_premium_two', get_option( 'wporlogin-design-img-premium' ) ); ?> value="wporlogin_design_img_premium_two" name="wporlogin-design-img-premium" id="wporlogin-design-img-premium-two" type="radio" style="position: absolute; margin: 10px 0 0 10px;">
                            <img onclick="wporloginimgclick('wporlogin-design-img-premium-two')" id="wporlogin-design-img-premium-two" src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'../img/wporlogin-design-premium-two.jpg'); ?>" style="margin-bottom: 10px; max-width: 100%; height: auto; cursor: pointer;">
                        </div>

                        <div style="position: relative; margin-bottom: 15px;">
                            <input <?php checked( 'wporlogin_design_img_premium_three', get_option( 'wporlogin-design-img-premium' ) ); ?> value="wporlogin_design_img_premium_three" name="wporlogin-design-img-premium" id="wporlogin-design-img-premium-three" type="radio" style="position: absolute; margin: 10px 0 0 10px;">
                            <img onclick="wporloginimgclick('wporlogin-design-img-premium-three')" id="wporlogin-design-img-premium-three" src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'../img/wporlogin-design-premium-three.jpg'); ?>" style="margin-bottom: 10px; max-width: 100%; height: auto; cursor: pointer;">
                        </div>

                        <script>
                            function wporloginimgclick(valor){
                                document.getElementById(valor).checked=true;
                            }
                        </script>

                    </div>
                    
                </div><!-- END DISEÑO PREMIUM -->

                
                <!--BEGIN DISEÑO ESTÁNDAR Y PREMIUM-->
                <div class="wporlogin-container-design-premium" id="wporlogin-container-standard-premium" style="padding-top: 40px; <?php if(get_option('wporlogin_design') != 'wporlogin_design_standard' && get_option('wporlogin_design') != 'wporlogin_design_premium'){ echo 'display: none;'; } ?> width: 90%; margin-left: auto; margin-right: auto;">
                    
                    <table class="form-table" role="presentation">
                
                        <tbody>
                
                        <!--CABEZA DEL LOGOTIPO-->
                        <tr>
                            <th scope="row">
                                <label style="font-size: 1.5em;"><strong><?php _e('Logo', 'wporlogin'); ?></strong></label>
                            </th>
                            <td><hr></td>
                        </tr>
                
                        <!--URL DEL LOGOTIPO-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_url_logotipo_text"><?php _e('Logo', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <?php
                                if(esc_url(get_option('wporlogin_url_logotipo'))){
                                ?>
                                <img id="wporlogin_url_logotipo_img" src="<?php echo esc_url(get_option('wporlogin_url_logotipo')); ?>" style="<?php if(get_option('wporlogin_design') == 'wporlogin_design_premium' ) { echo 'background-color: #D6DBDF;'; } else { echo 'background-color: #ffffff;'; }  ?> margin-bottom: 10px; width: 220px; padding: 10px;  border: 2px dashed rgba(0,0,0,.1);"><br>
                                <?php
                                }
                                ?>
                                <input aria-label="Close" id="wporlogin_url_logotipo_text" type="text" name="wporlogin_url_logotipo" class="regular-text" style="margin-bottom: 10px;" value="<?php echo esc_url(get_option('wporlogin_url_logotipo')); ?>"/><br>
                                <input id="wporlogin_url_logotipo_button" type="button" class="button" value="<?php _e('Upload logo', 'wporlogin'); ?>" />
                                <p class="description" id="tagline-description"><?php _e('You can upload your logo here. A maximum width of <strong>300 pixels</strong> is recommended.', 'wporlogin'); ?></p>
                            </td>
                        </tr>
                
                        <!--Ancho de la imagen-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_width_logotipo_text"><?php _e('Logo width', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <input id="wporlogin_width_logotipo_text" type="number" name="wporlogin_width_logotipo_text" class="small-text" value="<?php echo esc_html(get_option('wporlogin_width_logotipo_text')); ?>"/>
                                <span class="description" id="tagline-description"><?php _e('Specify the logo width in pixels.', 'wporlogin'); ?></span>
                            </td>
                        </tr>
                
                        <!--Altura de la imagen-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_height_logotipo_text"><?php _e('Logo height', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <input id="wporlogin_height_logotipo_text" type="number" name="wporlogin_height_logotipo_text" class="small-text" value="<?php echo esc_html(get_option('wporlogin_height_logotipo_text')); ?>"/>
                                <span class="description" id="tagline-description"><?php _e('Specify the logo height in pixels.', 'wporlogin'); ?></span>
                            </td>
                        </tr>
                
                        <!--Posición de fondo-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_background_position_logotipo_select"><?php _e('Logo position', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <select name="wporlogin_background_position_logotipo_select" id="wporlogin_background_position_logotipo_select" class="regular">
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '0'); ?> value="0"><?php _e('Top left', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '1'); ?> value="1"><?php _e('Center left', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '2'); ?> value="2"><?php _e('Bottom left', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '3'); ?> value="3"><?php _e('Top right', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '4'); ?> value="4"><?php _e('Center right', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '5'); ?> value="5"><?php _e('Bottom right', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '6'); ?> value="6"><?php _e('Top center', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '7'); ?> value="7"><?php _e('Center', 'wporlogin'); ?></option>  
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '8'); ?> value="8"><?php _e('Bottom center', 'wporlogin'); ?></option>                                       
                                </select>
                                <span class="description" id="tagline-description"><?php _e('Set the initial position of the logo on the login page.', 'wporlogin'); ?></span>
                            </td>
                        </tr>
                
                        <!--Tamaño de fondo-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_background_size_logotipo_select"><?php _e('Background size', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <select name="wporlogin_background_size_logotipo_select" id="wporlogin_background_size_logotipo_select" class="regular">
                                    <option <?php selected(get_option('wporlogin_background_size_logotipo_select'), '0'); ?> value="0"><?php _e('None → No background size adjustment.', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_size_logotipo_select'), '1'); ?> value="1"><?php _e('Cover → The image will cover the entire background without losing proportion.', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_size_logotipo_select'), '2'); ?> value="2"><?php _e('Contain → The image will fit within the background without being cropped.', 'wporlogin'); ?></option>                                   
                                </select>
                                <span class="description" id="tagline-description"><?php _e('Adjust how the background image is displayed.', 'wporlogin'); ?></span>
                            </td>
                        </tr>
                
                        <!--URL del LOGOTIPO-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_ruta_url_logotipo_text"><?php _e('Logo URL', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <input id="wporlogin_ruta_url_logotipo_text" type="text" name="wporlogin_ruta_url_logotipo" class="regular-text" placeholder="https://ejemplo.com" value="<?php echo esc_html(get_option('wporlogin_ruta_url_logotipo')); ?>"/><br>
                                <p class="description" id="tagline-description"><?php _e('Specify the URL where the logo will redirect when clicked.', 'wporlogin'); ?></p>
                            </td>
                        </tr>
                
                        <!--Título del LOGOTIPO-->
                        <tr>                            
                            <th scope="row">
                                <label for="wporlogin_titulo_logotipo_text"><?php _e('Logo title', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <input id="wporlogin_titulo_logotipo_text" type="text" name="wporlogin_titulo_logotipo" class="regular-text" value="<?php echo esc_html(get_option('wporlogin_titulo_logotipo')); ?>"/><br>
                                <p class="description" id="tagline-description"><?php _e('Set the alternative title for the logo (useful for accessibility and SEO).', 'wporlogin'); ?></p>
                            </td>
                        </tr>
                
                        <!--IMAGEN DE FONDO-->
                        <tr>
                            <th scope="row">
                                <label style="font-size: 1.5em;"><strong><?php _e('Background image', 'wporlogin'); ?></strong></label>
                            </th>
                            <td><hr></td>
                        </tr>
                
                        <!--URL de la imagen de fondo-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin-img-fondo"><?php _e('Background image', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <div style="margin-bottom: 20px;">
                                    <input type="radio" id="wporlogin_free_images" name="wporlogin_background_images" value="wporlogin_free_images" <?php checked(get_option('wporlogin_background_images'), 'wporlogin_free_images'); ?>>
                                    <label id="wporlogin_free_images" for="wporlogin_free_images"><?php _e('Free images', 'wporlogin'); ?></label>
                
                                    <input type="radio" id="wporlogin_my_images" name="wporlogin_background_images" value="wporlogin_my_images" <?php checked(get_option('wporlogin_background_images'), 'wporlogin_my_images'); ?> style="margin-left: 20px;">
                                    <label id="wporlogin_my_images" for="wporlogin_my_images"><?php _e('My images', 'wporlogin'); ?></label>
                                </div>                                    
                
                                <!--IMAGEN DE FONDO PERSONALIZADA-->
                                <div id="wporlogin-container-background-my-image" style="<?php if( (get_option('wporlogin_background_images') == 'wporlogin_free_images' )){ echo 'display: none;'; } ?>">
                                    <?php
                                    if(esc_url(get_option('wporlogin_url_img_fondo'))){ ?>
                                        <img id="wporlogin_url_img_fondo_img" src="<?php echo esc_url(get_option('wporlogin_url_img_fondo')); ?>" style="margin-bottom: 10px; width: 220px; padding: 10px; background-color: #ffffff; border: 2px dashed rgba(0,0,0,.1);"><br>
                                    <?php } ?>
                                    <input id="wporlogin_url_img_fondo_text" type="text" name="wporlogin_url_img_fondo" class="regular-text" style="margin-bottom: 10px;" value="<?php echo esc_html(get_option('wporlogin_url_img_fondo')); ?>"/><br>
                                    <input id="wporlogin_url_img_fondo_button" type="button" class="button" value="<?php _e('Upload or Select Background Image', 'wporlogin'); ?>" />
                                    <p class="description" id="tagline-description"><?php _e('Select a background image from here. For the best results, use images with a resolution of <strong>1920x1080 pixels</strong>.', 'wporlogin'); ?>.</p>
                                </div><!--FIN IMAGEN DE FONDO PERSONALIZADA-->                                    
                
                                <!--IMÁGENES GRATUITAS-->
                                <div id="wporlogin-container-background-free-image" style="<?php if( get_option('wporlogin_background_images') == 'wporlogin_my_images' ){ echo 'display: none;'; } ?>">
                                    <p class="description"><?php _e('You can select a background image from here', 'wporlogin'); ?></p>
                                    <br><br>
                
                                    <div style="overflow: hidden;">
                                        <?php for($i=0; $i<14; $i++){ ?>
                                        <div style="float: left;">
                                            <input type="radio" id="wporlogin-background-free-image-<?php echo $i; ?>" name="wporlogin-background-free-image" value="<?php echo esc_url(WPORLOGINBACKGROUNDIMAGE[$i]); ?>" <?php if($i == 0){ if( get_option('wporlogin-background-free-image') != false){ checked( get_option('wporlogin-background-free-image'), WPORLOGINBACKGROUNDIMAGE[$i]); } else { echo 'checked'; } } else { checked( get_option('wporlogin-background-free-image'), WPORLOGINBACKGROUNDIMAGE[$i]); } ?>>
                                            <label for="wporlogin-background-free-image-<?php echo $i; ?>"><?php _e('Image ', 'wporlogin'); ?><?php echo $i+1; ?></label>
                                            <div style="padding-top: 10px; margin-right: 15px;">
                                                <img id="wporlogin_url_img_fondo_img" src="<?php echo esc_url(WPORLOGINBACKGROUNDIMAGE[$i]); ?>" style="margin-bottom: 10px; width: 220px; padding: 10px; background-color: #ffffff; border: 2px dashed rgba(0,0,0,.1);"><br>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <p class="description"><?php _e('Images from Unsplash and Pixabay are free to use. You can download more images from <a href="https://unsplash.com/" target="_blank"><strong>Unsplash</strong></a> and <a href="https://pixabay.com/" target="_blank"><strong>Pixabay</strong></a>.', 'wporlogin'); ?></p>
                                </div>    
                            </td>
                        </tr>  
                        </tbody>
                    </table>  
                </div><!--FIN DISEÑO ESTÁNDAR-->
                
                
                <!--BEGIN BOTÓN DE DONACIÓN CON PAYPAL--><?php
                
                // Incluir la plantilla HTML desde el directorio de plantillas
                include(WPORLOGIN_PLUGIN_PATH . 'includes/templates/wporlogin-paypal-done.php'); ?>

                <!--END BOTÓN DE DONACIÓN CON PAYPAL-->
                
            </div>
            
            <?php submit_button(); ?>
            
        </form>
        
    </div>    
    
</div>

<?php
}



function wporlogin_register_options_admin_page() {

    //delete_option('delete_notice_wporlogin_condition');
    //delete_option('wporlogin_date_5_review');

    //Date: para RESEÑA de 5 estrellas
    //Fecha Inicial - Installer Plugin WPOrLogin
    add_option('wporlogin_date_5_review', date("Y-m-d H:i:s"));


    /* 
    * AGREGAR valor predeterminado/por defecto
    */
    add_option( 'wporlogin_design', 'wporlogin_design_basic' );

    add_option( 'wporlogin_width_logotipo_text', '200');
    add_option( 'wporlogin_background_position_logotipo_select', '7');
    add_option( 'wporlogin_background_size_logotipo_select', '2');

    add_option( 'wporlogin_background_images', 'wporlogin_free_images');
    add_option( 'wporlogin_url_img_fondo', esc_url(WPORLOGINBACKGROUNDIMAGE[0]));

    //AGREGAR Diseño one premium
    add_option( 'wporlogin-design-img-premium', 'wporlogin_design_img_premium_one' );
    
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_design');
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin-design-img-premium');
    
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_url_logotipo');
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_width_logotipo_text');
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_height_logotipo_text');
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_background_position_logotipo_select');
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_background_size_logotipo_select');
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_ruta_url_logotipo');
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_titulo_logotipo');

    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_url_img_fondo');    
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin_background_images');
    
    register_setting( 'wporlogin_custom_admin_settings_group', 'wporlogin-background-free-image');
    
}
add_action('admin_init','wporlogin_register_options_admin_page');





//reCAPTCHA
function recaptcha_wporlogin_register_options_admin_page() {    

    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'recaptcha_v2_wporlogin');//reCAPTCHA v2
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'recaptcha_version_wporlogin');//reCAPTCHA v3
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'recaptcha_v2_site_key_wporlogin');
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'recaptcha_v2_secret_key_wporlogin');
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'recaptcha_v3_site_key_wporlogin');
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'recaptcha_v3_secret_key_wporlogin');

    /* 
    * AGREGAR valor predeterminado
    */
    add_option( 'activa_acceso_recaptcha_v2_wporlogin', '1' );
    add_option( 'activa_registro_recaptcha_v2_wporlogin', '1' );

    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'activa_acceso_recaptcha_v2_wporlogin');
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'activa_registro_recaptcha_v2_wporlogin');
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'activa_recuperar_recaptcha_wporlogin'); // Nueva opción para recuperación de contraseña
    
}
add_action('admin_init','recaptcha_wporlogin_register_options_admin_page');


//remove_language_wporlogin
function remove_language_wporlogin_register_options_admin_page(){

    register_setting( 'remove_language_wporlogin_custom_admin_settings_group', 'remove_language_wporlogin');

}
add_action('admin_init','remove_language_wporlogin_register_options_admin_page');







