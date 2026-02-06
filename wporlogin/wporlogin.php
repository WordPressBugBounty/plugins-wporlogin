<?php
/**
 * Plugin Name:       WPOrLogin - Custom Login, Social Login, Limit Attempts, Hide Login & reCAPTCHA
 * Plugin URI:        https://oregoom.com/wporlogin/
 * Description:       Customize your WordPress login page design. Secure it with Google reCAPTCHA, Limit Login Attempts, and Hide Login URL. Includes Social Login and Role-Based Redirects.
 * Version:           3.0.2
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Oregoom
 * Author URI:        https://oregoom.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wporlogin
 * Domain Path:       /languages/
 */

// Si este archivo es llamado directamente, abortar.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes para rutas (ayuda a la escalabilidad)
define( 'WPORLOGIN_VERSION', '3.0.2' );
define( 'WPORLOGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPORLOGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * El código que se ejecuta durante la activación del plugin.
 */
function activate_wporlogin() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wporlogin-activator.php';
    Wporlogin_Activator::activate();
}

/**
 * El código que se ejecuta durante la desactivación del plugin.
 */
function deactivate_wporlogin() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wporlogin-deactivator.php';
    Wporlogin_Deactivator::deactivate(); // <--- CORREGIDO
}

register_activation_hook( __FILE__, 'activate_wporlogin' );
register_deactivation_hook( __FILE__, 'deactivate_wporlogin' );

/**
 * La clase central del núcleo.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wporlogin.php';

/**
 * Iniciar ejecución del plugin.
 */
function run_wporlogin() {
    $plugin = new Wporlogin();
    $plugin->run();
}
run_wporlogin();