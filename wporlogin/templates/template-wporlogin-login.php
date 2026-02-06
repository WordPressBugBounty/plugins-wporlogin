<?php
/**
 * Template Name: WPOrLogin Simulacion LOGIN (Estructura Completa Core)
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
	<title><?php echo esc_html__( 'Log In' ); ?> &lsaquo; <?php bloginfo( 'name' ); ?> &#8212; WordPress</title>
	<meta name='robots' content='max-image-preview:large, noindex, noarchive' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
	<?php wp_head(); ?>
    
    <style>
        /* CSS Base para simulación - No afecta al HTML final */
        body.login-simulation a, 
        body.login-simulation input, 
        body.login-simulation button { cursor: default !important; }
    </style>
</head>

<body class="login no-js login-action-login wp-core-ui login-simulation locale-<?php echo sanitize_html_class( get_locale() ); ?>">

<script>
document.body.className = document.body.className.replace('no-js','js');
</script>

    <h1 class="screen-reader-text"><?php _e( 'Log In' ); ?></h1>

    <div id="login">
        <h1 role="presentation" class="wp-login-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Powered by WordPress' ); ?></a>
        </h1>
    
        <p class="message">
            <?php _e( 'You are now logged out.' ); ?>
        </p>

        <div id="login_error" class="notice notice-error">
            <p>
                <strong><?php _e( 'Error:' ); ?></strong> 
                <?php 
                    printf( 
                        /* translators: %s: User Name */
                        __( 'The password you entered for the username %s is incorrect.' ), 
                        '<strong>nivardochoqque</strong>' 
                    ); 
                ?> 
                <a href="#"><?php _e( 'Lost your password?' ); ?></a>
            </p>
        </div>

        <form name="loginform" id="loginform" action="#" method="post">
            <p>
                <label for="user_login"><?php _e( 'Username or Email Address' ); ?></label>
                <input type="text" name="log" id="user_login" aria-describedby="login_error" class="input" value="nivardochoqque" size="20" autocapitalize="off" autocomplete="username" required="required" />
            </p>

            <div class="user-pass-wrap">
                <label for="user_pass"><?php _e( 'Password' ); ?></label>
                <div class="wp-pwd">
                    <input type="password" name="pwd" id="user_pass" aria-describedby="login_error" class="input password-input" value="" size="20" autocomplete="current-password" spellcheck="false" required="required" />
                    
                    <button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php echo esc_attr__( 'Show password' ); ?>">
                        <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                    </button>
                </div>
            </div>

            <p class="forgetmenot">
                <input name="rememberme" type="checkbox" id="rememberme" value="forever" /> 
                <label for="rememberme"><?php _e( 'Remember Me' ); ?></label>
            </p>

            <p class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php echo esc_attr__( 'Log In' ); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url( admin_url() ); ?>" />
                <input type="hidden" name="testcookie" value="1" />
            </p>
        </form>

        <p id="nav">
            <a class="wp-login-lost-password" href="#"><?php _e( 'Lost your password?' ); ?></a>
        </p>

        <script>
        function wp_attempt_focus() {setTimeout( function() {try {d = document.getElementById( "user_pass" ); d.value = "";d.focus(); d.select();} catch( er ) {}}, 200);}
        wp_attempt_focus();
        if ( typeof wpOnload === 'function' ) { wpOnload() }
        </script>

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

<script>document.querySelector('form').classList.add('shake');</script>

</body>
</html>