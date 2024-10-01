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
                <label for="recaptcha_version_wporlogin"><?php _e('Versión de Google reCAPTCHA', 'wporlogin'); ?></label>
            </th>
            <td>
                <select name="recaptcha_version_wporlogin" id="recaptcha_version_wporlogin">
                    <option value="none" <?php selected($recaptcha_version, 'none'); ?>><?php _e('Desactivado Google reCAPTCHA', 'wporlogin'); ?></option>
                    <option value="v2" <?php selected($recaptcha_version, 'v2'); ?>><?php _e('Google reCAPTCHA v2', 'wporlogin'); ?></option>
                    <option value="v3" <?php selected($recaptcha_version, 'v3'); ?>><?php _e('Google reCAPTCHA v3', 'wporlogin'); ?></option>
                </select>
                <p><?php _e('Selecciona la versión de Google reCAPTCHA que deseas utilizar.', 'wporlogin'); ?></p>
                <br>
                <!-- Registro de dominio para Google reCAPTCHA -->
                <p><?php _e('Registra tu nombre de dominio en el servicio de Google reCAPTCHA y luego añade las claves en los siguientes campos.', 'wporlogin'); ?></p>
                <p><?php _e('Haz clic aquí para', 'wporlogin'); ?> <a href="https://www.google.com/recaptcha/admin" target="_blank"><?php _e('registrar tu dominio', 'wporlogin'); ?></a></p>
            </td>
        </tr>

        <!-- Claves reCAPTCHA v2 -->
        <tr class="wporlogin-recaptcha-v2-fields">
            <th scope="row">
                <label for="wporlogin_recaptcha_v2_site_key"><?php _e('Clave del sitio (v2)', 'wporlogin'); ?></label>
            </th>
            <td>
                <input id="wporlogin_recaptcha_v2_site_key" type="text" name="recaptcha_v2_site_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v2_site_key_wporlogin')); ?>" />
            </td>
        </tr>
        <tr class="wporlogin-recaptcha-v2-fields" style="border-bottom: 1px solid #e5e7e8;">
            <th scope="row">
                <label for="wporlogin_recaptcha_v2_secret_key"><?php _e('Clave secreta (v2)', 'wporlogin'); ?></label>
            </th>
            <td>
                <input id="wporlogin_recaptcha_v2_secret_key" type="text" name="recaptcha_v2_secret_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v2_secret_key_wporlogin')); ?>" />
            </td>
        </tr>

        <!-- Claves reCAPTCHA v3 -->
        <tr class="wporlogin-recaptcha-v3-fields">
            <th scope="row">
                <label for="wporlogin_recaptcha_v3_site_key"><?php _e('Clave del sitio (v3)', 'wporlogin'); ?></label>
            </th>
            <td>
                <input id="wporlogin_recaptcha_v3_site_key" type="text" name="recaptcha_v3_site_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v3_site_key_wporlogin')); ?>" />
            </td>
        </tr>
        <tr class="wporlogin-recaptcha-v3-fields" style="border-bottom: 1px solid #e5e7e8;">
            <th scope="row">
                <label for="wporlogin_recaptcha_v3_secret_key"><?php _e('Clave secreta (v3)', 'wporlogin'); ?></label>
            </th>
            <td>
                <input id="wporlogin_recaptcha_v3_secret_key" type="text" name="recaptcha_v3_secret_key_wporlogin" class="regular-text" value="<?php echo esc_html(get_option('recaptcha_v3_secret_key_wporlogin')); ?>" />
            </td>
        </tr>

        <!-- Opción para activar reCAPTCHA en login, registro y recuperación de contraseña -->
        <tr>
            <th scope="row">
                <label for="activar_recaptcha_wporlogin"><?php _e('Activar reCAPTCHA para', 'wporlogin'); ?></label>
            </th>
            <td>

                <input name="activa_acceso_recaptcha_v2_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option('activa_acceso_recaptcha_v2_wporlogin')); ?> id="activa_acceso_recaptcha_v2_wporlogin"/>
                <label for="activa_acceso_recaptcha_v2_wporlogin"><?php _e('Formulario de acceso', 'wporlogin'); ?></label><br>

                <input name="activa_registro_recaptcha_v2_wporlogin" type="checkbox" value="1" <?php checked( '1', get_option('activa_registro_recaptcha_v2_wporlogin')); ?> id="activa_registro_recaptcha_v2_wporlogin"/>
                <label for="activa_registro_recaptcha_v2_wporlogin"><?php _e('Formulario de registro', 'wporlogin'); ?></label><br>
                        
                <!-- Activar para el formulario de recuperación de contraseña -->
                <input name="activa_recuperar_recaptcha_wporlogin" type="checkbox" value="1" <?php checked('1', get_option('activa_recuperar_recaptcha_wporlogin')); ?> id="activa_recuperar_recaptcha_wporlogin"/>
                <label for="activa_recuperar_recaptcha_wporlogin"><?php _e('Formulario de recuperación de contraseña', 'wporlogin'); ?></label>

            </td>
        </tr>
    </tbody>
</table>

    <?php
}
add_action('wporlogin_render_recaptcha_form', 'wporlogin_render_recaptcha_form');
