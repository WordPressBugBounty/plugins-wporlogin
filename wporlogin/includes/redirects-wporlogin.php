<?php

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente.
}

// Función para redirigir al iniciar sesión
function wporlogin_custom_login_redirect($redirect_to, $request, $user) {
    if (is_a($user, 'WP_User')) {
        // Verificar si la redirección de login está activada
        $redirect_enabled = get_option('wporlogin_enable_login_redirect');
        $redirect_admin_enabled = get_option('wporlogin_enable_admin_redirect');
        $custom_redirect = get_option('wporlogin_login_redirect');

        // Si la opción está activada y hay una URL personalizada
        if ($redirect_enabled == '1' && !empty($custom_redirect)) {
            // Si el usuario es administrador y la opción de redirección para admins está desactivada, enviarlo al dashboard
            if (in_array('administrator', $user->roles) && $redirect_admin_enabled != '1') {
                return admin_url();
            }
            // Si la opción está activada para admins, redirigir a la página personalizada
            return esc_url($custom_redirect);
        }
    }
    
    return admin_url(); // Redirección por defecto si no hay URL personalizada o la opción está desactivada.
}
add_filter('login_redirect', 'wporlogin_custom_login_redirect', 10, 3);

// Función para redirigir al cerrar sesión
function wporlogin_custom_logout_redirect() {
    $redirect_enabled = get_option('wporlogin_enable_logout_redirect');
    $custom_redirect = get_option('wporlogin_logout_redirect');

    if ($redirect_enabled == '1' && !empty($custom_redirect)) {
        wp_safe_redirect(esc_url($custom_redirect));
        exit;
    }
}
add_action('wp_logout', 'wporlogin_custom_logout_redirect');

function redirects_wporlogin_content_page_menu(){
    ?>
    
    <div class="wrap"> 
    
        <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
            <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
        </div>

        <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
        
            <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Redirecciones personalizadas', 'wporlogin'); ?></strong></h1>  
        
            <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('Permite redirigir a los usuarios a páginas personalizadas al iniciar y cerrar sesión en WordPress.', 'wporlogin'); ?></p>
    
        
            <?php settings_errors(); // Muestra los mensajes de éxito o de error cuando se envía el formulario ?>

            <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
            
            <?php 
            // Para proteger formularios
            wp_nonce_field(basename(__FILE__), 'redirects_wporlogin_form_nonce'); 
            ?>
            
            <?php settings_fields( 'redirects_wporlogin_custom_admin_settings_group' ); ?>
            <?php do_settings_sections( 'redirects_wporlogin_custom_admin_settings_group' ); ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
                        
                <!-- CONTENIDO -->
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">

                    <div style="border-bottom: 1px solid #e5e7e8; padding-bottom: 15px; padding-top: 10px;">
                        <span><?php _e('¿Necesitas ayuda? ', 'wporlogin'); ?><a href="#" target="_blank"><?php _e('Ver el video', 'wporlogin'); ?></a></span>
                    </div>
                                    
                    <!-- Sección de Redirección al Iniciar Sesión -->
            <h2 class="title"><?php _e('Redirección al Iniciar Sesión', 'wporlogin'); ?></h2>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><?php _e('Activar redirección al iniciar sesión', 'wporlogin'); ?></th>
                        <td>
                            <fieldset>
                                <label for="wporlogin_enable_login_redirect">
                                    <input type="checkbox" id="wporlogin_enable_login_redirect" name="wporlogin_enable_login_redirect" value="1" <?php checked(get_option('wporlogin_enable_login_redirect'), '1'); ?> />
                                    <?php _e('Sí, activar redirección', 'wporlogin'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('URL de redirección al iniciar sesión', 'wporlogin'); ?></th>
                        <td>
                            <input type="text" name="wporlogin_login_redirect" value="<?php echo esc_attr(get_option('wporlogin_login_redirect')); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Incluir administradores en la redirección', 'wporlogin'); ?></th>
                        <td>
                            <fieldset>
                                <label for="wporlogin_enable_admin_redirect">
                                    <input type="checkbox" id="wporlogin_enable_admin_redirect" name="wporlogin_enable_admin_redirect" value="1" <?php checked(get_option('wporlogin_enable_admin_redirect'), '1'); ?> />
                                    <?php _e('Sí, redirigir administradores también', 'wporlogin'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Separador -->
            <h2 class="title"><?php _e('Redirección al Cerrar Sesión', 'wporlogin'); ?></h2>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><?php _e('Activar redirección al cerrar sesión', 'wporlogin'); ?></th>
                        <td>
                            <fieldset>
                                <label for="wporlogin_enable_logout_redirect">
                                    <input type="checkbox" id="wporlogin_enable_logout_redirect" name="wporlogin_enable_logout_redirect" value="1" <?php checked(get_option('wporlogin_enable_logout_redirect'), '1'); ?> />
                                    <?php _e('Sí, activar redirección', 'wporlogin'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('URL de redirección al cerrar sesión', 'wporlogin'); ?></th>
                        <td>
                            <input type="text" name="wporlogin_logout_redirect" value="<?php echo esc_attr(get_option('wporlogin_logout_redirect')); ?>" class="regular-text" />
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

function redirects_wporlogin_register_options_admin_page(){

    register_setting( 'redirects_wporlogin_custom_admin_settings_group', 'wporlogin_login_redirect', array( 'sanitize_callback' => 'esc_url_raw' ) );
    register_setting( 'redirects_wporlogin_custom_admin_settings_group', 'wporlogin_logout_redirect', array( 'sanitize_callback' => 'esc_url_raw' ) );
    register_setting( 'redirects_wporlogin_custom_admin_settings_group', 'wporlogin_enable_admin_redirect', array( 'sanitize_callback' => 'absint' ));
    register_setting( 'redirects_wporlogin_custom_admin_settings_group', 'wporlogin_enable_login_redirect', array( 'sanitize_callback' => 'absint' ));
    register_setting( 'redirects_wporlogin_custom_admin_settings_group', 'wporlogin_enable_logout_redirect', array( 'sanitize_callback' => 'absint' ) );

}
add_action('admin_init','redirects_wporlogin_register_options_admin_page');
