<?php
/**
 * Vista para la subpágina de Social Login.
 */
?>

<div class="wrap"> 
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo WPORLOGIN_URL . 'assets/img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
    </div>

    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
    
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;">
            <strong><?php _e('Social Login', 'wporlogin'); ?></strong>
        </h1>  

        <?php 
        // Verificamos si el registro está desactivado
        if ( ! get_option( 'users_can_register' ) ) : 
        ?>
            <div class="notice notice-warning inline" style="border-left-color: #f0ad4e; padding: 10px 15px; margin-bottom: 20px; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                <p style="margin: 0; color: #8a6d3b; font-weight: 500;">
                    <span class="dashicons dashicons-warning" style="vertical-align: text-bottom; margin-right: 5px;"></span>
                    <strong><?php _e( 'Attention:', 'wporlogin' ); ?></strong> 
                    <?php _e( 'User registration is currently disabled in WordPress Settings.', 'wporlogin' ); ?>
                </p>
                <p style="margin: 5px 0 0 25px; color: #666;">
                    <?php _e( 'Existing users can still log in with Google, but <strong>new users cannot create accounts</strong>. To change this, go to Settings > General and check "Anyone can register".', 'wporlogin' ); ?>
                </p>
            </div>
        <?php endif; ?>
    
        <p style="margin-bottom: 40px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px;">
            <?php _e('Allow users to log in and register using their social media accounts.', 'wporlogin'); ?>
        </p>
 
        <?php settings_errors(); ?>

        <form method="post" action="options.php">
        
            <?php 
                // Esto debe coincidir con el grupo que registraste en register_settings
                settings_fields( 'wporlogin_social_settings_group' ); 
                do_settings_sections( 'wporlogin_social_settings_group' );
            ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); margin-bottom: 30px;">
                        
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">

                    <h2 class="title" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                        <span class="dashicons dashicons-google" style="font-size: 26px; width: 26px; height: 26px; vertical-align: middle; margin-right: 5px;"></span>
                        <?php _e('Google Settings', 'wporlogin'); ?>
                    </h2>

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><?php _e('Enable Google Login', 'wporlogin'); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_google_enable">
                                            <input type="checkbox" id="wporlogin_google_enable" name="wporlogin_google_enable" value="1" <?php checked(get_option('wporlogin_google_enable'), '1'); ?> />
                                            <?php _e('Yes, enable login with Google', 'wporlogin'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e('Client ID', 'wporlogin'); ?></th>
                                <td>
                                    <input type="text" name="wporlogin_google_client_id" value="<?php echo esc_attr(get_option('wporlogin_google_client_id')); ?>" class="regular-text" />
                                    <p class="description">
                                        <?php _e('Enter your Google Client ID.', 'wporlogin'); ?> 
                                        <a href="https://console.cloud.google.com/apis/credentials" target="_blank"><?php _e('Get it here', 'wporlogin'); ?></a>.
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e('Client Secret', 'wporlogin'); ?></th>
                                <td>
                                    <input type="password" name="wporlogin_google_client_secret" value="<?php echo esc_attr(get_option('wporlogin_google_client_secret')); ?>" class="regular-text" />
                                    <p class="description"><?php _e('Enter your Google Client Secret.', 'wporlogin'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e('Authorized Redirect URI', 'wporlogin'); ?></th>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <code id="wporlogin_callback_url" style="padding: 8px 10px; background: #f0f0f1; border-radius: 4px; border: 1px solid #ccc; color: #555;">
                                            <?php echo home_url( '/?wporlogin_social_auth=google' ); ?>
                                        </code>
                                        <button type="button" class="button button-secondary" onclick="copyToClipboard('#wporlogin_callback_url')">
                                            <span class="dashicons dashicons-admin-page" style="vertical-align: middle;"></span> <?php _e('Copy', 'wporlogin'); ?>
                                        </button>
                                        <span id="wporlogin_copy_msg" style="color: green; display: none; font-weight: bold;"><?php _e('Copied!', 'wporlogin'); ?></span>
                                    </div>

                                    <script>
                                    function copyToClipboard(element) {
                                        var $temp = jQuery("<input>");
                                        jQuery("body").append($temp);
                                        $temp.val(jQuery(element).text().trim()).select();
                                        document.execCommand("copy");
                                        $temp.remove();
                                        jQuery("#wporlogin_copy_msg").fadeIn().delay(1000).fadeOut();
                                    }
                                    </script>
                                    <p class="description">
                                        <?php _e('Copy this URL and paste it into the "Authorized redirect URIs" field in your Google Cloud Console.', 'wporlogin'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Sync Social Avatar', 'wporlogin'); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_social_avatar_sync">
                                            <input type="checkbox" id="wporlogin_social_avatar_sync" name="wporlogin_social_avatar_sync" value="1" <?php checked(get_option('wporlogin_social_avatar_sync'), '1'); ?> />
                                            <?php _e('Show user\'s Google profile picture instead of Gravatar.', 'wporlogin'); ?>
                                        </label>
                                        <p class="description">
                                            <?php _e('If enabled, the plugin will use the Google profile picture for users who logged in via Social Login.', 'wporlogin'); ?>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Social Registration Only', 'wporlogin'); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_social_only_register">
                                            <input type="checkbox" id="wporlogin_social_only_register" name="wporlogin_social_only_register" value="1" <?php checked(get_option('wporlogin_social_only_register'), '1'); ?> />
                                            <?php _e('Disable standard registration form', 'wporlogin'); ?>
                                        </label>
                                        <p class="description">
                                          <?php _e('If enabled, the username and email fields will be hidden on the registration page and users will register using only their Google account.', 'wporlogin'); ?>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            
            <?php submit_button(); ?>
            
        </form>
    </div>
</div>