<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Vista para la subpágina de Social Login.
 */
?>

<div class="wrap"> 
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo esc_url( WPORLOGIN_URL . 'assets/img/logo-wporlogin.png' ); ?>" style="margin-left: 20px; height: 48px;">
    </div>

    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
    
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;">
            <strong><?php esc_html_e( 'Social Login', 'wporlogin' ); ?></strong>
        </h1>  

        <?php 
        // Verificamos si el registro está desactivado
        if ( ! get_option( 'users_can_register' ) ) : 
        ?>
            <div class="notice notice-warning inline" style="border-left-color: #f0ad4e; padding: 10px 15px; margin-bottom: 20px; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                <p style="margin: 0; color: #8a6d3b; font-weight: 500;">
                    <span class="dashicons dashicons-warning" style="vertical-align: text-bottom; margin-right: 5px;"></span>
                    <strong><?php esc_html_e( 'Attention:', 'wporlogin' ); ?></strong> 
                    <?php esc_html_e( 'User registration is currently disabled in WordPress Settings.', 'wporlogin' ); ?>
                </p>
                <p style="margin: 5px 0 0 25px; color: #666;">
                    <?php echo wp_kses_post( __( 'Existing users can still log in with configured social providers, but <strong>new users cannot create accounts</strong>. To change this, go to Settings > General and check "Anyone can register".', 'wporlogin' ) ); ?>
                </p>
            </div>
        <?php endif; ?>
    
        <p style="margin-bottom: 40px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px;">
            <?php esc_html_e( 'Allow users to log in and register using their social media accounts.', 'wporlogin' ); ?>
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
                        <?php esc_html_e( 'Google Settings', 'wporlogin' ); ?>
                    </h2>

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Google Login', 'wporlogin' ); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_google_enable">
                                            <input type="checkbox" id="wporlogin_google_enable" name="wporlogin_google_enable" value="1" <?php checked(get_option('wporlogin_google_enable'), '1'); ?> />
                                            <?php esc_html_e( 'Yes, enable login with Google', 'wporlogin' ); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php esc_html_e( 'Client ID', 'wporlogin' ); ?></th>
                                <td>
                                    <input type="text" name="wporlogin_google_client_id" value="<?php echo esc_attr(get_option('wporlogin_google_client_id')); ?>" class="regular-text" />
                                    <p class="description">
                                        <?php esc_html_e( 'Enter your Google Client ID.', 'wporlogin' ); ?> 
                                        <a href="https://console.cloud.google.com/apis/credentials" target="_blank"><?php esc_html_e( 'Get it here', 'wporlogin' ); ?></a>.
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php esc_html_e( 'Client Secret', 'wporlogin' ); ?></th>
                                <td>
                                    <input type="password" name="wporlogin_google_client_secret" value="<?php echo esc_attr(get_option('wporlogin_google_client_secret')); ?>" class="regular-text" />
                                    <p class="description"><?php esc_html_e( 'Enter your Google Client Secret.', 'wporlogin' ); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php esc_html_e( 'Authorized Redirect URI', 'wporlogin' ); ?></th>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <code id="wporlogin_callback_url" style="padding: 8px 10px; background: #f0f0f1; border-radius: 4px; border: 1px solid #ccc; color: #555;">
                                            <?php echo esc_html( home_url( '/?wporlogin_social_auth=google' ) ); ?>
                                        </code>
                                        <button type="button" class="button button-secondary" onclick="copyToClipboard('#wporlogin_callback_url')">
                                            <span class="dashicons dashicons-admin-page" style="vertical-align: middle;"></span> <?php esc_html_e( 'Copy', 'wporlogin' ); ?>
                                        </button>
                                        <span id="wporlogin_copy_msg" style="color: green; display: none; font-weight: bold;"><?php esc_html_e( 'Copied!', 'wporlogin' ); ?></span>
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
                                        <?php esc_html_e( 'Copy this URL and paste it into the "Authorized redirect URIs" field in your Google Cloud Console.', 'wporlogin' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            
            <!-- Facebook has independent activation and credentials. -->
            <div style="padding: 25px 0 35px; background: #fff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,.13); margin-bottom: 30px;">
                <div class="wporlogin-container-design" style="width:90%; margin:0 auto;">
                    <h2 class="title" style="border-bottom:1px solid #eee; padding-bottom:12px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                        <span style="background:#1877f2;color:#fff;border-radius:6px;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:22px;" aria-hidden="true">f</span>
                        <?php esc_html_e( 'Facebook Settings', 'wporlogin' ); ?>
                    </h2>
                    <table class="form-table"><tbody>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Enable Facebook Login', 'wporlogin' ); ?></th>
                            <td><label for="wporlogin_facebook_enable">
                                <input type="checkbox" id="wporlogin_facebook_enable" name="wporlogin_facebook_enable" value="1" <?php checked( get_option( 'wporlogin_facebook_enable' ), '1' ); ?> />
                                <?php esc_html_e( 'Yes, enable login with Facebook', 'wporlogin' ); ?>
                            </label></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wporlogin_facebook_app_id"><?php esc_html_e( 'App ID', 'wporlogin' ); ?></label></th>
                            <td><input type="text" id="wporlogin_facebook_app_id" name="wporlogin_facebook_app_id" value="<?php echo esc_attr( get_option( 'wporlogin_facebook_app_id', '' ) ); ?>" class="regular-text" autocomplete="off" />
                                <p class="description"><?php esc_html_e( 'Enter the App ID from Meta for Developers.', 'wporlogin' ); ?>
                                    <a href="https://developers.facebook.com/apps/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open Meta for Developers', 'wporlogin' ); ?></a>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wporlogin_facebook_app_secret"><?php esc_html_e( 'App Secret', 'wporlogin' ); ?></label></th>
                            <td><input type="password" id="wporlogin_facebook_app_secret" name="wporlogin_facebook_app_secret" value="<?php echo esc_attr( get_option( 'wporlogin_facebook_app_secret', '' ) ); ?>" class="regular-text" autocomplete="new-password" />
                                <p class="description"><?php esc_html_e( 'Keep this value private. It is only used for server-to-server requests to Meta.', 'wporlogin' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Facebook Redirect URL', 'wporlogin' ); ?></th>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <input id="wporlogin_fb_callback_url" class="regular-text code" style="min-width:min(100%,420px);" type="text" readonly value="<?php echo esc_attr( home_url( '/?wporlogin_social_auth=facebook' ) ); ?>" aria-label="<?php echo esc_attr__( 'Facebook OAuth Redirect URI', 'wporlogin' ); ?>" />
                                    <button type="button" class="button button-secondary" onclick="wporloginCopyFacebookUri()">
                                        <span class="dashicons dashicons-admin-page" style="vertical-align:middle;"></span> <?php esc_html_e( 'Copy', 'wporlogin' ); ?>
                                    </button>
                                    <span id="wporlogin_fb_copy_success" role="status" style="display:none;color:#16753a;"><?php esc_html_e( 'Copied!', 'wporlogin' ); ?></span>
                                </div>
                                <p class="description"><?php esc_html_e( 'Copy this URL into Valid OAuth Redirect URIs in your Facebook Login settings on Meta for Developers.', 'wporlogin' ); ?></p>
                                <p class="description"><?php esc_html_e( 'For public sign-in, use HTTPS and make sure your Meta app allows people outside your app team to log in.', 'wporlogin' ); ?></p>
                            </td>
                        </tr>
                    </tbody></table>
                </div>
            </div>
            <script>
            function wporloginCopyFacebookUri() {
                var field = document.getElementById('wporlogin_fb_callback_url');
                var feedback = document.getElementById('wporlogin_fb_copy_success');
                if (!field) { return; }
                function done() { if (feedback) { feedback.style.display = 'inline'; } }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(field.value).then(done).catch(function () {
                        field.select(); if (document.execCommand('copy')) { done(); }
                    });
                } else {
                    field.select(); if (document.execCommand('copy')) { done(); }
                }
            }
            </script>

            <!-- Independent Microsoft identity platform settings. -->
            <div style="padding:25px 0 35px;background:#fff;border-radius:10px;box-shadow:0 3px 6px rgba(0,0,0,.13);margin-bottom:30px;">
                <div class="wporlogin-container-design" style="width:90%;margin:0 auto;">
                    <h2 class="title" style="border-bottom:1px solid #eee;padding-bottom:12px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                        <span aria-hidden="true" style="display:inline-grid;grid-template-columns:repeat(2,10px);gap:2px;">
                            <span style="width:10px;height:10px;background:#f25022;"></span><span style="width:10px;height:10px;background:#7fba00;"></span>
                            <span style="width:10px;height:10px;background:#00a4ef;"></span><span style="width:10px;height:10px;background:#ffb900;"></span>
                        </span>
                        <?php esc_html_e( 'Microsoft Settings', 'wporlogin' ); ?>
                    </h2>
                    <table class="form-table"><tbody>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Enable Microsoft Login', 'wporlogin' ); ?></th>
                            <td><label for="wporlogin_microsoft_enable">
                                <input id="wporlogin_microsoft_enable" name="wporlogin_microsoft_enable" type="checkbox" value="1" <?php checked( get_option( 'wporlogin_microsoft_enable' ), '1' ); ?> />
                                <?php esc_html_e( 'Yes, enable login with Microsoft', 'wporlogin' ); ?>
                            </label></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wporlogin_microsoft_client_id"><?php esc_html_e( 'Application (client) ID', 'wporlogin' ); ?></label></th>
                            <td><input id="wporlogin_microsoft_client_id" name="wporlogin_microsoft_client_id" type="text" class="regular-text" autocomplete="off" value="<?php echo esc_attr( get_option( 'wporlogin_microsoft_client_id', '' ) ); ?>" />
                                <p class="description"><?php esc_html_e( 'Copy the Application (client) ID from your app registration in Microsoft Entra.', 'wporlogin' ); ?>
                                    <a href="https://entra.microsoft.com/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open Microsoft Entra', 'wporlogin' ); ?></a>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wporlogin_microsoft_client_secret"><?php esc_html_e( 'Client Secret', 'wporlogin' ); ?></label></th>
                            <td><input id="wporlogin_microsoft_client_secret" name="wporlogin_microsoft_client_secret" type="password" class="regular-text" autocomplete="new-password" value="<?php echo esc_attr( get_option( 'wporlogin_microsoft_client_secret', '' ) ); ?>" />
                                <p class="description"><?php esc_html_e( 'Copy the secret Value (not the Secret ID). Keep it private.', 'wporlogin' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Microsoft Redirect URL', 'wporlogin' ); ?></th>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <input id="wporlogin_ms_callback_url" class="regular-text code" style="min-width:min(100%,420px);" readonly type="text" value="<?php echo esc_attr( rest_url( 'wporlogin/v1/microsoft/callback' ) ); ?>" aria-label="<?php echo esc_attr__( 'Microsoft OAuth Redirect URI', 'wporlogin' ); ?>" />
                                    <button class="button button-secondary" type="button" onclick="wporloginCopyMicrosoftUri()"><span class="dashicons dashicons-admin-page" style="vertical-align:middle;"></span> <?php esc_html_e( 'Copy', 'wporlogin' ); ?></button>
                                    <span id="wporlogin_ms_copy_success" role="status" style="display:none;color:#16753a;"><?php esc_html_e( 'Copied!', 'wporlogin' ); ?></span>
                                </div>
                                <p class="description"><?php esc_html_e( 'Copy this exact URL into the Redirect URIs of a Web platform in Microsoft Entra. Remove the previous Microsoft redirect URL that contained ?wporlogin_social_auth=microsoft.', 'wporlogin' ); ?></p>
                                <?php if ( false !== strpos( rest_url( 'wporlogin/v1/microsoft/callback' ), '?' ) ) : ?>
                                    <p class="description" style="color:#b32d2e;"><?php esc_html_e( 'Microsoft accounts require a redirect URL without query parameters. Enable pretty permalinks under Settings → Permalinks, save, and then copy the updated URL above.', 'wporlogin' ); ?></p>
                                <?php endif; ?>
                                <p class="description"><?php esc_html_e( 'Choose Accounts in any organizational directory and personal Microsoft accounts to support Outlook, Hotmail, Live, and work or school accounts. Use HTTPS on public websites.', 'wporlogin' ); ?></p>
                                <p class="description"><?php esc_html_e( 'When social profile pictures are enabled, Microsoft Graph User.Read is requested to retrieve a profile photo when available. Otherwise, WordPress uses the default avatar.', 'wporlogin' ); ?></p>
                            </td>
                        </tr>
                    </tbody></table>
                </div>
            </div>
            <script>
            function wporloginCopyMicrosoftUri() {
                var field = document.getElementById('wporlogin_ms_callback_url');
                var feedback = document.getElementById('wporlogin_ms_copy_success');
                if (!field) { return; }
                function done() { if (feedback) { feedback.style.display = 'inline'; } }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(field.value).then(done).catch(function () {
                        field.select(); if (document.execCommand('copy')) { done(); }
                    });
                } else {
                    field.select(); if (document.execCommand('copy')) { done(); }
                }
            }
            </script>

            <!-- Shared settings apply independently of which provider is enabled. -->
            <div style="padding:25px 0 35px; background:#fff; border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,.13); margin-bottom:30px;">
                <div class="wporlogin-container-design" style="width:90%;margin:0 auto;">
                    <h2 class="title" style="border-bottom:1px solid #eee; padding-bottom:12px; margin-bottom:20px;">
                        <?php esc_html_e( 'General Social Settings', 'wporlogin' ); ?>
                    </h2>
                    <table class="form-table"><tbody>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Profile Picture', 'wporlogin' ); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_social_avatar_sync">
                                            <input type="checkbox" id="wporlogin_social_avatar_sync" name="wporlogin_social_avatar_sync" value="1" <?php checked(get_option('wporlogin_social_avatar_sync'), '1'); ?> />
                                            <?php esc_html_e( 'Use social profile picture', 'wporlogin' ); ?>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e( 'Display the profile picture from the social account used for the most recent sign-in, when available. Otherwise, use the default WordPress avatar.', 'wporlogin' ); ?>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Registration Methods', 'wporlogin' ); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_social_only_register">
                                            <input type="checkbox" id="wporlogin_social_only_register" name="wporlogin_social_only_register" value="1" <?php checked(get_option('wporlogin_social_only_register'), '1'); ?> />
                                            <?php esc_html_e( 'Allow social registration only', 'wporlogin' ); ?>
                                        </label>
                                        <p class="description">
                                          <?php esc_html_e( 'When enabled, the standard registration form is hidden if at least one social login service is enabled and configured. Otherwise, standard registration remains available if WordPress allows new user registrations.', 'wporlogin' ); ?>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                    </tbody></table>
                </div>
            </div>

            <?php submit_button(); ?>
            
        </form>
    </div>
</div>