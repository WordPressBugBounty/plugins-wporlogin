<?php
/**
 * Clase principal que inicializa el personalizador de WPORLogin.
 *
 * Esta clase se encarga de registrar el panel principal del plugin en el Customizer
 * y de cargar todas las secciones modulares como fondo, botones, campos, etc.
 *
 * @package WPORLogin\Customizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clase WPORLogin_Customizer
 */
class WPORLogin_Customizer {

	/**
	 * Inicializa la clase.
	 * Registra el hook para cargar el Personalizador.
     * CORRECCIÓN: Cambiado de __construct a init para evitar el Fatal Error.
	 */
	public function init() {
		add_action( 'customize_register', array( $this, 'register_customizer' ) );
	}

	/**
	 * Registra el panel y carga las secciones del Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Instancia del Personalizador.
	 */
	public function register_customizer( $wp_customize ) {
		$this->add_main_panel( $wp_customize );
		$this->load_sections( $wp_customize );
	}

	/**
	 * Agrega el panel principal al Personalizador.
	 *
	 * @param WP_Customize_Manager $wp_customize Instancia del Personalizador.
	 */
	private function add_main_panel( $wp_customize ) {
		$wp_customize->add_panel( 'wporlogin_panel_login', array(
			'title'       => __( '(WPORLogin) Personalizar', 'wporlogin' ),
			'priority'    => 30,
		) );
	}

	/**
	 * Carga e instancia las clases de cada sección modular del Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Instancia del Personalizador.
	 */
	private function load_sections( $wp_customize ) {

		// Fondo y diseño general.
		require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-background.php';
		$background = new WPORLogin_Customizer_Background( 'wporlogin_panel_login' );
		$background->register( $wp_customize );

		
		require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-authentication.php';
                $authentication = new WPORLogin_Customizer_Authentication( 'wporlogin_panel_login' );
                $authentication->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-form.php';
                $form = new WPORLogin_Customizer_Form( 'wporlogin_panel_login' );
                $form->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-input-fields.php';
                $inputFields = new WPORLogin_Customizer_Input_Fields( 'wporlogin_panel_login' );
                $inputFields->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-button.php';
                $button = new WPORLogin_Customizer_Button( 'wporlogin_panel_login' );
                $button->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-links.php';
                $links = new WPORLogin_Customizer_Links( 'wporlogin_panel_login' );
                $links->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-language.php';
                $language = new WPORLogin_Customizer_Language( 'wporlogin_panel_login' );
                $language->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-privacy.php';
                $privacy = new WPORLogin_Customizer_Privacy( 'wporlogin_panel_login' );
                $privacy->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-messages.php';
                $messages = new WPORLogin_Customizer_Messages( 'wporlogin_panel_login' );
                $messages->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-register.php';
                $register = new WPORLogin_Customizer_Register( 'wporlogin_panel_login' );
                $register->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-recovery.php';
                $recovery = new WPORLogin_Customizer_Recovery( 'wporlogin_panel_login' );
                $recovery->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/sections/class-emailconfirm.php';
                $emailconfirm = new WPORLogin_Customizer_EmailConfirm( 'wporlogin_panel_login' );
                $emailconfirm->register( $wp_customize );
                
                require_once WPORLOGIN_PATH . 'includes/customizer/class-customizer-assets.php';
                require_once WPORLOGIN_PATH . 'includes/customizer/class-customizer-preview.php';
                
                // Cargar las clases del Customizer dinámico
                require_once WPORLOGIN_PATH . 'includes/customizer/class-wporlogin-dynamic-css-generator.php';
                require_once WPORLOGIN_PATH . 'includes/customizer/class-wporlogin-custom-css-loader.php';

                // Inicializar las clases
                WPORLogin_Dynamic_CSS_Generator::init();
                WPORLogin_Custom_CSS_Loader::init();
                
	}
}
