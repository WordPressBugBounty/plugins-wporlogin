<?php
/**
 * Template Name: WPOrLogin Simulacion CONFIRM EMAIL
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html__( 'Confirm your administration email' ); ?> &lsaquo; <?php bloginfo( 'name' ); ?> &#8212; WordPress</title>
    <meta name='robots' content='noindex, follow' />
    
    <?php wp_head(); ?>
    
    <style>
        /* Estilos para bloquear la interacción en modo simulación */
        body.login-simulation a, 
        body.login-simulation input { cursor: default !important; }
        /* Para simular el efecto shake si quisieras probarlo */
        .shake { animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both; transform: translate3d(0, 0, 0); backface-visibility: hidden; perspective: 1000px; }
        @keyframes shake { 10%, 90% { transform: translate3d(-1px, 0, 0); } 20%, 80% { transform: translate3d(2px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-4px, 0, 0); } 40%, 60% { transform: translate3d(4px, 0, 0); } }
    </style>
</head>

<body class="login js login-action-confirm_admin_email wp-core-ui login-simulation locale-<?php echo sanitize_html_class( get_locale() ); ?>">
    
    <script type="text/javascript">
        document.body.className = document.body.className.replace('no-js','js');
    </script>

    <div id="login">
        <h1 role="presentation" class="wp-login-logo">
            <a href="javascript:void(0);"><?php _e( 'Powered by WordPress' ); ?></a>
        </h1>

        <div id="login_error" class="notice notice-error">
            <p><?php _e( 'Error: The email could not be updated.' ); ?></p>
        </div>
        
        <div class="message notice notice-success">
             <p><?php _e( 'Verification email sent.' ); ?></p>
        </div>

        <form class="admin-email-confirm-form" name="admin-email-confirm-form" action="javascript:void(0);" method="post">
            
            <input type="hidden" id="confirm_admin_email_nonce" name="confirm_admin_email_nonce" value="" />
            <input type="hidden" name="redirect_to" value="#" />

            <h1 class="admin-email__heading">
                <?php _e( 'Administration email verification' ); ?>
            </h1>

            <p class="admin-email__details">
                <?php _e( 'Please verify that the <strong>administration email</strong> for this website is still correct.' ); ?>
                <a href="javascript:void(0);" target="_blank">
                    <?php _e( 'Why is this important?' ); ?>
                    <span class="screen-reader-text"> <?php _e( '(opens in a new tab)' ); ?></span>
                </a>
            </p>

            <p class="admin-email__details">
                <?php 
                /* Traductores: %s: Email actual */
                printf( 
                    __( 'Current administration email: %s' ), 
                    '<strong>' . get_option( 'admin_email', 'demo@example.com' ) . '</strong>' 
                ); 
                ?>
            </p>

            <p class="admin-email__details">
                <?php _e( 'This email may be different from your personal email address.' ); ?>
            </p>

            <div class="admin-email__actions">
                <div class="admin-email__actions-primary">
                    <a class="button button-large" href="javascript:void(0);"><?php _e( 'Update' ); ?></a>
                    <input type="submit" name="correct-admin-email" id="correct-admin-email" class="button button-primary button-large" value="<?php echo esc_attr__( 'The email is correct' ); ?>" />
                </div>
                <div class="admin-email__actions-secondary">
                    <a href="javascript:void(0);"><?php _e( 'Remind me later' ); ?></a>
                </div>
            </div>
        </form>

        <p id="backtoblog">
            <a href="javascript:void(0);">
                <?php printf( _x( '&larr; Go to %s', 'login' ), get_bloginfo( 'title', 'display' ) ); ?>
            </a>
        </p>

        <div class="privacy-policy-page-link">
            <a class="privacy-policy-link" href="javascript:void(0);"><?php _e( 'Privacy Policy' ); ?></a>
        </div>
    </div>

    <div class="language-switcher">
        <form id="language-switcher" action="javascript:void(0);" method="get">
            <label for="language-switcher-locales">
                <span class="dashicons dashicons-translation" aria-hidden="true"></span>
                <span class="screen-reader-text"><?php _e( 'Language' ); ?></span>
            </label>

            <select name="wp_lang" id="language-switcher-locales">
                <option value="en_US" lang="en">English (United States)</option>
                <option value="es_ES" lang="es" selected="selected">Español</option>
                <option value="fr_FR" lang="fr">Français</option>
            </select>
            
            <input type="hidden" name="action" value="confirm_admin_email" />
            <input type="submit" class="button" value="<?php echo esc_attr__( 'Change' ); ?>">
        </form>
    </div>

    <?php wp_footer(); ?>

</body>
</html>