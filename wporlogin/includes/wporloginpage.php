<?php
if(!defined('ABSPATH'))exit;

/*
 * Function para agregar un Menú y Página del Plugin WPOrLogin
 * en Admin de WordPress
 */
function wporlogin_add_admin_menu_page(){
    
    //PD: https://codex.wordpress.org/Adding_Administration_Menus
    
    $page_title = 'Plugin WPOrLogin';           //Título de la página
    $menu_title = 'WPOrLogin';                  //Título para Menú
    $capability = 'manage_options';             //Capacidad - manage_option => Adminsitrar opción
    $menu_slug = 'wporlogin-plugin';            //El nombre del slug para referirse a este menú
    $function = 'wporlogin_content_page_menu';  //La función que muestra el contenido de la página del menú.
    $icon_url = 'dashicons-unlock';             //La url del icono que se utilizará para este menú.
    
    add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url);
    add_submenu_page( $menu_slug, 'Google reCAPTCHA', 'Google reCAPTCHA', 'manage_options', 'recaptcha-wporlogin-plugin', 'recaptcha_wporlogin_content_page_menu');
    add_submenu_page( $menu_slug, 'Remove Language', 'Remove Language', 'manage_options', 'remove-language-plugin', 'remove_language_content_page_menu');
    
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
        
            <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('Eliminar selector de idioma disponible en la pantalla de inicio de sesión', 'wporlogin'); ?></p>
    
        
            <?php settings_errors();//Muestra los mensajes de éxito o de error cuando se envía el formulario ?>

            <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
            
            <?php 
            //Para proteger formularios
            wp_nonce_field(basename(__FILE__), 'remove_language_form_nonce'); 
            ?>
            
            <?php settings_fields( 'remove_language_wporlogin_custom_admin_settings_group' ); ?>
            <?php do_settings_sections( 'remove_language_wporlogin_custom_admin_settings_group' ); ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
                        
                <!--BEGIN Content Google reCAPTCHA-->
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">

                    <div style="border-bottom: 1px solid #e5e7e8; padding-bottom: 15px; padding-top: 10px;">
                        <span><?php _e('¿Necesitas ayuda? ', 'wporlogin'); ?><a href="#" target="_blank"> <?php _e('Ver el vídeo', 'wporlogin'); ?></a></span>
                    </div>
                                    
                    <table class="form-table" role="presentation">

                        <tbody>

                            <!--Google reCAPTCHA-->
                            <tr>
                                <th scope="row">
                                    <label for="remove_language_wporlogin"><?php _e('Eliminar', 'wporlogin'); ?></label>
                                </th>
                                <td> 
                                    <input name="remove_language_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option( 'remove_language_wporlogin' ) ); ?> id="remove_language_wporlogin" />
                                    <label for="remove_language_wporlogin"><?php _e('Eliminar el menú', 'wporlogin'); ?></label>
                                    <br><br>
                                    <!--Web Site de Google reCAPTCHA-->
                                    <p><?php _e('Eliminar el menú desplegable de Idioma', 'wporlogin'); ?>.</p>
                                </td>
                            </tr>

                        </tbody>
                    </table> 

                </div><!--END Content Google reCAPTCHA-->
                
                <!--BEGIN BOTÓN DE DONACIÓN CON PAYPAL-->
                <div style="margin-top: 40px; border-bottom: solid 1px #005d8c; border-top: 1px #005d8c solid;">
                    <div style="width: 90%; margin-left: auto; margin-right: auto;">
                        
                        <table class="form-table">                
                            <tbody>

                                <tr>
                                    <th scope="row"><label><?php _e('Donate now', 'wporlogin') ?></label></th>
                                    <td>
                                        <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CTG69VCQ5TZZN&source=url">
                                            <img src="<?php echo esc_url('https://www.paypalobjects.com/'.__('en_US', 'wporlogin').'/i/btn/btn_donate_SM.gif'); ?>">
                                        </a>
                                        <p class="description" id="tagline-description">
                                            <strong><?php _e('Important', 'wporlogin'); ?>: </strong><?php _e('If you value my work, considered a small donation to show your appreciation. Thank you!', 'wporlogin'); ?>
                                        </p>
                                        <p class="description" id="tagline-description"><?php _e('To donate now, click the Donate button', 'wporlogin'); ?></p>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        
                    </div>
                </div><!--END BOTÓN DE DONACIÓN CON PAYPAL-->
                
            </div>
            
            <?php submit_button(); ?>
            
        </form>

                
        </div>

    </div> <?php
}




/*
 * Function para agregar contenido HTML en la página reCAPTCHA del Plugin WPOrLogin
 */
function recaptcha_wporlogin_content_page_menu(){
    ?>
    
    <div class="wrap"> 
    
        <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
            <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
        </div>

        <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
        
            <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Google reCAPTCHA', 'wporlogin'); ?></strong></h1>  
        
            <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('Protect your website with reCAPTCHA v2.', 'wporlogin'); ?></p>
    
        
            <?php settings_errors();//Muestra los mensajes de éxito o de error cuando se envía el formulario ?>

            <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
            
            <?php 
            //Para proteger formularios
            wp_nonce_field(basename(__FILE__), 'recaptcha_wporlogin_form_nonce'); 
            ?>
            
            <?php settings_fields( 'recaptcha_wporlogin_custom_admin_settings_group' ); ?>
            <?php do_settings_sections( 'recaptcha_wporlogin_custom_admin_settings_group' ); ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
                        
                <!--BEGIN Content Google reCAPTCHA-->
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">

                    <div style="border-bottom: 1px solid #e5e7e8; padding-bottom: 15px; padding-top: 10px;">
                        <span><?php _e('¿Necesitas ayuda? ', 'wporlogin'); ?><a href="https://youtu.be/U5x6FE5rre0" target="_blank"> <?php _e('Ver el vídeo', 'wporlogin'); ?></a></span>
                    </div>
                                    
                    <table class="form-table" role="presentation">

                        <tbody>

                            <!--Google reCAPTCHA-->
                            <tr>
                                <th scope="row">
                                    <label for="recaptcha_v2_wporlogin"><?php _e('Google reCAPTCHA', 'wporlogin'); ?></label>
                                </th>
                                <td> 
                                    <input name="recaptcha_v2_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option( 'recaptcha_v2_wporlogin' ) ); ?> id="recaptcha_v2_wporlogin" />
                                    <label for="recaptcha_v2_wporlogin"><?php _e('Versíon 2', 'wporlogin'); ?></label>
                                    <br><br>
                                    <!--Web Site de Google reCAPTCHA-->
                                    <p><?php _e('Registra tu nombre de dominio en el servicio de Google reCAPTCHA y luego añade las claves en los siguientes campos', 'wporlogin'); ?>.</p>
                                    <p><?php _e('Haz clic aquí para', 'wporlogin'); ?> <a href="https://www.google.com/recaptcha/admin" target="_blank"><?php _e('registrar tu dominio', 'wporlogin'); ?></a></p>
                                </td>
                            </tr>

                            <!--Clave del Sitio reCAPTCHA-->
                            <tr>
                                <th scope="row">
                                    <label for="recaptcha_v2_site_key_wporlogin"><?php _e('Clave del sitio', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="recaptcha_v2_site_key_wporlogin" type="text" name="recaptcha_v2_site_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v2_site_key_wporlogin')); ?>"/><br>                                    
                                </td>
                            </tr>

                            <!--Clave Secreta reCAPTCHA-->
                            <tr>                            
                                <th scope="row">
                                    <label for="recaptcha_v2_secret_key_wporlogin"><?php _e('Clave secreta', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="recaptcha_v2_secret_key_wporlogin" type="text" name="recaptcha_v2_secret_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v2_secret_key_wporlogin')); ?>"/><br>                                    
                                </td>
                            </tr>

                        </tbody>
                    </table> 

                    <!--Activar reCAPTCHA para-->
                    <div style="margin-top: 40px; border-top: 1px solid #e5e7e8; padding-bottom: 10px; padding-top: 30px;">
                        <table class="form-table" role="presentation">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="activar_recaptcha_v2_wporlogin"><?php _e('Activar reCAPTCHA para', 'wporlogin'); ?></label>                                    
                                    </th>
                                    <td>

                                        <div style="margin-bottom: 8px;">                                            
                                            <input name="activa_acceso_recaptcha_v2_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option( 'activa_acceso_recaptcha_v2_wporlogin' ) ); ?> id="activa_acceso_recaptcha_v2_wporlogin"/>
                                            <label for="activa_acceso_recaptcha_v2_wporlogin"><?php _e('Formulario de acceso', 'wporlogin'); ?></label>
                                        </div>

                                        <div style="margin-bottom: 8px;">
                                            <input name="activa_registro_recaptcha_v2_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option( 'activa_registro_recaptcha_v2_wporlogin' ) ); ?> id="activa_registro_recaptcha_v2_wporlogin"/>
                                            <label for="activa_registro_recaptcha_v2_wporlogin"><?php _e('Formulario de registro', 'wporlogin'); ?></label>
                                        </div>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!--END Activar reCAPTCHA para-->

                </div><!--END Content Google reCAPTCHA-->
                
                <!--BEGIN BOTÓN DE DONACIÓN CON PAYPAL-->
                <div style="margin-top: 40px; border-bottom: solid 1px #005d8c; border-top: 1px #005d8c solid;">
                    <div style="width: 90%; margin-left: auto; margin-right: auto;">
                        
                        <table class="form-table">                
                            <tbody>

                                <tr>
                                    <th scope="row"><label><?php _e('Donate now', 'wporlogin') ?></label></th>
                                    <td>
                                        <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CTG69VCQ5TZZN&source=url">
                                            <img src="<?php echo esc_url('https://www.paypalobjects.com/'.__('en_US', 'wporlogin').'/i/btn/btn_donate_SM.gif'); ?>">
                                        </a>
                                        <p class="description" id="tagline-description">
                                            <strong><?php _e('Important', 'wporlogin'); ?>: </strong><?php _e('If you value my work, considered a small donation to show your appreciation. Thank you!', 'wporlogin'); ?>
                                        </p>
                                        <p class="description" id="tagline-description"><?php _e('To donate now, click the Donate button', 'wporlogin'); ?></p>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        
                    </div>
                </div><!--END BOTÓN DE DONACIÓN CON PAYPAL-->
                
            </div>
            
            <?php submit_button(); ?>
            
        </form>

                
        </div>

    </div> <?php

}

/*
 * Function para agregar contenido HTML en la página del Plugin WPOrLogin
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
        
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><strong>WPOrLogin</strong> <?php _e('allows you to modify the appearance of the WordPress login page', 'wporlogin'); ?></p>
    
        
        <?php settings_errors();//Muestra los mensajes de éxito o de error cuando se envía el formulario ?>
                
        
        <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
            
            <?php 
            //Para proteger formularios
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
                 * BEGIN: Select Option WP-OrLogin Design
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
                
                <?php  //END: Select Option WP-OrLogin Design ?>    
                
                <!--BEGIN DESIGN BASIC-->                
                <div style="background-color: #AEB6BF; padding-top: 50px; padding-bottom: 50px; <?php if(get_option('wporlogin_design') != 'wporlogin_design_basic'){ echo 'display: none'; } ?>" class="wporlogin-container-design" id="wporlogin-container-basic">
                    
                    <div style="text-align: center;">
                        <img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'../img/wporlogin-design-basic.jpg'); ?>" style="box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); max-width: 100%; height: auto;">
                    </div>
                    
                </div><!--END DESIGN BASIC-->

                <!--BEGIN DESIGN STANDARD-->                
                <div style="background-color: #AEB6BF; padding-top: 50px; padding-bottom: 50px; <?php if(get_option('wporlogin_design') != 'wporlogin_design_standard'){ echo 'display: none'; } ?>"  class="wporlogin-container-design" id="wporlogin-container-standard">
                    
                    <div style="text-align: center;">
                        <img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'../img/wporlogin-design-standard.jpg'); ?>" style="box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); max-width: 100%; height: auto;">
                    </div>
                    
                </div><!--END DESIGN STANDARD-->

                <!--BEGIN DESIGN PREMIUM-->                
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
                    
                </div><!--END DESIGN PREMIUM-->
                
                
                <!--BEGIN DESIGN STANDARD AND DESIGN PREMIUM-->
                <div class="wporlogin-container-design-premium" id="wporlogin-container-standard-premium" style="padding-top: 40px; <?php if(get_option('wporlogin_design') != 'wporlogin_design_standard' && get_option('wporlogin_design') != 'wporlogin_design_premium'){ echo 'display: none;'; } ?> width: 90%; margin-left: auto; margin-right: auto;">
                    
                    <table class="form-table" role="presentation">

                        <tbody>

                        <!--HEAD del LOGOTIPO-->
                            <tr>
                                <th scope="row">
                                    <label style="font-size: 1.5em;"><strong>Logo</strong></label>
                                </th>
                                <td><hr></td>
                            </tr>

                            <!--URL del LOGOTIPO-->
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
                                    <input id="wporlogin_url_logotipo_button" type="button" class="button" value="<?php _e('Select the logo', 'wporlogin'); ?>" />
                                    <p class="description" id="tagline-description"><?php _e('You can upload your logo from here', 'wporlogin'); ?>.</p>
                                    <p class="description" id="tagline-description"><?php _e("I would suggest a max width of", 'wporlogin'); ?> <strong><?php _e('300 pixels', 'wporlogin'); ?>.</strong></p>
                                </td>
                            </tr>

                            <!--Image width-->
                            <tr>
                                <th scope="row">
                                    <label for="wporlogin_width_logotipo_text"><?php _e('Logo width', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="wporlogin_width_logotipo_text" type="number" name="wporlogin_width_logotipo_text" class="small-text" value="<?php echo esc_html(get_option('wporlogin_width_logotipo_text')); ?>"/>
                                    <span class="description" id="tagline-description"><?php _e('Ingrese la anchura deseada del logotipo', 'wporlogin'); ?>.</span>
                                </td>
                            </tr>

                             <!--Image height-->
                             <tr>
                                <th scope="row">
                                    <label for="wporlogin_height_logotipo_text"><?php _e('Logo height', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="wporlogin_height_logotipo_text" type="number" name="wporlogin_height_logotipo_text" class="small-text" value="<?php echo esc_html(get_option('wporlogin_height_logotipo_text')); ?>"/>
                                    <span class="description" id="tagline-description"><?php _e('Ingrese la altura deseada del logotipo', 'wporlogin'); ?>.</span>
                                </td>
                            </tr>

                            <!--Background Position-->
                            <tr>
                                <th scope="row">
                                    <label for="wporlogin_background_position_logotipo_select"><?php _e('Logo position', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <select name="wporlogin_background_position_logotipo_select" id="wporlogin_background_position_logotipo_select" class="regular">
                                        <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '0'); ?> value="0"><?php _e('left top', 'wporlogin'); ?></option>
                                        <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '1'); ?> value="1"><?php _e('left center', 'wporlogin'); ?></option>
                                        <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '2'); ?> value="2"><?php _e('left bottom', 'wporlogin'); ?></option>
                                        <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '3'); ?> value="3"><?php _e('right top', 'wporlogin'); ?></option>
                                        <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '4'); ?> value="4"><?php _e('right center', 'wporlogin'); ?></option>
                                        <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '5'); ?> value="5"><?php _e('right bottom', 'wporlogin'); ?></option>
                                        <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '6'); ?> value="6"><?php _e('center top', 'wporlogin'); ?></option>
                                        <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '7'); ?> value="7"><?php _e('center center', 'wporlogin'); ?></option>  
                                        <option <?php selected(get_option('wporlogin_background_position_logotipo_select'), '8'); ?> value="8"><?php _e('center bottom', 'wporlogin'); ?></option>                                       
                                    </select>
                                    <span class="description" id="tagline-description"><?php _e('Define la posición inicial de la imagen de fondo', 'wporlogin'); ?>.</span>
                                </td>
                            </tr>

                            <!--Background Size-->
                            <tr>
                                <th scope="row">
                                    <label for="wporlogin_background_size_logotipo_select"><?php _e('Background size', 'wporlogin'); ?></label>
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

                            <!--Ruta de URL en el LOGOTIPO-->
                            <tr>
                                <th scope="row">
                                    <label for="wporlogin_ruta_url_logotipo_text"><?php _e('Logo URL', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="wporlogin_ruta_url_logotipo_text" type="text" name="wporlogin_ruta_url_logotipo" class="regular-text" placeholder="https://example.com" value="<?php echo esc_html(get_option('wporlogin_ruta_url_logotipo')); ?>"/><br>
                                    <p class="description" id="tagline-description"><?php _e('Change the login logo URL', 'wporlogin'); ?>.</p>
                                </td>
                            </tr>

                            <!--Titulo del LOGOTIPO-->
                            <tr>                            
                                <th scope="row">
                                    <label for="wporlogin_titulo_logotipo_text"><?php _e('Logo Title', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="wporlogin_titulo_logotipo_text" type="text" name="wporlogin_titulo_logotipo" class="regular-text" value="<?php echo esc_html(get_option('wporlogin_titulo_logotipo')); ?>"/><br>
                                    <p class="description" id="tagline-description"><?php _e('Change the logo title in login', 'wporlogin'); ?>.</p>
                                </td>
                            </tr>


                        <!--BACKGROUND IMAGE-->
                        <tr>
                            <th scope="row">
                                <label style="font-size: 1.5em;"><strong><?php _e('Background image', 'wporlogin'); ?></strong></label>
                            </th>
                            <td><hr></td>
                        </tr>


                            <!--URL de IMG de fondo-->
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
                
                
                                    <!--BEGIN BACKGROUND MY IMAGE-->
                                    <div id="wporlogin-container-background-my-image" style="<?php if( (get_option('wporlogin_background_images') == 'wporlogin_free_images' )){ echo 'display: none;'; } ?>">
                                        <?php
                                        if(esc_url(get_option('wporlogin_url_img_fondo'))){ ?>

                                            <img id="wporlogin_url_img_fondo_img" src="<?php echo esc_url(get_option('wporlogin_url_img_fondo')); ?>" style="margin-bottom: 10px; width: 220px; padding: 10px; background-color: #ffffff; border: 2px dashed rgba(0,0,0,.1);"><br>
                                            
                                        <?php } ?>

                                        <input id="wporlogin_url_img_fondo_text" type="text" name="wporlogin_url_img_fondo" class="regular-text" style="margin-bottom: 10px;" value="<?php echo esc_html(get_option('wporlogin_url_img_fondo')); ?>"/><br>
                                        <input id="wporlogin_url_img_fondo_button" type="button" class="button" value="<?php _e('Select background image', 'wporlogin'); ?>" />
                                        <p class="description" id="tagline-description"><?php _e('You can upload a background image from here', 'wporlogin'); ?>.</p>
                                        <p class="description" id="tagline-description"><?php _e("You'll get the best results when using images with a dimension of 1920 by 1080 pixels", 'wporlogin'); ?>.</strong></p>
                                    </div><!--END BACKGROUND MY IMAGE-->                                    
                                    
                                    
                                    
                                    <!--BEGIN BACKGROUND FREE IMAGES-->
                                    <div id="wporlogin-container-background-free-image" style="<?php if( get_option('wporlogin_background_images') == 'wporlogin_my_images' ){ echo 'display: none;'; } ?>">

                                        <p class="description"><?php _e('You can select a background image from here', 'wporlogin'); ?></p>
                                        <p class="description"><?php _e('The images of ', 'wporlogin'); ?><a href="https://unsplash.com/" target="_blank"><strong>Unsplash</strong></a><?php _e(' and ', 'wporlogin'); ?><a href="https://pixabay.com/" target="_blank"><strong>Pixabay</strong></a><?php _e(' are made to be used freely', 'wporlogin'); ?>.</p><br><br>

                                        <div style="overflow: hidden;">
                                        
                                            <?php for($i=0; $i<14; $i++){ ?>

                                            <div style="float: left;">

                                                <input type="radio" id="wporlogin-background-free-image-<?php echo $i; ?>" name="wporlogin-background-free-image" value="<?php echo esc_url(WPORLOGINBACKGROUNDIMAGE[$i]); ?>" <?php if($i == 0){ if( get_option('wporlogin-background-free-image') != false){ checked( get_option('wporlogin-background-free-image'), WPORLOGINBACKGROUNDIMAGE[$i]); } else { echo 'checked'; } } else { checked( get_option('wporlogin-background-free-image'), WPORLOGINBACKGROUNDIMAGE[$i]); } ?>>
                                                <label for="wporlogin-background-free-image-<?php echo $i; ?>">Image <?php echo $i+1; ?></label>
                                                <div style="padding-top: 10px; margin-right: 15px;">
                                                    <img id="wporlogin_url_img_fondo_img" src="<?php echo esc_url(WPORLOGINBACKGROUNDIMAGE[$i]); ?>" style="margin-bottom: 10px; width: 220px; padding: 10px; background-color: #ffffff; border: 2px dashed rgba(0,0,0,.1);"><br>
                                                </div>

                                            </div>

                                            <?php } ?>
                                        </div>
                                        <p><?php _e('You can download more images from ', 'wporlogin'); ?><a href="https://unsplash.com/" target="_blank"><strong><?php _e('here', 'wporlogin'); ?></strong></a><?php _e(' totally free', 'wporlogin'); ?>.</p>
                                    
                                    </div>    
                                    
                                </td>
                            </tr>  
                        </tbody>
                    </table>  
                </div><!--END DESIGN STANDARD-->
                
                <!--BEGIN BOTÓN DE DONACIÓN CON PAYPAL-->
                <div style="margin-top: 40px; border-bottom: solid 1px #005d8c; border-top: 1px #005d8c solid;">
                    <div style="width: 90%; margin-left: auto; margin-right: auto;">
                        
                        <table class="form-table">                
                            <tbody>

                                <tr>
                                    <th scope="row"><label><?php _e('Donate now', 'wporlogin') ?></label></th>
                                    <td>
                                        <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CTG69VCQ5TZZN&source=url">
                                            <img src="<?php echo esc_url('https://www.paypalobjects.com/'.__('en_US', 'wporlogin').'/i/btn/btn_donate_SM.gif'); ?>">
                                        </a>
                                        <p class="description" id="tagline-description">
                                            <strong><?php _e('Important', 'wporlogin'); ?>: </strong><?php _e('If you value my work, considered a small donation to show your appreciation. Thank you!', 'wporlogin'); ?>
                                        </p>
                                        <p class="description" id="tagline-description"><?php _e('To donate now, click the Donate button', 'wporlogin'); ?></p>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        
                    </div>
                </div><!--END BOTÓN DE DONACIÓN CON PAYPAL-->
                
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

    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'recaptcha_v2_wporlogin');
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'recaptcha_v2_site_key_wporlogin');
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'recaptcha_v2_secret_key_wporlogin');

    /* 
    * AGREGAR valor predeterminado
    */
    add_option( 'activa_acceso_recaptcha_v2_wporlogin', '1' );
    add_option( 'activa_registro_recaptcha_v2_wporlogin', '1' );

    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'activa_acceso_recaptcha_v2_wporlogin');
    register_setting( 'recaptcha_wporlogin_custom_admin_settings_group', 'activa_registro_recaptcha_v2_wporlogin');
    
}
add_action('admin_init','recaptcha_wporlogin_register_options_admin_page');


//remove_language_wporlogin
function remove_language_wporlogin_register_options_admin_page(){

    register_setting( 'remove_language_wporlogin_custom_admin_settings_group', 'remove_language_wporlogin');

}
add_action('admin_init','remove_language_wporlogin_register_options_admin_page');







