<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CAPTCHA provider settings (Google reCAPTCHA v2/v3 and Cloudflare Turnstile).
 *
 * This view intentionally retains the historical settings group, option names,
 * and menu slug to keep existing installations compatible.
 *
 * @package Wporlogin
 */
?>
<div class="wrap wporlogin-captcha-admin">
    <div style="width:95%;margin:-10px auto 0;background:#fff;padding-top:5px;border-radius:0 0 10px 10px;box-shadow:0 1px 2px rgba(0,0,0,.16);">
        <img src="<?php echo esc_url( WPORLOGIN_URL . 'assets/img/logo-wporlogin.png' ); ?>" alt="WPOrLogin" style="margin-left:20px;height:48px;width:auto;">
    </div>

    <div style="width:95%;margin:0 auto;position:relative;">
        <h1 style="text-align:center;font-size:34px;padding-top:30px;font-weight:700;">
            <?php esc_html_e( 'CAPTCHA Protection', 'wporlogin' ); ?>
        </h1>
        <p style="text-align:center;font-size:16px;margin:5px auto 36px;max-width:720px;line-height:1.6;">
            <?php esc_html_e( 'Protect login, registration, and password recovery forms from automated abuse with Google reCAPTCHA or Cloudflare Turnstile.', 'wporlogin' ); ?>
        </p>
        <?php settings_errors(); ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
            <?php
            settings_fields( 'recaptcha_wporlogin_custom_admin_settings_group' );
            do_settings_sections( 'recaptcha_wporlogin_custom_admin_settings_group' );
            ?>
            <div class="wporlogin-captcha-card">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="recaptcha_version_wporlogin"><?php esc_html_e( 'CAPTCHA provider', 'wporlogin' ); ?></label></th>
                            <td>
                                <select name="recaptcha_version_wporlogin" id="recaptcha_version_wporlogin">
                                    <option value="none" <?php selected( $recaptcha_version, 'none' ); ?>><?php esc_html_e( 'Disabled', 'wporlogin' ); ?></option>
                                    <option value="v2" <?php selected( $recaptcha_version, 'v2' ); ?>><?php esc_html_e( 'Google reCAPTCHA v2', 'wporlogin' ); ?></option>
                                    <option value="v3" <?php selected( $recaptcha_version, 'v3' ); ?>><?php esc_html_e( 'Google reCAPTCHA v3', 'wporlogin' ); ?></option>
                                    <option value="turnstile" <?php selected( $recaptcha_version, 'turnstile' ); ?>><?php esc_html_e( 'Cloudflare Turnstile', 'wporlogin' ); ?></option>
                                </select>
                                <p class="description"><?php esc_html_e( 'Choose one CAPTCHA service. Your saved keys for other services will be preserved.', 'wporlogin' ); ?></p>
                                <p class="wporlogin-captcha-help" data-captcha-provider="v2 v3">
                                    <?php esc_html_e( 'Create your Google reCAPTCHA keys:', 'wporlogin' ); ?>
                                    <a href="https://www.google.com/recaptcha/admin" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Google reCAPTCHA Admin', 'wporlogin' ); ?></a>
                                </p>
                                <p class="wporlogin-captcha-help" data-captcha-provider="turnstile">
                                    <?php esc_html_e( 'Create your Cloudflare Turnstile keys:', 'wporlogin' ); ?>
                                    <a href="https://dash.cloudflare.com/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Cloudflare Dashboard', 'wporlogin' ); ?></a>
                                </p>
                            </td>
                        </tr>

                        <?php
                        // A local identification icon and provider label above each key pair.
                        $providers = array(
                            'v2' => array(
                                'label'       => __( 'Google reCAPTCHA v2', 'wporlogin' ),
                                'icon'        => 'wporlogin-captcha-recaptcha.svg',
                                'site_option' => 'recaptcha_v2_site_key_wporlogin',
                                'key_option'  => 'recaptcha_v2_secret_key_wporlogin',
                            ),
                            'v3' => array(
                                'label'       => __( 'Google reCAPTCHA v3', 'wporlogin' ),
                                'icon'        => 'wporlogin-captcha-recaptcha.svg',
                                'site_option' => 'recaptcha_v3_site_key_wporlogin',
                                'key_option'  => 'recaptcha_v3_secret_key_wporlogin',
                            ),
                            'turnstile' => array(
                                'label'       => __( 'Cloudflare Turnstile', 'wporlogin' ),
                                'icon'        => 'wporlogin-captcha-cloudflare.svg',
                                'site_option' => 'wporlogin_turnstile_site_key',
                                'key_option'  => 'wporlogin_turnstile_secret_key',
                            ),
                        );
                        foreach ( $providers as $provider => $fields ) :
                            ?>
                            <tr class="wporlogin-captcha-fields" data-captcha-provider="<?php echo esc_attr( $provider ); ?>">
                                <th scope="row"><?php esc_html_e( 'Service', 'wporlogin' ); ?></th>
                                <td>
                                    <div class="wporlogin-captcha-brand">
                                        <img src="<?php echo esc_url( WPORLOGIN_URL . 'assets/img/' . $fields['icon'] ); ?>" alt="" width="35" height="35" loading="lazy">
                                        <strong><?php echo esc_html( $fields['label'] ); ?></strong>
                                    </div>
                                </td>
                            </tr>
                            <tr class="wporlogin-captcha-fields" data-captcha-provider="<?php echo esc_attr( $provider ); ?>">
                                <th scope="row"><label for="<?php echo esc_attr( $fields['site_option'] ); ?>"><?php esc_html_e( 'Site Key', 'wporlogin' ); ?></label></th>
                                <td><input id="<?php echo esc_attr( $fields['site_option'] ); ?>" name="<?php echo esc_attr( $fields['site_option'] ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( get_option( $fields['site_option'], '' ) ); ?>" autocomplete="off"></td>
                            </tr>
                            <tr class="wporlogin-captcha-fields wporlogin-captcha-key-end" data-captcha-provider="<?php echo esc_attr( $provider ); ?>">
                                <th scope="row"><label for="<?php echo esc_attr( $fields['key_option'] ); ?>"><?php esc_html_e( 'Secret Key', 'wporlogin' ); ?></label></th>
                                <td><input id="<?php echo esc_attr( $fields['key_option'] ); ?>" name="<?php echo esc_attr( $fields['key_option'] ); ?>" type="password" class="regular-text" value="<?php echo esc_attr( get_option( $fields['key_option'], '' ) ); ?>" autocomplete="new-password"></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Enable protection for:', 'wporlogin' ); ?></th>
                            <td>
                                <?php
                                // These legacy option names are used by the public forms.
                                $forms = array(
                                    'activa_acceso_recaptcha_v2_wporlogin' => __( 'Login form', 'wporlogin' ),
                                    'activa_registro_recaptcha_v2_wporlogin' => __( 'Registration form', 'wporlogin' ),
                                    'activa_recuperar_recaptcha_wporlogin' => __( 'Password recovery form', 'wporlogin' ),
                                );
                                foreach ( $forms as $option => $label ) :
                                    ?>
                                    <label class="wporlogin-captcha-form-option">
                                        <input type="checkbox" name="<?php echo esc_attr( $option ); ?>" value="1" <?php checked( '1', get_option( $option ) ); ?>>
                                        <?php echo esc_html( $label ); ?>
                                    </label>
                                <?php endforeach; ?>
                                <p class="description"><?php esc_html_e( 'These settings apply to the selected CAPTCHA service.', 'wporlogin' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php submit_button(); ?>
        </form>
    </div>
</div>
<style>
.wporlogin-captcha-admin .wporlogin-captcha-card{background:#fff;border-radius:10px;padding:20px 5%;box-shadow:0 2px 6px rgba(0,0,0,.12)}
.wporlogin-captcha-admin .wporlogin-captcha-card .form-table tr:not(:last-child){border-bottom:1px solid #e5e7e8}
.wporlogin-captcha-admin .wporlogin-captcha-card .form-table th{padding-top:18px}
.wporlogin-captcha-admin .wporlogin-captcha-help{margin:12px 0 0}
.wporlogin-captcha-admin .wporlogin-captcha-brand{display:flex;align-items:center;gap:12px;font-size:16px}
.wporlogin-captcha-admin .wporlogin-captcha-brand img{width:35px;height:35px;object-fit:contain}
.wporlogin-captcha-admin .wporlogin-captcha-form-option{display:block;margin-bottom:9px}
@media(max-width:782px){.wporlogin-captcha-admin .wporlogin-captcha-card{padding:16px 5%}.wporlogin-captcha-admin .regular-text{max-width:100%;width:100%}}
</style>
