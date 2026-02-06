<?php

/**
 * Gestiona todas las notificaciones y avisos del área de administración.
 *
 * Separa la lógica de "negocio" (configuraciones) de la lógica de "comunicación" (avisos de marketing y updates).
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/admin
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin_Admin_Notices {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * 1. AVISO DE RESEÑA (5 ESTRELLAS)
     * Solicita amablemente una valoración después de 15 días de uso.
     */
    public function show_review_notice() {
        // Si ya lo descartó, salir.
        if ( get_option('delete_notice_wporlogin_condition') == 1 ) return;

        $fecha_instalacion = get_option('wporlogin_date_5_review'); 
        if (empty($fecha_instalacion)) return;

        try {
            $date1 = new DateTime($fecha_instalacion);
            $date2 = new DateTime();
            $diff  = $date1->diff($date2);
            
            // Si han pasado 15 días o más
            if ($diff->days >= 15) {
                if (file_exists(WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-review-notice.php')) {
                    require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-review-notice.php';
                }
            }
        } catch (Exception $e) { return; }
    }

    /**
     * AVISO PREMIUM: Diseño moderno para destacar la nueva función Limit Login.
     * Incluye lógica para no mostrarse si la función ya está activa o estamos en la página de configuración.
     */
    public function notice_new_feature_limit_login() {
        
        // 1. Verificamos si ya se descartó manualmente
        if ( get_option( 'wporlogin_dismiss_limit_msg' ) === '1' ) {
            return;
        }

        // 2. Verificamos si el usuario YA activó la función (Éxito = No mostrar aviso)
        if ( get_option( 'wporlogin_limit_enable' ) === '1' ) {
            return; 
        }

        // 3. Verificamos permisos de admin
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // 4. Lógica Inteligente: No mostrar si ya estamos en la página de configuración
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'limit-login-wporlogin-plugin' ) {
            return;
        }

        $setting_url = admin_url( 'admin.php?page=limit-login-wporlogin-plugin' );
        
        ?>
        <style>
            .wporlogin-premium-notice {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-left: 4px solid #d63638;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                margin: 20px 0 15px 0;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
                border-radius: 4px;
                min-height: 80px;
            }
            .wporlogin-notice-content { display: flex; align-items: center; padding: 15px 20px; }
            .wporlogin-icon-box {
                background: #ffe3e3; color: #d63638; width: 48px; height: 48px;
                border-radius: 50%; display: flex; align-items: center; justify-content: center;
                margin-right: 15px; flex-shrink: 0;
            }
            .wporlogin-icon-box .dashicons { font-size: 24px; width: 24px; height: 24px; }
            .wporlogin-text h3 { margin: 0 0 5px 0; font-size: 16px; color: #1d2327; font-weight: 700; }
            .wporlogin-text p { margin: 0; font-size: 14px; color: #50575e; }
            .wporlogin-actions {
                padding-right: 20px; padding-left: 20px; border-left: 1px solid #f0f0f1;
                height: 80px; display: flex; align-items: center;
            }
            .wporlogin-btn-cta {
                background-color: #d63638; color: #fff; text-decoration: none; padding: 8px 16px;
                border-radius: 4px; font-weight: 600; font-size: 14px; transition: background 0.3s;
                border: none; cursor: pointer;
            }
            .wporlogin-btn-cta:hover { background-color: #a52022; color: #fff; }
            @media (max-width: 782px) {
                .wporlogin-premium-notice { flex-direction: column; align-items: flex-start; }
                .wporlogin-actions { border-left: none; padding: 0 20px 15px 20px; height: auto; }
            }
        </style>

        <div class="notice is-dismissible wporlogin-premium-notice wporlogin-limit-feature-notice">
            <div class="wporlogin-notice-content">
                <div class="wporlogin-icon-box">
                    <span class="dashicons dashicons-shield"></span>
                </div>
                <div class="wporlogin-text">
                    <h3>
                        <?php _e( 'New Security Module Available!', 'wporlogin' ); ?> 
                        <span style="background:#e5fdf4; color:#00a32a; font-size:10px; padding:2px 6px; border-radius:3px; border:1px solid #00a32a; text-transform:uppercase; vertical-align:middle; margin-left:5px;">New</span>
                    </h3>
                    <p>
                        <?php _e( 'Protect your website from brute force attacks with the new <strong>Limit Login Attempts</strong> feature. Secure your login page now.', 'wporlogin' ); ?>
                    </p>
                </div>
            </div>
            <div class="wporlogin-actions">
                <a href="<?php echo esc_url( $setting_url ); ?>" class="wporlogin-btn-cta">
                    <?php _e( 'Enable Protection', 'wporlogin' ); ?> &rarr;
                </a>
            </div>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.wporlogin-limit-feature-notice').on('click', '.notice-dismiss', function() {
                    $.post(ajaxurl, {
                        action: 'wporlogin_dismiss_notice_generic',
                        nonce: '<?php echo wp_create_nonce( "wporlogin_dismiss_nonce" ); ?>',
                        notice_id: 'wporlogin_dismiss_limit_msg'
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * AVISO PREMIUM: Diseño moderno para destacar la nueva función Hide Login.
     * Incluye lógica para no mostrarse si la función ya está activa o estamos en la página de configuración.
     */
    public function notice_new_feature_hide_login() {
        
        // 1. Verificamos si ya se descartó manualmente (Cambiamos el ID de la opción)
        if ( get_option( 'wporlogin_dismiss_hide_login_msg' ) === '1' ) {
            return;
        }

        // 2. Verificamos permisos de admin
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // 3. Lógica Inteligente: No mostrar si ya estamos en la página de configuración
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'hide-login-wporlogin-plugin' ) {
            return;
        }

        // Actualizamos la URL a la página de Hide Login
        $setting_url = admin_url( 'admin.php?page=hide-login-wporlogin-plugin' );
        
        ?>
        <style>
            .wporlogin-premium-notice {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-left: 4px solid #d63638; /* Mantenemos el rojo llamativo */
                box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                margin: 20px 0 15px 0;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
                border-radius: 4px;
                min-height: 80px;
            }
            .wporlogin-notice-content { display: flex; align-items: center; padding: 15px 20px; }
            .wporlogin-icon-box {
                background: #ffe3e3; color: #d63638; width: 48px; height: 48px;
                border-radius: 50%; display: flex; align-items: center; justify-content: center;
                margin-right: 15px; flex-shrink: 0;
            }
            .wporlogin-icon-box .dashicons { font-size: 24px; width: 24px; height: 24px; }
            .wporlogin-text h3 { margin: 0 0 5px 0; font-size: 16px; color: #1d2327; font-weight: 700; }
            .wporlogin-text p { margin: 0; font-size: 14px; color: #50575e; }
            .wporlogin-actions {
                padding-right: 20px; padding-left: 20px; border-left: 1px solid #f0f0f1;
                height: 80px; display: flex; align-items: center;
            }
            .wporlogin-btn-cta {
                background-color: #d63638; color: #fff; text-decoration: none; padding: 8px 16px;
                border-radius: 4px; font-weight: 600; font-size: 14px; transition: background 0.3s;
                border: none; cursor: pointer;
            }
            .wporlogin-btn-cta:hover { background-color: #a52022; color: #fff; }
            @media (max-width: 782px) {
                .wporlogin-premium-notice { flex-direction: column; align-items: flex-start; }
                .wporlogin-actions { border-left: none; padding: 0 20px 15px 20px; height: auto; }
            }
        </style>

        <div class="notice is-dismissible wporlogin-premium-notice wporlogin-hide-login-feature-notice">
            <div class="wporlogin-notice-content">
                <div class="wporlogin-icon-box">
                    <span class="dashicons dashicons-hidden"></span>
                </div>
                <div class="wporlogin-text">
                    <h3>
                        <?php _e( 'New Security Feature: Hide Login URL!', 'wporlogin' ); ?> 
                        <span style="background:#e5fdf4; color:#00a32a; font-size:10px; padding:2px 6px; border-radius:3px; border:1px solid #00a32a; text-transform:uppercase; vertical-align:middle; margin-left:5px;">New</span>
                    </h3>
                    <p>
                        <?php _e( 'Stop hackers from finding your login page. Change your default <strong>wp-login.php</strong> URL to something secret and secure.', 'wporlogin' ); ?>
                    </p>
                </div>
            </div>
            <div class="wporlogin-actions">
                <a href="<?php echo esc_url( $setting_url ); ?>" class="wporlogin-btn-cta">
                    <?php _e( 'Hide My Login URL', 'wporlogin' ); ?> &rarr;
                </a>
            </div>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Selector actualizado para la nueva clase del aviso
                $('.wporlogin-hide-login-feature-notice').on('click', '.notice-dismiss', function() {
                    $.post(ajaxurl, {
                        action: 'wporlogin_dismiss_notice_generic',
                        nonce: '<?php echo wp_create_nonce( "wporlogin_dismiss_nonce" ); ?>',
                        notice_id: 'wporlogin_dismiss_hide_login_msg' // ID específico para este aviso
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * AVISO NEW FEATURE: Social Login (Google).
     * Destaca la nueva funcionalidad de inicio de sesión con redes sociales.
     */
    public function notice_new_feature_social_login() {
        
        // 1. Verificamos si ya se descartó manualmente (ID único para este aviso)
        if ( get_option( 'wporlogin_dismiss_social_login_msg' ) === '1' ) {
            return;
        }

        // 2. Verificamos si la función YA está activa (Para no molestar al usuario si ya lo configuró)
        if ( get_option( 'wporlogin_google_enable' ) === '1' ) {
            return; 
        }

        // 3. Verificamos permisos de admin
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // 4. Lógica Inteligente: No mostrar si ya estamos en la página de configuración de Social Login
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'social-login-wporlogin-plugin' ) {
            return;
        }

        // URL al nuevo menú de Social Login
        $setting_url = admin_url( 'admin.php?page=social-login-wporlogin-plugin' );
        
        ?>
        <style>
            .wporlogin-premium-notice {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-left: 4px solid #d63638;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                margin: 20px 0 15px 0;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
                border-radius: 4px;
                min-height: 80px;
            }
            .wporlogin-notice-content { display: flex; align-items: center; padding: 15px 20px; }
            .wporlogin-icon-box {
                background: #ffe3e3; color: #d63638; width: 48px; height: 48px;
                border-radius: 50%; display: flex; align-items: center; justify-content: center;
                margin-right: 15px; flex-shrink: 0;
            }
            .wporlogin-icon-box .dashicons { font-size: 24px; width: 24px; height: 24px; }
            .wporlogin-text h3 { margin: 0 0 5px 0; font-size: 16px; color: #1d2327; font-weight: 700; }
            .wporlogin-text p { margin: 0; font-size: 14px; color: #50575e; }
            .wporlogin-actions {
                padding-right: 20px; padding-left: 20px; border-left: 1px solid #f0f0f1;
                height: 80px; display: flex; align-items: center;
            }
            .wporlogin-btn-cta {
                background-color: #d63638; color: #fff; text-decoration: none; padding: 8px 16px;
                border-radius: 4px; font-weight: 600; font-size: 14px; transition: background 0.3s;
                border: none; cursor: pointer;
            }
            .wporlogin-btn-cta:hover { background-color: #a52022; color: #fff; }
            @media (max-width: 782px) {
                .wporlogin-premium-notice { flex-direction: column; align-items: flex-start; }
                .wporlogin-actions { border-left: none; padding: 0 20px 15px 20px; height: auto; }
            }
        </style>

        <div class="notice is-dismissible wporlogin-premium-notice wporlogin-social-login-feature-notice">
            <div class="wporlogin-notice-content">
                <div class="wporlogin-icon-box">
                    <span class="dashicons dashicons-google"></span>
                </div>
                <div class="wporlogin-text">
                    <h3>
                        <?php _e( 'New Feature: Social Login!', 'wporlogin' ); ?> 
                        <span style="background:#e5fdf4; color:#00a32a; font-size:10px; padding:2px 6px; border-radius:3px; border:1px solid #00a32a; text-transform:uppercase; vertical-align:middle; margin-left:5px;">New</span>
                    </h3>
                    <p>
                        <?php _e( 'Allow users to register and log in using their <strong>Google Account</strong>. Increase conversions and simplify access.', 'wporlogin' ); ?>
                    </p>
                </div>
            </div>
            <div class="wporlogin-actions">
                <a href="<?php echo esc_url( $setting_url ); ?>" class="wporlogin-btn-cta">
                    <?php _e( 'Setup Social Login', 'wporlogin' ); ?> &rarr;
                </a>
            </div>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Selector específico para este aviso
                $('.wporlogin-social-login-feature-notice').on('click', '.notice-dismiss', function() {
                    $.post(ajaxurl, {
                        action: 'wporlogin_dismiss_notice_generic',
                        nonce: '<?php echo wp_create_nonce( "wporlogin_dismiss_nonce" ); ?>',
                        notice_id: 'wporlogin_dismiss_social_login_msg' // ID único de la opción a guardar en BD
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    public function recaptcha_config_warning() {
        $recaptcha_version = get_option('recaptcha_version_wporlogin', 'none');
        if ($recaptcha_version === 'v2') {
            $site_key   = get_option('recaptcha_v2_site_key_wporlogin');
            $secret_key = get_option('recaptcha_v2_secret_key_wporlogin');
            if (empty($site_key) || empty($secret_key)) {
                echo '<div class="notice notice-error"><p>' . __('Google reCAPTCHA v2 is selected, but the site or secret keys are not configured.', 'wporlogin') . '</p></div>';
            }
        } elseif ($recaptcha_version === 'v3') {
            $site_key = get_option('recaptcha_v3_site_key_wporlogin');
            if (empty($site_key)) {
                echo '<div class="notice notice-error"><p>' . __('Google reCAPTCHA v3 is selected, but the site or secret keys are not configured.', 'wporlogin') . '</p></div>';
            }
        }
    }

    public function show_cache_warning() {
        global $pagenow;
        if ( $pagenow !== 'admin.php' || ! isset( $_GET['settings-updated'] ) || $_GET['settings-updated'] != true ) return;

        $plugin_pages = array(
            'wporlogin-plugin',
            'recaptcha-wporlogin-plugin',
            'remove-language-plugin',
            'redirects-wporlogin-plugin',
            'limit-login-wporlogin-plugin' // Agregado aquí también
        );

        if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $plugin_pages ) ) {
            if ( file_exists( WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-cache-notice.php' ) ) {
                require_once WPORLOGIN_PATH . 'admin/partials/wporlogin-admin-cache-notice.php';
            }
        }
    }

    /**
     * AJAX: Cierra permanentemente el aviso de solicitud de reseña.
     */
    public function ajax_delete_notice() {
        // 1. Verificación de Seguridad (Nonce)
        if ( ! isset( $_POST['nonce'] ) ) {
            wp_die( __( 'Error: Nonce missing', 'wporlogin' ) );
        }

        $nonce = sanitize_text_field( $_POST['nonce'] );

        if ( wp_verify_nonce( $nonce, 'my-ajax-nonce-wporlogin') ) {
            // 2. Guardar en base de datos
            update_option( 'delete_notice_wporlogin_condition', '1' );
            _e('Excelente', 'wporlogin');
        } else {
            wp_die( __( 'Error de nonce', 'wporlogin' ) );
        }
        
        wp_die();
    }

    /**
     * Manejador AJAX Genérico para cerrar cualquier aviso.
     */
    public function ajax_dismiss_notice() {
        check_ajax_referer( 'wporlogin_dismiss_nonce', 'nonce' );

        if ( isset( $_POST['notice_id'] ) ) {
            $notice_id = sanitize_text_field( $_POST['notice_id'] );
            
            // Seguridad: Solo permitimos guardar opciones que empiecen por nuestro prefijo
            if ( strpos( $notice_id, 'wporlogin_dismiss_' ) === 0 ) {
                update_option( $notice_id, '1' );
            }
        }

        wp_die();
    }
}