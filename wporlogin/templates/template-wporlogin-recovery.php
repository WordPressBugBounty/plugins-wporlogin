<?php
/**
 * Template Name: WPOrLogin Simulacion LOST PASSWORD (Recuperar)
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
	<title><?php echo esc_html__( 'Lost Password' ); ?> &lsaquo; <?php bloginfo( 'name' ); ?> &#8212; WordPress</title>
	<meta name='robots' content='max-image-preview:large, noindex, noarchive' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
	<?php wp_head(); ?>
    
    <style>
        /* CSS Base para simulación */
        body.login-simulation a, 
        body.login-simulation input, 
        body.login-simulation button { cursor: default !important; }
    </style>
</head>

<body class="login no-js login-action-lostpassword wp-core-ui login-simulation locale-<?php echo sanitize_html_class( get_locale() ); ?>">

<script>
document.body.className = document.body.className.replace('no-js','js');
</script>

    <h1 class="screen-reader-text"><?php _e( 'Lost Password' ); ?></h1>

    <div id="login">
        <h1 role="presentation" class="wp-login-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Powered by WordPress' ); ?></a>
        </h1>

        <p class="message">
            <?php _e( 'Please enter your username or email address. You will receive a link to create a new password via email.' ); ?>
        </p>

        <div id="login_error" class="notice notice-error">
            <p>
                <strong><?php _e( 'Error:' ); ?></strong> 
                <?php _e( 'There is no user registered with that email address.' ); ?>
            </p>
        </div>

        <form name="lostpasswordform" id="lostpasswordform" action="#" method="post">
            
            <p>
                <label for="user_login"><?php _e( 'Username or Email Address' ); ?></label>
                <input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" autocomplete="username" required="required" />
            </p>
            
            <?php do_action( 'lostpassword_form' ); ?>

            <p class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php echo esc_attr__( 'Get New Password' ); ?>" />
            </p>
            
        </form>

        <p id="nav">
            <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e( 'Log in' ); ?></a>
            | 
            <?php if ( get_option( 'users_can_register' ) ) : ?>
                <a href="<?php echo esc_url( wp_registration_url() ); ?>"><?php _e( 'Register' ); ?></a>
            <?php endif; ?>
        </p>

        <p id="backtoblog">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php printf( _x( '&larr; Go to %s', 'login' ), get_bloginfo( 'title', 'display' ) ); ?>
            </a>
        </p>

        <div class="privacy-policy-page-link">
            <a class="privacy-policy-link" href="#"><?php _e( 'Privacy Policy' ); ?></a>
        </div>

    </div>

    <div class="language-switcher">
        <form id="language-switcher" method="get">
            <label for="language-switcher-locales">
                <span class="dashicons dashicons-translation" aria-hidden="true"></span>
                <span class="screen-reader-text"><?php _e( 'Language' ); ?></span>
            </label>
            <select name="wp_lang" id="language-switcher-locales">
                <option value="en_US" lang="en">English (United States)</option>
                <option value="es_ES" lang="es" selected='selected'>Español</option>
            </select>
            <input type="submit" class="button" value="<?php echo esc_attr__( 'Change' ); ?>">
        </form>
    </div>

<?php wp_footer(); ?>

<script>
try{document.getElementById('user_login').focus();}catch(e){}
if(typeof wpOnload==='function')wpOnload();
</script>

</body>
</html>