<!-- Archivo: templates/recaptcha-settings-template.php -->
<div class="wrap"> 
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo esc_url( WPORLOGIN_PLUGIN_URL . 'img/logo-wporlogin.png' ); ?>" style="margin-left: 20px; height: 48px;">
    </div>

    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;">
            <strong><?php _e('Google reCAPTCHA', 'wporlogin'); ?></strong>
        </h1>
        
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;">
            <?php _e('Protege tu sitio con reCAPTCHA v2 o v3', 'wporlogin'); ?>
        </p>
        
        <?php settings_errors(); ?>

        <form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>">

            <?php 
                wp_nonce_field(basename(__FILE__), 'recaptcha_wporlogin_form_nonce'); 
                settings_fields('recaptcha_wporlogin_custom_admin_settings_group');
                do_settings_sections('recaptcha_wporlogin_custom_admin_settings_group');
            ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
                
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">
                    <div style="border-bottom: 1px solid #e5e7e8; padding-bottom: 15px; padding-top: 10px;">
                        <span><?php _e('¿Necesitas ayuda? ', 'wporlogin'); ?><a href="https://youtu.be/U5x6FE5rre0" target="_blank"> <?php _e('Ver el vídeo', 'wporlogin'); ?></a></span>
                    </div>
                    
                    <!-- Aquí va el contenido del formulario de reCAPTCHA -->
                    <?php do_action('wporlogin_render_recaptcha_form'); ?>

                </div><!--FIN Contenido Google reCAPTCHA-->

                <!--INICIO BOTÓN DE DONACIÓN CON PAYPAL--><?php
                
                // Incluir la plantilla HTML desde el directorio de plantillas
                include(WPORLOGIN_PLUGIN_PATH . 'includes/templates/wporlogin-paypal-done.php'); ?>

                <!--FIN BOTÓN DE DONACIÓN CON PAYPAL-->

            </div>

            <?php submit_button(); ?>
        </form>
    </div>
</div>

