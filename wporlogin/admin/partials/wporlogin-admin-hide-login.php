<?php
/**
 * Vista para la configuración de URLs Limpias (Hide Login).
 */
?>

<div class="wrap"> 
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo WPORLOGIN_URL . 'assets/img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
    </div>

    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
    
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Clean Login URLs', 'wporlogin'); ?></strong></h1>  
    
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('Customize your authentication URLs to improve security and branding.', 'wporlogin'); ?></p>
 
        <?php settings_errors(); ?>

        <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
        
            <?php 
                settings_fields( 'wporlogin_hide_login_settings_group' ); 
                do_settings_sections( 'wporlogin_hide_login_settings_group' );
            ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); margin-bottom: 30px;">
                        
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">

                    <div style="border-bottom: 1px solid #e5e7e8; padding-bottom: 15px; padding-top: 10px;">
                        <span><?php _e('Need help? ', 'wporlogin'); ?><a href="https://www.youtube.com/watch?v=7zKFE5EjEfE" target="_blank"><?php _e('Watch the video', 'wporlogin'); ?></a></span>
                    </div>
                                        
                    <h2 class="title" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <?php _e('1. Custom URL Settings', 'wporlogin'); ?>
                    </h2>
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><?php _e('Login URL', 'wporlogin'); ?></th>
                                <td>
                                    <code><?php echo home_url('/'); ?></code>
                                    <input type="text" name="wporlogin_hide_login_slug" value="<?php echo esc_attr( get_option( 'wporlogin_hide_login_slug' ) ); ?>" class="regular-text" placeholder="acceso" />
                                    <p class="description"><?php _e('Example: <code>access</code>, <code>login</code>, <code>enter</code>.', 'wporlogin'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e('Registration URL', 'wporlogin'); ?></th>
                                <td>
                                    <code><?php echo home_url('/'); ?></code>
                                    <input type="text" name="wporlogin_hide_register_slug" value="<?php echo esc_attr( get_option( 'wporlogin_hide_register_slug' ) ); ?>" class="regular-text" placeholder="registro" />
                                    <p class="description"><?php _e('Example: <code>sign-up</code>, <code>join-us</code>, <code>registro</code>.', 'wporlogin'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e('Lost Password URL', 'wporlogin'); ?></th>
                                <td>
                                    <code><?php echo home_url('/'); ?></code>
                                    <input type="text" name="wporlogin_hide_recovery_slug" value="<?php echo esc_attr( get_option( 'wporlogin_hide_recovery_slug' ) ); ?>" class="regular-text" placeholder="recuperar" />
                                    <p class="description"><?php _e('Example: <code>forgot-password</code>, <code>reset</code>, <code>recuperar</code>.', 'wporlogin'); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="background: #fff8e5; border-left: 4px solid #ffb900; padding: 10px 15px; margin-bottom: 25px;">
                        <p style="margin: 0; color: #555;">
                            <strong><?php _e('Important:', 'wporlogin'); ?></strong> 
                            <?php _e('Note: Leave any field empty to use the default WordPress URL for that page.', 'wporlogin'); ?>
                        </p>
                    </div>

                    <h2 class="title" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 0;">
                        <?php _e('2. Advanced Protection', 'wporlogin'); ?>
                    </h2>                    

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <?php _e('Redirect Strategy', 'wporlogin'); ?>
                                    <br>
                                    <small style="font-weight: normal; color: #666;">(wp-admin & wp-login.php)</small>
                                </th>
                                <td>
                                    <code><?php echo home_url('/'); ?></code>
                                    <input type="text" name="wporlogin_hide_wp_admin_redirect_slug" value="<?php echo esc_attr( get_option( 'wporlogin_hide_wp_admin_redirect_slug' ) ); ?>" class="regular-text" placeholder="404" />
                                    
                                    <p class="description" style="margin-top: 8px;">
                                        <?php _e('Choose where to send unauthorized users (bots/intruders).', 'wporlogin'); ?><br>
                                        <?php _e('Leave empty to use the <b>404 Error Page</b> (Recommended for security).', 'wporlogin'); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="background: #fff8e5; border-left: 4px solid #ffb900; padding: 10px 15px; margin-bottom: 25px;">
                        <p style="margin: 0; color: #555;">
                            <strong><?php _e('Important:', 'wporlogin'); ?></strong> 
                            <?php _e('These protection settings will <b>only activate</b> if you have set a custom <b>Login URL</b> in section 1.', 'wporlogin'); ?>
                        </p>
                    </div>

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