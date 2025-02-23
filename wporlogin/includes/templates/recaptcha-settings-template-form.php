<?php

function wporlogin_render_recaptcha_form() {

    ///////////////////////////////////////////////////////////////////////////////
    /////////// CÓDIGO PARA MANTENER COMPATIBLE CON LA VERSIÓN 2.8.6  /////////////
    ///////////////////////////////////////////////////////////////////////////////

    // Recuperar el valor de 'recaptcha_v2_wporlogin' desde la base de datos, o asignar un valor por defecto (0 en este caso).
    $recaptcha_v2_enabled = get_option('recaptcha_v2_wporlogin', 0); // Si no existe la opción, devuelve 0.

    // Recupera el valor de 'recaptcha_version_wporlogin' desde la base de datos.
    $recaptcha_version = get_option('recaptcha_version_wporlogin', 'none'); // El valor por defecto será 'none' si no hay una opción guardada.

    // Si no está configurado 'recaptcha_version_wporlogin' pero v2 está habilitado.
    if ($recaptcha_version === 'none' && $recaptcha_v2_enabled == 1) {
        $recaptcha_version = 'v2'; // Ajustar la versión como v2 si v2 está habilitado.
        update_option('recaptcha_version_wporlogin', 'v2'); // Actualizar la versión en la base de datos.
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///////////////      FIN DEL CÓDIGO DE COMPROBACIÓN      ///////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    ?>

<table class="form-table" role="presentation">
    <tbody>

        <!-- Selector para elegir la versión de reCAPTCHA -->
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
                <!-- Registro de dominio para Google reCAPTCHA -->
                <p><?php _e("To use reCAPTCHA, first register your domain with Google's service, then enter the keys in the fields below.", 'wporlogin'); ?></p>
                <p><?php _e('<a href="https://www.google.com/recaptcha/admin" target="_blank">Click here to register your domain</a>', 'wporlogin'); ?></a></p>
            </td>
        </tr>

        <!-- Claves reCAPTCHA v2 -->
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

        <!-- Claves reCAPTCHA v3 -->
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

        <!-- Opción para activar reCAPTCHA en login, registro y recuperación de contraseña -->
        <tr>
            <th scope="row">
                <label for="activar_recaptcha_wporlogin"><?php _e('Enable reCAPTCHA for:', 'wporlogin'); ?></label>
            </th>
            <td>

                <input name="activa_acceso_recaptcha_v2_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option('activa_acceso_recaptcha_v2_wporlogin')); ?> id="activa_acceso_recaptcha_v2_wporlogin"/>
                <label for="activa_acceso_recaptcha_v2_wporlogin"><?php _e('Login form', 'wporlogin'); ?></label><br>

                <input name="activa_registro_recaptcha_v2_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option('activa_registro_recaptcha_v2_wporlogin')); ?> id="activa_registro_recaptcha_v2_wporlogin"/>
                <label for="activa_registro_recaptcha_v2_wporlogin"><?php _e('Registration form', 'wporlogin'); ?></label><br>
                        
                <!-- Activar para el formulario de recuperación de contraseña -->
                <input name="activa_recuperar_recaptcha_wporlogin" type="checkbox" value="1" <?php checked('1', get_option('activa_recuperar_recaptcha_wporlogin')); ?> id="activa_recuperar_recaptcha_wporlogin"/>
                <label for="activa_recuperar_recaptcha_wporlogin"><?php _e('Password recovery form', 'wporlogin'); ?></label>

            </td>
        </tr>
    </tbody>
</table>

    <?php
}
add_action('wporlogin_render_recaptcha_form', 'wporlogin_render_recaptcha_form');
