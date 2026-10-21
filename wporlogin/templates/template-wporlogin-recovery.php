<?php
/**
 * Template Name: WPOrLogin Simulacion LOST PASSWORD (Recuperar)
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
	<title><?php echo esc_html__( 'Lost Password', 'default' ); ?> &lsaquo; <?php echo esc_html( get_bloginfo( 'name' ) ); ?> &#8212; WordPress</title>
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

    <h1 class="screen-reader-text"><?php esc_html_e( 'Lost Password', 'default' ); ?></h1>

    <div id="login">
        <h1 role="presentation" class="wp-login-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Powered by WordPress', 'default' ); ?></a>
        </h1>

        <p class="message">
            <?php esc_html_e( 'Please enter your username or email address. You will receive a link to create a new password via email.', 'default' ); ?>
        </p>

        <div id="login_error" class="notice notice-error">
            <p>
                <strong><?php esc_html_e( 'Error:', 'default' ); ?></strong> 
                <?php esc_html_e( 'There is no user registered with that email address.', 'default' ); ?>
            </p>
        </div>

        <form name="lostpasswordform" id="lostpasswordform" action="#" method="post">
            
            <p>
                <label for="user_login"><?php esc_html_e( 'Username or Email Address', 'default' ); ?></label>
                <input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" autocomplete="username" required="required" />
            </p>
            
            <?php do_action( 'lostpassword_form' ); ?>

            <p class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php echo esc_attr__( 'Get New Password', 'default' ); ?>" />
            </p>
            
        </form>

        <p id="nav">
            <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Log in', 'default' ); ?></a>
            | 
            <?php if ( get_option( 'users_can_register' ) ) : ?>
                <a href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Register', 'default' ); ?></a>
            <?php endif; ?>
        </p>

        <p id="backtoblog">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php
                /* translators: %s: Title of the website linked from the login screen. */
                echo esc_html( sprintf( _x( '&larr; Go to %s', 'login', 'default' ), get_bloginfo( 'title', 'display' ) ) ); ?>
            </a>
        </p>

        <div class="privacy-policy-page-link">
            <a class="privacy-policy-link" href="#"><?php esc_html_e( 'Privacy Policy', 'default' ); ?></a>
        </div>

    </div>

    <div class="language-switcher">
        <form id="language-switcher" method="get">
            <label for="language-switcher-locales">
                <span class="dashicons dashicons-translation" aria-hidden="true"></span>
                <span class="screen-reader-text"><?php esc_html_e( 'Language', 'default' ); ?></span>
            </label>
            <select name="wp_lang" id="language-switcher-locales">
                <option value="en_US" lang="en">English (United States)</option>
                <option value="es_ES" lang="es" selected='selected'>Español</option>
            </select>
            <input type="submit" class="button" value="<?php echo esc_attr__( 'Change', 'default' ); ?>">
        </form>
    </div>

<?php wp_footer(); ?>

<script>
try{document.getElementById('user_login').focus();}catch(e){}
if(typeof wpOnload==='function')wpOnload();
</script>

</body>
</html>