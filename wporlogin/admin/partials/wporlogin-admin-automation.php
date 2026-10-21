<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap"> 
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo esc_url( WPORLOGIN_URL . 'assets/img/logo-wporlogin.png' ); ?>" style="margin-left: 20px; height: 48px;">
    </div>

    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
    
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;">
            <strong><?php esc_html_e( 'Automation & Webhooks', 'wporlogin' ); ?></strong>
        </h1>  
    
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px; color: #555;">
            <?php esc_html_e( 'Connect your WordPress users with Zapier, Make, Pabbly, or any other app.', 'wporlogin' ); ?>
        </p>
 
        <?php settings_errors(); ?>

        <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
        
            <?php 
            // Campos ocultos necesarios
            settings_fields( 'wporlogin_automation_settings_group' ); 
            do_settings_sections( 'wporlogin_automation_settings_group' ); 
            ?>
            
            <div style="padding: 30px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); margin-bottom: 30px; display: flex; flex-wrap: wrap; gap: 30px;">
                        
                <div class="wporlogin-config-col" style="flex: 2; min-width: 300px;">
                    
                    <h2 class="title" style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px;"><?php esc_html_e( 'Webhook Connection', 'wporlogin' ); ?></h2>
                    
                    <div style="margin-bottom: 25px;">
                        <label for="wporlogin_webhook_url_free" style="font-weight: 600; font-size: 14px; display: block; margin-bottom: 8px;">
                            <?php esc_html_e( 'Webhook URL (Zapier / Make / Pabbly)', 'wporlogin' ); ?>
                        </label>
                        
                        <div style="display: flex; gap: 10px;">
                            <input type="url" 
                                id="wporlogin_webhook_url_free" 
                                name="wporlogin_webhook_url_free" 
                                value="<?php echo esc_attr(get_option('wporlogin_webhook_url_free')); ?>" 
                                class="regular-text" 
                                style="width: 100%; font-family: 'Consolas', monospace; padding: 8px;" 
                                placeholder="https://hooks.zapier.com/hooks/catch/..." 
                            />
                            
                            <button type="button" id="wporlogin-test-webhook-btn" class="button button-secondary">
                                <?php esc_html_e( '⚡ Send Test', 'wporlogin' ); ?>
                            </button>
                        </div>
                        <p class="description"><?php esc_html_e( 'Paste the URL provided by your automation tool. We will send a JSON payload here.', 'wporlogin' ); ?></p>
                        <div id="wporlogin-test-result" style="margin-top: 5px; font-weight: 600;"></div>
                    </div>

                    <h2 class="title" style="margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px;"><?php esc_html_e( 'Trigger Events', 'wporlogin' ); ?></h2>
                    
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'New User Registration', 'wporlogin' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="wporlogin_webhook_trigger_register" value="1" <?php checked(get_option('wporlogin_webhook_trigger_register'), '1'); ?> />
                                        <?php esc_html_e( 'Send data when a new user registers (via WPOrLogin Form)', 'wporlogin' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'User Login', 'wporlogin' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="wporlogin_webhook_trigger_login" value="1" <?php checked(get_option('wporlogin_webhook_trigger_login'), '1'); ?> />
                                        <?php esc_html_e( 'Send data every time a user logs in', 'wporlogin' ); ?>
                                    </label>
                                    <p class="description" style="color: #d63638;">
                                        ⚠️ <?php esc_html_e( 'Warning: This can consume many tasks in Zapier if users login frequently.', 'wporlogin' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h2 class="title" style="margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; color: #555;">
                        <?php esc_html_e( 'Commercial Integrations', 'wporlogin' ); ?> 
                        <span style="background: #ffb900; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 4px; vertical-align: middle;">PRO</span>
                    </h2>

                    <div style="opacity: 0.6; pointer-events: none; user-select: none;">
                        <p>
                            <label>
                                <input type="checkbox" disabled /> 
                                <strong>WooCommerce Sync:</strong> <?php esc_html_e( 'Capture customers from Checkout flow.', 'wporlogin' ); ?> 🔒
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" disabled /> 
                                <strong>LearnDash Sync:</strong> <?php esc_html_e( 'Sync students when they enroll in courses.', 'wporlogin' ); ?> 🔒
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" disabled /> 
                                <strong>Social Login Capture:</strong> <?php esc_html_e( 'Get leads from Google/Facebook login.', 'wporlogin' ); ?> 🔒
                            </label>
                        </p>
                    </div>

                    <div style="margin-top: 15px; padding: 10px; background: #f0f6fc; border-left: 4px solid #72aee6;">
                        <a href="https://oregoom.com/wporlogin/" target="_blank" style="text-decoration: none; font-weight: bold;">
                            <?php esc_html_e( 'Upgrade to PRO to unlock advanced commercial integrations &raquo;', 'wporlogin' ); ?>
                        </a>
                    </div>

                </div>

                <div class="wporlogin-help-col" style="flex: 1; min-width: 250px; background: #f9f9f9; padding: 20px; border-radius: 8px; height: fit-content;">
                    <h3 style="margin-top: 0;"><?php esc_html_e( 'How it works?', 'wporlogin' ); ?></h3>
                    <ol style="margin-left: 20px; line-height: 1.6; color: #555;">
                        <li><?php esc_html_e( 'Go to Zapier, Make or Pabbly.', 'wporlogin' ); ?></li>
                        <li><?php echo wp_kses_post( __( 'Create a new workflow and select <b>Webhook</b> as the trigger.', 'wporlogin' ) ); ?></li>
                        <li><?php esc_html_e( 'Copy the URL they give you.', 'wporlogin' ); ?></li>
                        <li><?php esc_html_e( 'Paste it here and click "Send Test".', 'wporlogin' ); ?></li>
                        <li><?php esc_html_e( 'Save changes.', 'wporlogin' ); ?></li>
                    </ol>
                    
                    <hr style="border: 0; border-top: 1px solid #ddd; margin: 20px 0;">

                    <p><strong><?php esc_html_e( 'Data we send:', 'wporlogin' ); ?></strong></p>
                    <ul style="list-style: disc; margin-left: 20px; font-size: 12px; color: #666;">
                        <li>User ID, Email, Name</li>
                        <li>User Role (Customer/Student)</li>
                        <li>Registration Date</li>
                        <li>Source (Form/Social/Woo)</li>
                    </ul>
                </div>
                
            </div>
            
            <?php submit_button(); ?>
            
        </form>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#wporlogin-test-webhook-btn').click(function(e) {
        e.preventDefault();
        var btn = $(this);
        var url = $('#wporlogin_webhook_url_free').val();
        var resultDiv = $('#wporlogin-test-result');

        if(url.length < 5) {
            alert('<?php echo esc_js( __( 'Please enter a valid URL', 'wporlogin' ) ); ?>');
            return;
        }

        btn.prop('disabled', true).text('<?php echo esc_js( __( 'Sending...', 'wporlogin' ) ); ?>');
        resultDiv.html('');

        $.post(ajaxurl, {
            action: 'wporlogin_test_webhook',
            webhook_url: url,
            nonce: '<?php echo esc_js( wp_create_nonce("wporlogin_test_webhook_nonce") ); ?>'
        }, function(response) {
            btn.prop('disabled', false).text('<?php echo esc_js( __( '⚡ Send Test', 'wporlogin' ) ); ?>');
            if(response.success) {
                resultDiv.empty().append($('<span>').css('color', 'green').text('✔ ' + response.data.message));
            } else {
                resultDiv.empty().append($('<span>').css('color', 'red').text('✘ ' + response.data.message));
            }
        });
    });
});
</script>
