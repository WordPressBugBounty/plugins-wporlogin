<?php
/**
 * Template Name: WPOrLogin Simulacion REGISTER (Estructura Completa)
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
	<title><?php echo esc_html__( 'Registration Form' ); ?> &lsaquo; <?php bloginfo( 'name' ); ?> &#8212; WordPress</title>
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

<body class="login no-js login-action-register wp-core-ui login-simulation locale-<?php echo sanitize_html_class( get_locale() ); ?>">

<script>
document.body.className = document.body.className.replace('no-js','js');
</script>

    <h1 class="screen-reader-text"><?php _e( 'Register' ); ?></h1>

    <div id="login">
        <h1 role="presentation" class="wp-login-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Powered by WordPress' ); ?></a>
        </h1>

        <p class="message register"><?php _e( 'Register For This Site' ); ?></p>

        <div id="login_error" class="notice notice-error">
            <p>
                <strong><?php _e( 'Error:' ); ?></strong> 
                <?php _e( 'This username is already registered. Please choose another one.' ); ?>
            </p>
        </div>

        <form name="registerform" id="registerform" action="#" method="post" novalidate="novalidate">
            
            <p>
                <label for="user_login"><?php _e( 'Username' ); ?></label>
                <input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" autocomplete="username" required="required" />
            </p>

            <p>
                <label for="user_email"><?php _e( 'Email' ); ?></label>
                <input type="email" name="user_email" id="user_email" class="input" value="" size="25" autocomplete="email" required="required" />
            </p>

            <p id="reg_passmail">
                <?php _e( 'Registration confirmation will be emailed to you.' ); ?>
            </p>

            <br class="clear" />

            <p class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php echo esc_attr__( 'Register' ); ?>" />
                <input type="hidden" name="redirect_to" value="" />
            </p>
        </form>

        <p id="nav">
            <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e( 'Log in' ); ?></a>
            | 
            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?' ); ?></a>
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
// Script simple para enfocar el usuario en registro (Similar al del core)
try{document.getElementById('user_login').focus();}catch(e){}
if(typeof wpOnload==='function')wpOnload();
</script>

</body>
</html>