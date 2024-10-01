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
    add_submenu_page( $menu_slug, __('Eliminar Idioma', 'wporlogin'), __('Eliminar Idioma', 'wporlogin'), 'manage_options', 'remove-language-plugin', 'remove_language_content_page_menu');
    
}
add_action('admin_menu','wporlogin_add_admin_menu_page');




function remove_language_content_page_menu(){
    ?>
    
    <div class="wrap"> 
    
        <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
            <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
        </div>

        <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
        
            <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Eliminar selector de idioma', 'wporlogin'); ?></strong></h1>  
        
            <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('Eliminar el selector de idioma disponible en la pantalla de inicio de sesión', 'wporlogin'); ?></p>
    
        
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
                        <span><?php _e('¿Necesitas ayuda? ', 'wporlogin'); ?><a href="#" target="_blank"><?php _e('Ver el video', 'wporlogin'); ?></a></span>
                    </div>
                                    
                    <table class="form-table" role="presentation">

                        <tbody>

                            <!-- Google reCAPTCHA -->
                            <tr>
                                <th scope="row">
                                    <label for="remove_language_wporlogin"><?php _e('Eliminar', 'wporlogin'); ?></label>
                                </th>
                                <td> 
                                    <input name="remove_language_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option( 'remove_language_wporlogin' ) ); ?> id="remove_language_wporlogin" />
                                    <label for="remove_language_wporlogin"><?php _e('Eliminar el menú', 'wporlogin'); ?></label>
                                    <br><br>
                                    <!-- Sitio web de Google reCAPTCHA -->
                                    <p><?php _e('Eliminar el menú desplegable de idioma', 'wporlogin'); ?>.</p>
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
        
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Apariencia', 'wporlogin'); ?></strong></h1>  
        
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><strong>WPOrLogin</strong> <?php _e('te permite modificar la apariencia de la página de inicio de sesión de WordPress', 'wporlogin'); ?></p>
    
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
                    <h2 style="font-size: 20px; font-family: 'Roboto', sans-serif; color: #0073AA;"><?php _e('Selecciona una opción', 'wporlogin'); ?></h2>
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
                                <?php _e('Básico', 'wporlogin'); ?>
                            </label>
                        </div>                        
                        <div id="wporlogin-container-basic-triangulo" style="width: 0; height: 0; border-right: 15px solid transparent; border-bottom: 15px solid #AEB6BF; border-left: 15px solid transparent; margin-left: auto; margin-right: auto; <?php if(get_option('wporlogin_design') != 'wporlogin_design_basic'){ echo 'display: none;';} ?>"></div>
                    </div>           
                        
                    <div>
                        <div style="margin-bottom: 5px;">
                            <label for="wporlogin_design_standard">
                                <input <?php checked( 'wporlogin_design_standard', get_option( 'wporlogin_design' ) ); ?> type="radio" id="wporlogin_design_standard" class="wporlogin-option-input wporlogin-radio" name="wporlogin_design" value="wporlogin_design_standard">
                                <?php _e('Estándar', 'wporlogin'); ?>
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
                                <label style="font-size: 1.5em;"><strong><?php _e('Logotipo', 'wporlogin'); ?></strong></label>
                            </th>
                            <td><hr></td>
                        </tr>
                
                        <!--URL DEL LOGOTIPO-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_url_logotipo_text"><?php _e('Logotipo', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <?php
                                if(esc_url(get_option('wporlogin_url_logotipo'))){
                                ?>
                                <img id="wporlogin_url_logotipo_img" src="<?php echo esc_url(get_option('wporlogin_url_logotipo')); ?>" style="<?php if(get_option('wporlogin_design') == 'wporlogin_design_premium' ) { echo 'background-color: #D6DBDF;'; } else { echo 'background-color: #ffffff;'; }  ?> margin-bottom: 10px; width: 220px; padding: 10px;  border: 2px dashed rgba(0,0,0,.1);"><br>
                                <?php
                                }
                                ?>
                                <input aria-label="Cerrar" id="wporlogin_url_logotipo_text" type="text" name="wporlogin_url_logotipo" class="regular-text" style="margin-bottom: 10px;" value="<?php echo esc_url(get_option('wporlogin_url_logotipo')); ?>"/><br>
                                <input id="wporlogin_url_logotipo_button" type="button" class="button" value="<?php _e('Seleccionar el logotipo', 'wporlogin'); ?>" />
                                <p class="description" id="tagline-description"><?php _e('Puedes subir tu logotipo desde aquí', 'wporlogin'); ?>.</p>
                                <p class="description" id="tagline-description"><?php _e('Recomendaría un ancho máximo de', 'wporlogin'); ?> <strong><?php _e('300 píxeles', 'wporlogin'); ?>.</strong></p>
                            </td>
                        </tr>
                
                        <!--Ancho de la imagen-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_width_logotipo_text"><?php _e('Ancho del logotipo', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <input id="wporlogin_width_logotipo_text" type="number" name="wporlogin_width_logotipo_text" class="small-text" value="<?php echo esc_html(get_option('wporlogin_width_logotipo_text')); ?>"/>
                                <span class="description" id="tagline-description"><?php _e('Ingrese el ancho deseado del logotipo', 'wporlogin'); ?>.</span>
                            </td>
                        </tr>
                
                        <!--Altura de la imagen-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_height_logotipo_text"><?php _e('Altura del logotipo', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <input id="wporlogin_height_logotipo_text" type="number" name="wporlogin_height_logotipo_text" class="small-text" value="<?php echo esc_html(get_option('wporlogin_height_logotipo_text')); ?>"/>
                                <span class="description" id="tagline-description"><?php _e('Ingrese la altura deseada del logotipo', 'wporlogin'); ?>.</span>
                            </td>
                        </tr>
                
                        <!--Posición de fondo-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_background_position_logotipo_select"><?php _e('Posición del logotipo', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <select name="wporlogin_background_position_logotipo_select" id="wporlogin_background_position_logotipo_select" class="regular">
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '0'); ?> value="0"><?php _e('izquierda superior', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '1'); ?> value="1"><?php _e('izquierda centro', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '2'); ?> value="2"><?php _e('izquierda inferior', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '3'); ?> value="3"><?php _e('derecha superior', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '4'); ?> value="4"><?php _e('derecha centro', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '5'); ?> value="5"><?php _e('derecha inferior', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '6'); ?> value="6"><?php _e('centro superior', 'wporlogin'); ?></option>
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '7'); ?> value="7"><?php _e('centro centro', 'wporlogin'); ?></option>  
                                    <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '8'); ?> value="8"><?php _e('centro inferior', 'wporlogin'); ?></option>                                       
                                </select>
                                <span class="description" id="tagline-description"><?php _e('Define la posición inicial de la imagen de fondo', 'wporlogin'); ?>.</span>
                            </td>
                        </tr>
                
                        <!--Tamaño de fondo-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_background_size_logotipo_select"><?php _e('Tamaño del fondo', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <select name="wporlogin_background_size_logotipo_select" id="wporlogin_background_size_logotipo_select" class="regular">
                                    <option <?php selected(get_option('wporlogin_background_size_logotipo_select'), '0'); ?> value="0">none</option>
                                    <option <?php selected(get_option('wporlogin_background_size_logotipo_select'), '1'); ?> value="1">cover</option>
                                    <option <?php selected(get_option('wporlogin_background_size_logotipo_select'), '2'); ?> value="2">contain</option>                                   
                                </select>
                                <span class="description" id="tagline-description"><?php _e('Especifica el tamaño de la imagen de fondo', 'wporlogin'); ?>.</span>
                            </td>
                        </tr>
                
                        <!--URL del LOGOTIPO-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin_ruta_url_logotipo_text"><?php _e('URL del logotipo', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <input id="wporlogin_ruta_url_logotipo_text" type="text" name="wporlogin_ruta_url_logotipo" class="regular-text" placeholder="https://ejemplo.com" value="<?php echo esc_html(get_option('wporlogin_ruta_url_logotipo')); ?>"/><br>
                                <p class="description" id="tagline-description"><?php _e('Cambia la URL del logotipo de inicio de sesión', 'wporlogin'); ?>.</p>
                            </td>
                        </tr>
                
                        <!--Título del LOGOTIPO-->
                        <tr>                            
                            <th scope="row">
                                <label for="wporlogin_titulo_logotipo_text"><?php _e('Título del logotipo', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <input id="wporlogin_titulo_logotipo_text" type="text" name="wporlogin_titulo_logotipo" class="regular-text" value="<?php echo esc_html(get_option('wporlogin_titulo_logotipo')); ?>"/><br>
                                <p class="description" id="tagline-description"><?php _e('Cambia el título del logotipo en el inicio de sesión', 'wporlogin'); ?>.</p>
                            </td>
                        </tr>
                
                        <!--IMAGEN DE FONDO-->
                        <tr>
                            <th scope="row">
                                <label style="font-size: 1.5em;"><strong><?php _e('Imagen de fondo', 'wporlogin'); ?></strong></label>
                            </th>
                            <td><hr></td>
                        </tr>
                
                        <!--URL de la imagen de fondo-->
                        <tr>
                            <th scope="row">
                                <label for="wporlogin-img-fondo"><?php _e('Imagen de fondo', 'wporlogin'); ?></label>
                            </th>
                            <td>
                                <div style="margin-bottom: 20px;">
                                    <input type="radio" id="wporlogin_free_images" name="wporlogin_background_images" value="wporlogin_free_images" <?php checked(get_option('wporlogin_background_images'), 'wporlogin_free_images'); ?>>
                                    <label id="wporlogin_free_images" for="wporlogin_free_images"><?php _e('Imágenes gratuitas', 'wporlogin'); ?></label>
                
                                    <input type="radio" id="wporlogin_my_images" name="wporlogin_background_images" value="wporlogin_my_images" <?php checked(get_option('wporlogin_background_images'), 'wporlogin_my_images'); ?> style="margin-left: 20px;">
                                    <label id="wporlogin_my_images" for="wporlogin_my_images"><?php _e('Mis imágenes', 'wporlogin'); ?></label>
                                </div>                                    
                
                                <!--IMAGEN DE FONDO PERSONALIZADA-->
                                <div id="wporlogin-container-background-my-image" style="<?php if( (get_option('wporlogin_background_images') == 'wporlogin_free_images' )){ echo 'display: none;'; } ?>">
                                    <?php
                                    if(esc_url(get_option('wporlogin_url_img_fondo'))){ ?>
                                        <img id="wporlogin_url_img_fondo_img" src="<?php echo esc_url(get_option('wporlogin_url_img_fondo')); ?>" style="margin-bottom: 10px; width: 220px; padding: 10px; background-color: #ffffff; border: 2px dashed rgba(0,0,0,.1);"><br>
                                    <?php } ?>
                                    <input id="wporlogin_url_img_fondo_text" type="text" name="wporlogin_url_img_fondo" class="regular-text" style="margin-bottom: 10px;" value="<?php echo esc_html(get_option('wporlogin_url_img_fondo')); ?>"/><br>
                                    <input id="wporlogin_url_img_fondo_button" type="button" class="button" value="<?php _e('Seleccionar imagen de fondo', 'wporlogin'); ?>" />
                                    <p class="description" id="tagline-description"><?php _e('Puedes subir una imagen de fondo desde aquí', 'wporlogin'); ?>.</p>
                                    <p class="description" id="tagline-description"><?php _e('Obtendrás los mejores resultados al usar imágenes con una dimensión de 1920x1080 píxeles', 'wporlogin'); ?>.</p>
                                </div><!--FIN IMAGEN DE FONDO PERSONALIZADA-->                                    
                
                                <!--IMÁGENES GRATUITAS-->
                                <div id="wporlogin-container-background-free-image" style="<?php if( get_option('wporlogin_background_images') == 'wporlogin_my_images' ){ echo 'display: none;'; } ?>">
                                    <p class="description"><?php _e('Puedes seleccionar una imagen de fondo desde aquí', 'wporlogin'); ?></p>
                                    <p class="description"><?php _e('Las imágenes de ', 'wporlogin'); ?><a href="https://unsplash.com/" target="_blank"><strong>Unsplash</strong></a><?php _e(' y ', 'wporlogin'); ?><a href="https://pixabay.com/" target="_blank"><strong>Pixabay</strong></a><?php _e(' están hechas para ser usadas libremente', 'wporlogin'); ?>.</p><br><br>
                
                                    <div style="overflow: hidden;">
                                        <?php for($i=0; $i<14; $i++){ ?>
                                        <div style="float: left;">
                                            <input type="radio" id="wporlogin-background-free-image-<?php echo $i; ?>" name="wporlogin-background-free-image" value="<?php echo esc_url(WPORLOGINBACKGROUNDIMAGE[$i]); ?>" <?php if($i == 0){ if( get_option('wporlogin-background-free-image') != false){ checked( get_option('wporlogin-background-free-image'), WPORLOGINBACKGROUNDIMAGE[$i]); } else { echo 'checked'; } } else { checked( get_option('wporlogin-background-free-image'), WPORLOGINBACKGROUNDIMAGE[$i]); } ?>>
                                            <label for="wporlogin-background-free-image-<?php echo $i; ?>"><?php _e('Imagen ', 'wporlogin'); ?><?php echo $i+1; ?></label>
                                            <div style="padding-top: 10px; margin-right: 15px;">
                                                <img id="wporlogin_url_img_fondo_img" src="<?php echo esc_url(WPORLOGINBACKGROUNDIMAGE[$i]); ?>" style="margin-bottom: 10px; width: 220px; padding: 10px; background-color: #ffffff; border: 2px dashed rgba(0,0,0,.1);"><br>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <p><?php _e('Puedes descargar más imágenes desde ', 'wporlogin'); ?><a href="https://unsplash.com/" target="_blank"><strong><?php _e('aquí', 'wporlogin'); ?></strong></a><?php _e(' totalmente gratis', 'wporlogin'); ?>.</p>
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







