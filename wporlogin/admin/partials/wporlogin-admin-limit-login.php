<?php
/**
 * Vista para la subpágina de Limit Login Attempts.
 * Mantiene el diseño uniforme con el resto del plugin pero con lógica mejorada.
 */

// Recuperamos la IP actual para el botón de ayuda
$current_ip = $_SERVER['REMOTE_ADDR'];
// Recuperamos datos para la vista (Asumiendo que el controlador los pasó, si no, array vacío)
$blocked_ips = isset($blocked_ips_report) ? $blocked_ips_report : []; 
?>

<div class="wrap"> 
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo WPORLOGIN_URL . 'assets/img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
    </div>

    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">
    
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Limit Login Attempts', 'wporlogin'); ?></strong></h1>  
    
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('Protect your site from brute force attacks by limiting the number of login attempts.', 'wporlogin'); ?></p>
 
        <?php settings_errors(); ?>

        <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
        
            <?php 
                settings_fields( 'wporlogin_limit_settings_group' ); 
                do_settings_sections( 'wporlogin_limit_settings_group' );
            ?>
            
            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); margin-bottom: 30px;">
                        
                <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">

                    <div style="border-bottom: 1px solid #e5e7e8; padding-bottom: 15px; padding-top: 10px;">
                        <span><?php _e('Need help? ', 'wporlogin'); ?><a href="https://www.youtube.com/watch?v=uTtG9zSAXa0" target="_blank"><?php _e('Watch the video', 'wporlogin'); ?></a></span>
                    </div>
                                        
                    <h2 class="title"><?php _e('Security Settings', 'wporlogin'); ?></h2>
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><?php _e('Enable Limit Login', 'wporlogin'); ?></th>
                                <td>
                                    <fieldset>
                                        <label for="wporlogin_limit_enable">
                                            <input type="checkbox" id="wporlogin_limit_enable" name="wporlogin_limit_enable" value="1" <?php checked(get_option('wporlogin_limit_enable'), '1'); ?> />
                                            <?php _e('Yes, enable protection', 'wporlogin'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e('Max Retries Allowed', 'wporlogin'); ?></th>
                                <td>
                                    <input type="number" name="wporlogin_limit_max_retries" value="<?php echo esc_attr(get_option('wporlogin_limit_max_retries', 3)); ?>" class="small-text" min="1" max="50" />
                                    <p class="description"><?php _e('Number of allowed attempts before lockout. Recommended: 3', 'wporlogin'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><?php _e('Lockout Time (Minutes)', 'wporlogin'); ?></th>
                                <td>
                                    <input type="number" name="wporlogin_limit_lock_time" value="<?php echo esc_attr(get_option('wporlogin_limit_lock_time', 20)); ?>" class="small-text" min="1" />
                                    <p class="description"><?php _e('How long the IP remains blocked.', 'wporlogin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Custom Error Message', 'wporlogin'); ?></th>
                                <td>
                                    <textarea name="wporlogin_limit_message" rows="2" class="large-text code"><?php echo esc_textarea(get_option('wporlogin_limit_message', __('You have exceeded the maximum number of login attempts. Please try again later.', 'wporlogin'))); ?></textarea>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <?php _e('White List (IPs Only)', 'wporlogin'); ?>
                                    <br>
                                    <small style="color: #d63638; font-weight: normal;"><?php _e('No Usernames allowed', 'wporlogin'); ?></small>
                                </th>
                                <td>
                                    <?php $whitelist_val = esc_textarea(get_option('wporlogin_limit_whitelist')); ?>
                                    
                                    <textarea id="wporlogin_whitelist" name="wporlogin_limit_whitelist" rows="3" class="large-text code" placeholder="192.168.1.1"><?php echo $whitelist_val; ?></textarea>
                                    
                                    <div style="margin-top: 5px;">
                                        <button type="button" class="button button-secondary" id="wporlogin-add-current-ip" data-ip="<?php echo esc_attr($current_ip); ?>">
                                            <span class="dashicons dashicons-shield" style="vertical-align: text-top; font-size: 16px;"></span> 
                                            <?php printf( __('Add my current IP (%s)', 'wporlogin'), $current_ip ); ?>
                                        </button>
                                    </div>

                                    <p class="description">
                                        <?php _e('Enter one IP address per line. These IPs will never be blocked.', 'wporlogin'); ?><br>
                                        <?php _e('Do NOT write usernames here (e.g. "admin"), only IP addresses for security reasons.', 'wporlogin'); ?>
                                    </p>
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

            <div style="padding-top: 15px; padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
                 <div class="wporlogin-container-design" style="width: 90%; margin-left: auto; margin-right: auto;">
                    
                    <h2 class="title" style="color: #d63638; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                        <?php _e('Blocked IPs Report', 'wporlogin'); ?>
                    </h2>
                    
                    <?php 
                    // Lógica de "Flash Messages" (Mensajes de un solo uso)
                    $user_id = get_current_user_id();
                    
                    // 1. Verificar Éxito
                    if ( get_transient( 'wporlogin_flash_success_' . $user_id ) ) {
                        ?>
                        <div class="notice notice-success is-dismissible inline">
                            <p><?php _e('IP address unlocked successfully.', 'wporlogin'); ?></p>
                        </div>
                        <?php
                        // Borramos el mensaje interno inmediatamente para que no salga de nuevo
                        delete_transient( 'wporlogin_flash_success_' . $user_id );
                    }

                    // 2. Verificar Errores
                    $error_code = get_transient( 'wporlogin_flash_error_' . $user_id );
                    if ( $error_code ) {
                        $error_msg = __( 'An unknown error occurred.', 'wporlogin' );
                        
                        if ( $error_code === 'invalid_ip' ) {
                            $error_msg = __( 'The IP address provided is invalid.', 'wporlogin' );
                        } elseif ( $error_code === 'db_error' ) {
                            $error_msg = __( 'Database error. The IP could not be unlocked.', 'wporlogin' );
                        }
                        ?>
                        <div class="notice notice-error is-dismissible inline">
                            <p><strong><?php _e('Error:', 'wporlogin'); ?></strong> <?php echo esc_html( $error_msg ); ?></p>
                        </div>
                        <?php
                        // Borramos el error también
                        delete_transient( 'wporlogin_flash_error_' . $user_id );
                    }
                    ?>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('IP Address (Geo)', 'wporlogin'); ?></th>
                                <th><?php _e('Blocked At', 'wporlogin'); ?></th>
                                <th><?php _e('Time Remaining', 'wporlogin'); ?></th>
                                <th><?php _e('Status', 'wporlogin'); ?></th>
                                <th><?php _e('Action', 'wporlogin'); ?></th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ( ! empty( $blocked_ips ) ) {
                                foreach ( $blocked_ips as $record ) {
                                    $geo_url = 'https://whatismyipaddress.com/ip/' . esc_attr($record['ip']);
                                    
                                    echo '<tr>';
                                    
                                    // 1. IP + Link Geo
                                    echo '<td><a href="' . esc_url($geo_url) . '" target="_blank" style="font-weight:600;">'. esc_html( $record['ip'] ) . '</a></td>';
                                    
                                    // 2. Fecha (Último intento)
                                    echo '<td>' . esc_html( $record['start_date'] ) . '</td>';
                                    
                                    // 3. Intentos (Opcional pero útil)
                                    // Si quieres agregarlo, necesitarías añadir <th>Attempts</th> en el thead
                                    // echo '<td>' . esc_html( $record['attempts'] ) . '</td>'; 

                                    // 4. Tiempo Restante
                                    $time_style = ($record['status_code'] === 'blocked') ? 'color: #d63638; font-weight:bold;' : 'color: #999;';
                                    echo '<td style="' . $time_style . '">' . esc_html( $record['time_left'] ) . '</td>';
                                    
                                    // 5. Estado (Badges visuales)
                                    echo '<td>';
                                    if ( $record['status_code'] === 'blocked' ) {
                                        echo '<span class="dashicons dashicons-lock" style="color:#d63638; vertical-align:middle;"></span> <strong style="color:#d63638;">' . __('Blocked', 'wporlogin') . '</strong>';
                                    } elseif ( $record['status_code'] === 'expired' ) {
                                        echo '<span class="dashicons dashicons-clock" style="color:#dba617; vertical-align:middle;"></span> <span style="color:#dba617;">' . __('Expired', 'wporlogin') . '</span>';
                                    } else {
                                        echo '<span class="dashicons dashicons-unlock" style="color:#00a32a; vertical-align:middle;"></span> <span style="color:#00a32a;">' . __('Unlocked', 'wporlogin') . '</span>';
                                    }
                                    echo '</td>';
                                    
                                    // 6. Acción (Botón)
                                    echo '<td>';
                                    if ( $record['is_blocked'] ) {
                                        // Solo mostramos el botón si está bloqueado
                                        $unlock_url = wp_nonce_url( 
                                            admin_url( 'admin.php?page=limit-login-wporlogin-plugin&action=unlock_ip&ip=' . esc_attr($record['ip']) ), 
                                            'wporlogin_unlock_ip' 
                                        );
                                        echo '<a href="' . esc_url( $unlock_url ) . '" class="button button-small button-secondary" style="color: #d63638; border-color: #d63638;">';
                                        echo __( 'Unlock IP', 'wporlogin' );
                                        echo '</a>';
                                    } else {
                                        // Si ya está libre, no mostramos botón (o podrías poner "Bloquear" en el futuro)
                                        echo '<span style="color:#ccc;">—</span>';
                                    }
                                    echo '</td>';
                                    
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5" style="text-align:center; padding: 20px; color: #666;">' . __('No activity recorded yet.', 'wporlogin') . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                 </div>                 
            </div>
            
            <?php submit_button(); ?>
            
        </form>        
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
    $('#wporlogin-add-current-ip').on('click', function(e){
        e.preventDefault();
        var myIp = $(this).data('ip');
        var textarea = $('#wporlogin_whitelist');
        var currentVal = textarea.val();

        // Verificar si ya existe
        if (currentVal.indexOf(myIp) !== -1) {
            alert('<?php _e("Your IP is already in the list!", "wporlogin"); ?>');
            return;
        }

        // Agregar nueva línea
        var newVal = currentVal.trim();
        if(newVal.length > 0) {
            newVal += '\n' + myIp;
        } else {
            newVal = myIp;
        }
        
        textarea.val(newVal);
        
        // Feedback visual simple
        $(this).text('<?php _e("IP Added!", "wporlogin"); ?>');
        $(this).prop('disabled', true);
    });
});
</script>