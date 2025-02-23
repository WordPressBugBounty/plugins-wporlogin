<?php

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente.
}

function configure_urls_wporlogin_content_page_menu() {
    ?>
    <div class="wrap">
        <h1>Configurar URLs de Acceso</h1>
        <p>Personaliza las URLs de acceso para mejorar la seguridad y usabilidad de tu sitio.</p>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('wporlogin_configure_urls_settings');
            do_settings_sections('wporlogin_configure_urls_settings');
            ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="custom_login_url">Nueva URL de Login:</label></th>
                    <td>
                        <input type="text" id="custom_login_url" name="custom_login_url" 
                               value="<?php echo esc_attr(get_option('custom_login_url')); ?>" 
                               placeholder="/acceder" class="regular-text">
                               <p>Por defecto: /wp-login.php</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="custom_register_url">Nueva URL de Registro:</label></th>
                    <td>
                        <input type="text" id="custom_register_url" name="custom_register_url" 
                               value="<?php echo esc_attr(get_option('custom_register_url')); ?>" 
                               placeholder="/registrarse" class="regular-text">
                               <p>Por defecto: /wp-login.php?action=register</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="custom_lostpassword_url">Nueva URL de Recuperar Clave:</label></th>
                    <td>
                        <input type="text" id="custom_lostpassword_url" name="custom_lostpassword_url" 
                               value="<?php echo esc_attr(get_option('custom_lostpassword_url')); ?>" 
                               placeholder="/recuperar-clave" class="regular-text">
                               <p>Por defecto: /wp-login.php?action=lostpassword</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Redirigir URLs originales</th>
                    <td>
                        <input type="checkbox" name="disable_default_urls" value="1" 
                               <?php checked(get_option('disable_default_urls'), 1); ?>>
                        <label for="disable_default_urls">Evita el acceso a las URLs predeterminadas de WordPress.</label>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Guardar Cambios'); ?>
        </form>
    </div>
    <?php
}

function wporlogin_register_settings_configure_urls() {
    register_setting('wporlogin_configure_urls_settings', 'custom_login_url', 'sanitize_text_field');
    register_setting('wporlogin_configure_urls_settings', 'custom_register_url', 'sanitize_text_field');
    register_setting('wporlogin_configure_urls_settings', 'custom_lostpassword_url', 'sanitize_text_field');
    register_setting('wporlogin_configure_urls_settings', 'disable_default_urls', 'intval');
}
add_action('admin_init', 'wporlogin_register_settings_configure_urls');

function wporlogin_redirect_old_urls() {
    if (get_option('disable_default_urls')) {
        global $pagenow;

        // Redirigir login
        if ($pagenow == 'wp-login.php' && !isset($_GET['action'])) {
            wp_redirect(home_url(get_option('custom_login_url')));
            exit;
        }
        
        // Redirigir registro
        if ($pagenow == 'wp-login.php' && isset($_GET['action']) && $_GET['action'] == 'register') {
            wp_redirect(home_url(get_option('custom_register_url')));
            exit;
        }

        // Redirigir recuperación de contraseña
        if ($pagenow == 'wp-login.php' && isset($_GET['action']) && $_GET['action'] == 'lostpassword') {
            wp_redirect(home_url(get_option('custom_lostpassword_url')));
            exit;
        }
    }
}
add_action('init', 'wporlogin_redirect_old_urls');


