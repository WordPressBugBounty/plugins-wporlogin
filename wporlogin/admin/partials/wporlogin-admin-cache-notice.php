<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plantilla para la notificación de advertencia sobre la caché después de guardar ajustes.
 */
?>
<div class="notice notice-warning is-dismissible">
    <p>
        <strong><?php esc_html_e( 'IMPORTANT: ', 'wporlogin' ); ?></strong>
        <?php echo wp_kses_post( __( 'If you can\'t see the changes you\'ve made to your website, you may need to force the page to reload from the server with <strong>Ctrl + F5</strong> or clear the WordPress cache.', 'wporlogin' ) ); ?>
    </p>
    <p>
        <a target="_blank" href="https://raiolanetworks.es/blog/borrar-cache-wordpress/" title="<?php esc_attr_e( 'Clear WordPress cache', 'wporlogin' ); ?>">
            <?php esc_html_e( 'More information here', 'wporlogin' ); ?>
        </a>
    </p>
</div>