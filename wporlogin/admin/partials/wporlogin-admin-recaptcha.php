<?php
/**
 * Vista para la configuración de Google reCAPTCHA.
 */
?>

<div class="wrap"> 
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo WPORLOGIN_URL . 'assets/img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
    </div>

    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;">
            <strong><?php _e('Google reCAPTCHA', 'wporlogin'); ?></strong>
        </h1>
        
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;">
            <?php _e('Protect your website from bots and unauthorized access with <strong>reCAPTCHA v2</strong> or <strong>v3</strong>.', 'wporlogin'); ?>
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
                        <span><?php _e('Do you need help? ', 'wporlogin'); ?><a href="https://youtu.be/U5x6FE5rre0" target="_blank"> <?php _e('Watch the video', 'wporlogin'); ?></a></span>
                    </div>
                    
                    <table class="form-table" role="presentation">
                        <tbody>

                            <tr style="border-bottom: 1px solid #e5e7e8;">
                                <th scope="row">
                                    <label for="recaptcha_version_wporlogin"><?php _e('Google reCAPTCHA version', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <select name="recaptcha_version_wporlogin" id="recaptcha_version_wporlogin">
                                        <option value="none" <?php selected($recaptcha_version, 'none'); ?>><?php _e('Google reCAPTCHA disabled', 'wporlogin'); ?></option>
                                        <option value="v2" <?php selected($recaptcha_version, 'v2'); ?>><?php _e('Google reCAPTCHA v2', 'wporlogin'); ?></option>
                                        <option value="v3" <?php selected($recaptcha_version, 'v3'); ?>><?php _e('Google reCAPTCHA v3', 'wporlogin'); ?></option>
                                    </select>
                                    <p><?php _e('Select the Google reCAPTCHA version you want to use.', 'wporlogin'); ?></p>
                                    <br>
                                    <p><?php _e("To use reCAPTCHA, first register your domain with Google's service, then enter the keys in the fields below.", 'wporlogin'); ?></p>
                                    <p><?php _e('<a href="https://www.google.com/recaptcha/admin" target="_blank">Click here to register your domain</a>', 'wporlogin'); ?></a></p>
                                </td>
                            </tr>

                            <tr class="wporlogin-recaptcha-v2-fields">
                                <th scope="row">
                                    <label for="wporlogin_recaptcha_v2_site_key"><?php _e('Site key (v2)', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="wporlogin_recaptcha_v2_site_key" type="text" name="recaptcha_v2_site_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v2_site_key_wporlogin')); ?>" />
                                </td>
                            </tr>
                            <tr class="wporlogin-recaptcha-v2-fields" style="border-bottom: 1px solid #e5e7e8;">
                                <th scope="row">
                                    <label for="wporlogin_recaptcha_v2_secret_key"><?php _e('Secret key (v2)', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="wporlogin_recaptcha_v2_secret_key" type="text" name="recaptcha_v2_secret_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v2_secret_key_wporlogin')); ?>" />
                                </td>
                            </tr>

                            <tr class="wporlogin-recaptcha-v3-fields">
                                <th scope="row">
                                    <label for="wporlogin_recaptcha_v3_site_key"><?php _e('Site key (v3)', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="wporlogin_recaptcha_v3_site_key" type="text" name="recaptcha_v3_site_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v3_site_key_wporlogin')); ?>" />
                                </td>
                            </tr>
                            <tr class="wporlogin-recaptcha-v3-fields" style="border-bottom: 1px solid #e5e7e8;">
                                <th scope="row">
                                    <label for="wporlogin_recaptcha_v3_secret_key"><?php _e('Secret key (v3)', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input id="wporlogin_recaptcha_v3_secret_key" type="text" name="recaptcha_v3_secret_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v3_secret_key_wporlogin')); ?>" />
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="activar_recaptcha_wporlogin"><?php _e('Enable reCAPTCHA for:', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input name="activa_acceso_recaptcha_v2_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option('activa_acceso_recaptcha_v2_wporlogin')); ?> id="activa_acceso_recaptcha_v2_wporlogin"/>
                                    <label for="activa_acceso_recaptcha_v2_wporlogin"><?php _e('Login form', 'wporlogin'); ?></label><br>

                                    <input name="activa_registro_recaptcha_v2_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option('activa_registro_recaptcha_v2_wporlogin')); ?> id="activa_registro_recaptcha_v2_wporlogin"/>
                                    <label for="activa_registro_recaptcha_v2_wporlogin"><?php _e('Registration form', 'wporlogin'); ?></label><br>
                                            
                                    <input name="activa_recuperar_recaptcha_wporlogin" type="checkbox" value="1" <?php checked('1', get_option('activa_recuperar_recaptcha_wporlogin')); ?> id="activa_recuperar_recaptcha_wporlogin"/>
                                    <label for="activa_recuperar_recaptcha_wporlogin"><?php _e('Password recovery form', 'wporlogin'); ?></label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>

                <?php
                if ( file_exists( WPORLOGIN_PATH . 'includes/templates/wporlogin-paypal-done.php' ) ) {
                    include( WPORLOGIN_PATH . 'includes/templates/wporlogin-paypal-done.php' ); 
                }
                ?>
                </div>

            <?php submit_button(); ?>
        </form>
    </div>
</div>
