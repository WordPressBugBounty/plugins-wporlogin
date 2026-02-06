<div class="wrap"> 
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo WPORLOGIN_URL . 'assets/img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
    </div>

    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
    
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Session Redirection Settings', 'wporlogin'); ?></strong></h1>  
    
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;">
            <?php _e('Control exactly where users go when they log in or log out.', 'wporlogin'); ?>
        </p>
 
        <?php settings_errors(); ?>

        <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
        
            <?php 
            wp_nonce_field(basename(__FILE__), 'redirects_wporlogin_form_nonce'); 
            settings_fields( 'redirects_wporlogin_custom_admin_settings_group' ); 
            do_settings_sections( 'redirects_wporlogin_custom_admin_settings_group' ); 
            ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); margin-bottom: 30px;">
                        
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">

                    <div style="border-bottom: 1px solid #e5e7e8; padding-bottom: 15px; padding-top: 10px;">
                        <span><?php _e('Need help? ', 'wporlogin'); ?><a href="https://www.youtube.com/watch?v=AkT8zoTF-jA" target="_blank"><?php _e('Watch the video', 'wporlogin'); ?></a></span>
                    </div>
                                        
                    <h2 class="title"><?php _e('Redirect After Login', 'wporlogin'); ?></h2>
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><?php _e('Enable redirection', 'wporlogin'); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_enable_login_redirect" style="font-weight: 600;">
                                            <input type="checkbox" id="wporlogin_enable_login_redirect" name="wporlogin_enable_login_redirect" value="1" <?php checked(get_option('wporlogin_enable_login_redirect'), '1'); ?> />
                                            <?php _e('Yes, enable custom redirection after login', 'wporlogin'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row" style="color: #d63638;"><?php _e('Admin Protection', 'wporlogin'); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_enable_admin_redirect">
                                            <input type="checkbox" id="wporlogin_enable_admin_redirect" name="wporlogin_enable_admin_redirect" value="1" <?php checked(get_option('wporlogin_enable_admin_redirect'), '1'); ?> />
                                            <?php _e('Apply redirection to Administrators too', 'wporlogin'); ?>
                                        </label>
                                        <p class="description" style="color: #666;">
                                            <?php _e('<b>Recommendation:</b> Keep unchecked to let admins access the Dashboard directly.', 'wporlogin'); ?>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e('Default Redirect URL', 'wporlogin'); ?></th>
                                <td>
                                    <input type="text" name="wporlogin_login_redirect" value="<?php echo esc_attr(get_option('wporlogin_login_redirect')); ?>" class="regular-text" style="width: 100%; max-width: 500px;" placeholder="<?php echo home_url('/welcome'); ?>" />
                                    <p class="description"><?php _e('Destination for all users, unless a specific Role Rule applies below.', 'wporlogin'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e('Role-Based Rules', 'wporlogin'); ?></th>
                                <td>
                                    <details <?php echo (!empty(get_option('wporlogin_redirect_roles_rules'))) ? 'open' : ''; ?> style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; border-radius: 4px; max-width: 550px;">
                                        <summary style="cursor: pointer; font-weight: 600; color: #0073aa; outline: none;">
                                            <?php _e('Configure specific URLs by User Role', 'wporlogin'); ?>
                                        </summary>
                                        
                                        <p class="description" style="margin-top: 10px; margin-bottom: 15px;">
                                            <?php _e('Enter a full URL (e.g., https://site.com/member) or a relative path (e.g., /member). Leave empty to use the Default URL.', 'wporlogin'); ?>
                                        </p>

                                        <?php 
                                        global $wp_roles;
                                        $all_roles = $wp_roles->roles;
                                        $saved_rules = get_option('wporlogin_redirect_roles_rules', array());

                                        if(isset($all_roles['administrator'])) {
                                            $admin_role = $all_roles['administrator'];
                                            unset($all_roles['administrator']);
                                            $all_roles = array_merge(array('administrator' => $admin_role), $all_roles);
                                        }

                                        foreach ( $all_roles as $role_key => $role_data ) : 
                                            $role_name = translate_user_role( $role_data['name'] );
                                            $value = isset( $saved_rules[$role_key] ) ? $saved_rules[$role_key] : '';
                                        ?>
                                            <div style="margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
                                                <label style="font-weight: 500; min-width: 140px; font-size: 13px;"><?php echo esc_html( $role_name ); ?>:</label>
                                                <input type="text" 
                                                       name="wporlogin_redirect_roles_rules[<?php echo esc_attr($role_key); ?>]" 
                                                       value="<?php echo esc_attr( $value ); ?>" 
                                                       class="regular-text" 
                                                       style="width: 100%;"
                                                       placeholder="<?php _e('Default URL', 'wporlogin'); ?>" 
                                                />
                                            </div>
                                        <?php endforeach; ?>
                                    </details>
                                </td>
                            </tr>

                        </tbody>
                    </table>

                    <h2 class="title" style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px;"><?php _e('Redirect on Logout', 'wporlogin'); ?></h2>
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><?php _e('Enable redirection', 'wporlogin'); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_enable_logout_redirect" style="font-weight: 600;">
                                            <input type="checkbox" id="wporlogin_enable_logout_redirect" name="wporlogin_enable_logout_redirect" value="1" <?php checked(get_option('wporlogin_enable_logout_redirect'), '1'); ?> />
                                            <?php _e('Yes, enable custom redirection after logout', 'wporlogin'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Logout Redirect URL', 'wporlogin'); ?></th>
                                <td>
                                    <input type="text" name="wporlogin_logout_redirect" value="<?php echo esc_attr(get_option('wporlogin_logout_redirect')); ?>" class="regular-text" style="width: 100%; max-width: 500px;" placeholder="<?php echo home_url('/'); ?>" />
                                    <p class="description"><?php _e('Where to send users after they sign out (e.g., Home page, Login page).', 'wporlogin'); ?></p>
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

