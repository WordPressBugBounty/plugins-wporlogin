<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Vista del Dashboard Widget - Estilo "Apple Modern / iOS"
 * MÓDULOS CLICABLES COMPLETOS con indicador de enlace.
 * @package    Wporlogin
 * @subpackage Wporlogin/admin/partials
 */

// Definimos colores estilo Apple
$color_success = '#34c759'; // Apple Green
$color_danger  = '#ff3b30'; // Apple Red
$color_blue    = '#0071e3'; // Apple Blue
$color_text    = '#1d1d1f'; // Apple Near-Black
$color_sub     = '#86868b'; // Apple Grey

// Definimos las URLs de destino una sola vez para mantener el código limpio
$url_social    = admin_url('admin.php?page=social-login-wporlogin-plugin');
$url_hide      = admin_url('admin.php?page=hide-login-wporlogin-plugin');
$url_limit     = admin_url('admin.php?page=limit-login-wporlogin-plugin');
$url_recaptcha = admin_url('admin.php?page=recaptcha-wporlogin-plugin');
$url_redirect  = admin_url('admin.php?page=redirects-wporlogin-plugin');
$url_lang      = admin_url('admin.php?page=remove-language-plugin');
$url_main      = admin_url('admin.php?page=wporlogin-plugin');
?>

<style>
    .wpor-apple-widget {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        color: <?php echo esc_html( $color_text ); ?>;
        box-sizing: border-box;
    }
    .wpor-apple-widget * { box-sizing: border-box; }
    
    .wpor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        margin-bottom: 15px;
        border-bottom: 1px solid #f5f5f7;
    }
    .wpor-stat-big {
        font-size: 42px;
        font-weight: 700;
        letter-spacing: -1px;
        line-height: 1;
        color: <?php echo esc_html( $color_text ); ?>;
    }
    .wpor-stat-label {
        font-size: 12px;
        font-weight: 500;
        color: <?php echo esc_html( $color_sub ); ?>;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }
    
    .wpor-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    /* RESPONSIVE: WordPress Mobile Breakpoint (782px) */
    @media only screen and (max-width: 782px) {
        .wpor-grid {
            grid-template-columns: 1fr; /* 1 sola columna en móvil */
        }
        .wpor-stat-big {
            font-size: 32px; /* Número un poco más pequeño */
        }
    }

    /* TARJETA COMO ENLACE (<a>) */
    .wpor-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #ffffff;
        border: 1px solid #f0f0f1;
        border-radius: 12px;
        padding: 12px 14px;
        transition: all 0.2s ease;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        min-height: 75px;
        text-decoration: none !important; /* Quitar subrayado del enlace */
        color: <?php echo esc_html( $color_text ); ?> !important; /* Forzar color texto */
    }

    /* Efecto Hover */
    .wpor-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        border-color: transparent;
        background: #fafafa;
    }
    /* Mover la flechita al hacer hover */
    .wpor-card:hover .wpor-arrow-indicator {
        transform: translateX(3px);
        opacity: 1;
    }

    /* Estados de borde */
    .card-active { border-left: 3px solid <?php echo esc_html( $color_success ); ?>; }
    .card-danger { border-left: 3px solid <?php echo esc_html( $color_danger ); ?>; }
    
    .wpor-card-title {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    /* Indicador de Enlace (Flechita) */
    .wpor-arrow-indicator {
        position: absolute;
        top: 12px;
        right: 10px;
        color: #ccc;
        font-size: 14px;
        transition: all 0.2s ease;
        opacity: 0.5;
    }

    /* Badges */
    .wpor-status-badge { font-size: 11px; font-weight: 500; }
    .badge-active { color: <?php echo esc_html( $color_success ); ?>; }
    .badge-danger { color: <?php echo esc_html( $color_danger ); ?>; }
    .badge-cta { color: <?php echo esc_html( $color_blue ); ?>; font-weight: 600; }
    .badge-subtle { color: <?php echo esc_html( $color_sub ); ?>; }

    /* Botón Principal */
    .wpor-btn-main {
        display: block;
        background: <?php echo esc_html( $color_blue ); ?>;
        color: white !important;
        text-align: center;
        padding: 12px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        font-size: 13px;
        margin-top: 15px;
        transition: background 0.2s;
    }
    .wpor-btn-main:hover { background: #0077ED; }
    
    .wpor-icon { color: <?php echo esc_html( $color_sub ); ?>; font-size: 16px; width: 16px; height: 16px; margin-right: 2px; }
    .card-active .wpor-icon { color: <?php echo esc_html( $color_blue ); ?>; }
    .card-danger .wpor-icon { color: <?php echo esc_html( $color_danger ); ?>; }
</style>

<div class="wpor-apple-widget">

    <div class="wpor-header">
        <div>
            <div class="wpor-stat-big"><?php echo esc_html( $stats ); ?></div>
            <div class="wpor-stat-label"><?php esc_html_e( 'Blocked Threats', 'wporlogin' ); ?></div>
        </div>
        <div style="text-align: right;">
            <span class="dashicons dashicons-shield" style="font-size: 40px; width: 40px; height: 40px; color: <?php echo esc_html( ($stats > 0) ? $color_danger : $color_success ); ?>; opacity: 0.9;"></span>
        </div>
    </div>

    <div class="wpor-grid">

        <a href="<?php echo esc_url( $url_social ); ?>" class="wpor-card <?php echo esc_attr( $modules['social'] ? 'card-active' : '' ); ?>">
            <span class="dashicons dashicons-arrow-right-alt2 wpor-arrow-indicator"></span>
            <div class="wpor-card-title">
                <span class="dashicons dashicons-google wpor-icon"></span> <?php esc_html_e( 'Social Login', 'wporlogin' ); ?>
            </div>
            <div>
                <?php if ($modules['social']): ?>
                    <span class="wpor-status-badge badge-active"><?php esc_html_e( 'Active', 'wporlogin' ); ?></span>
                <?php else: ?>
                    <span class="wpor-status-badge badge-cta"><?php esc_html_e( 'Enable', 'wporlogin' ); ?></span>
                <?php endif; ?>
            </div>
        </a>

        <a href="<?php echo esc_url( $url_hide ); ?>" class="wpor-card <?php echo esc_attr( $modules['hide'] ? 'card-active' : 'card-danger' ); ?>">
            <span class="dashicons dashicons-arrow-right-alt2 wpor-arrow-indicator"></span>
            <div class="wpor-card-title">
                <span class="dashicons dashicons-hidden wpor-icon"></span> <?php esc_html_e( 'Hide Login', 'wporlogin' ); ?>
            </div>
            <div>
                <?php if ($modules['hide']): ?>
                    <span class="wpor-status-badge badge-active" title="/<?php echo esc_attr($modules['hide']); ?>">/<?php echo esc_html(substr($modules['hide'], 0, 6)); ?></span>
                <?php else: ?>
                    <span class="wpor-status-badge badge-danger"><?php esc_html_e( 'Insecure!', 'wporlogin' ); ?></span>
                <?php endif; ?>
            </div>
        </a>

        <a href="<?php echo esc_url( $url_limit ); ?>" class="wpor-card <?php echo esc_attr( $modules['limit'] ? 'card-active' : 'card-danger' ); ?>">
            <span class="dashicons dashicons-arrow-right-alt2 wpor-arrow-indicator"></span>
            <div class="wpor-card-title">
                <span class="dashicons dashicons-lock wpor-icon"></span> <?php esc_html_e( 'Limit Login', 'wporlogin' ); ?>
            </div>
            <div>
                <?php if ($modules['limit']): ?>
                    <span class="wpor-status-badge badge-active"><?php esc_html_e( 'Protected', 'wporlogin' ); ?></span>
                <?php else: ?>
                    <span class="wpor-status-badge badge-danger"><?php esc_html_e( 'Vulnerable!', 'wporlogin' ); ?></span>
                <?php endif; ?>
            </div>
        </a>

        <a href="<?php echo esc_url( $url_recaptcha ); ?>" class="wpor-card <?php echo esc_attr( $modules['recaptcha'] ? 'card-active' : '' ); ?>">
            <span class="dashicons dashicons-arrow-right-alt2 wpor-arrow-indicator"></span>
            <div class="wpor-card-title">
                <span class="dashicons dashicons-shield wpor-icon"></span> <?php esc_html_e( 'CAPTCHA Protection', 'wporlogin' ); ?>
            </div>
            <div>
                <?php if ($modules['recaptcha']): ?>
                    <span class="wpor-status-badge badge-active"><?php echo esc_html($modules['recaptcha']); ?></span>
                <?php else: ?>
                    <span class="wpor-status-badge badge-subtle"><?php esc_html_e( 'Setup', 'wporlogin' ); ?></span>
                <?php endif; ?>
            </div>
        </a>

        <a href="<?php echo esc_url( $url_redirect ); ?>" class="wpor-card <?php echo esc_attr( $modules['redirect'] ? 'card-active' : '' ); ?>">
            <span class="dashicons dashicons-arrow-right-alt2 wpor-arrow-indicator"></span>
            <div class="wpor-card-title">
                <span class="dashicons dashicons-randomize wpor-icon"></span> <?php esc_html_e( 'Redirects', 'wporlogin' ); ?>
            </div>
            <div>
                <?php if ($modules['redirect']): ?>
                    <span class="wpor-status-badge badge-active"><?php esc_html_e( 'On', 'wporlogin' ); ?></span>
                <?php else: ?>
                    <span class="wpor-status-badge badge-subtle"><?php esc_html_e( 'Setup', 'wporlogin' ); ?></span>
                <?php endif; ?>
            </div>
        </a>

        <a href="<?php echo esc_url( $url_lang ); ?>" class="wpor-card <?php echo esc_attr( $modules['language'] ? 'card-active' : '' ); ?>">
            <span class="dashicons dashicons-arrow-right-alt2 wpor-arrow-indicator"></span>
            <div class="wpor-card-title">
                <span class="dashicons dashicons-translation wpor-icon"></span> <?php esc_html_e( 'Clean Language', 'wporlogin' ); ?>
            </div>
            <div>
                <?php if ($modules['language']): ?>
                    <span class="wpor-status-badge badge-active"><?php esc_html_e( 'Hidden', 'wporlogin' ); ?></span>
                <?php else: ?>
                    <span class="wpor-status-badge badge-cta"><?php esc_html_e( 'Clean up', 'wporlogin' ); ?></span>
                <?php endif; ?>
            </div>
        </a>

    </div>

    <a href="<?php echo esc_url( $url_main ); ?>" class="wpor-btn-main">
        <?php esc_html_e( 'Open Settings', 'wporlogin' ); ?>
    </a>

</div>