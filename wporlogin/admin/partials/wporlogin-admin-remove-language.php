<?php
/**
 * Vista para la subpágina "Remove Language".
 */
?>

<div class="wrap"> 
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo WPORLOGIN_URL . 'assets/img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
    </div>

    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
    
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Remove language selector', 'wporlogin'); ?></strong></h1>  
    
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('Hide or remove the language selector from the login page.', 'wporlogin'); ?></p>
 
    
        <?php settings_errors(); ?>

        <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
        
            <?php 
            wp_nonce_field(basename(__FILE__), 'remove_language_form_nonce'); 
            ?>
            
            <?php settings_fields( 'remove_language_wporlogin_custom_admin_settings_group' ); ?>
            <?php do_settings_sections( 'remove_language_wporlogin_custom_admin_settings_group' ); ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
                        
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">

                    <div style="border-bottom: 1px solid #e5e7e8; padding-bottom: 15px; padding-top: 10px;">
                        <span><?php _e('Need help? ', 'wporlogin'); ?><a href="https://www.youtube.com/watch?v=WeDN4tx2_8k" target="_blank"><?php _e('Watch the video', 'wporlogin'); ?></a></span>
                    </div>
                                        
                    <table class="form-table" role="presentation">

                        <tbody>

                            <tr>
                                <th scope="row">
                                    <label for="remove_language_wporlogin"><?php _e('Delete', 'wporlogin'); ?></label>
                                </th>
                                <td> 
                                    <input name="remove_language_wporlogin" type="checkbox" value="1" <?php checked( '1', (int) get_option( 'remove_language_wporlogin', 0 ) ); ?> id="remove_language_wporlogin" />
                                    <label for="remove_language_wporlogin"><?php _e('Remove the dropdown language selector from the login page.', 'wporlogin'); ?></label>
                                </td>
                            </tr>

                        </tbody>
                    </table> 
                </div>

                <?php
                // CORREGIDO: Ruta segura y chequeo de existencia
                if ( file_exists( WPORLOGIN_PATH . 'includes/templates/wporlogin-paypal-done.php' ) ) {
                    include( WPORLOGIN_PATH . 'includes/templates/wporlogin-paypal-done.php' ); 
                }
                ?>
                </div>
            
            <?php submit_button(); ?>
            
        </form>

            
    </div>

</div>