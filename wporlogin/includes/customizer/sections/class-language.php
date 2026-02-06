<?php
/**
 * Class responsible for registering Language Switcher options.
 * * STANDARD: English (US) base language.
 * * UX: Clear description of what the option does.
 *
 * @package WPORLogin
 * @subpackage Customizer
 */

if ( ! class_exists( 'WPORLogin_Customizer_Language' ) ) {

    /**
     * Class WPORLogin_Customizer_Language
     */
    class WPORLogin_Customizer_Language {
            
        /**
         * Parent panel ID.
         * @var string
         */
        protected $panel_id;
        
        /**
         * Constructor.
         * @param string $panel_id ID of the parent Customizer panel.
         */
        public function __construct( $panel_id ) {
            $this->panel_id = $panel_id;
        }

        /**
         * Register settings and controls.
         * @param WP_Customize_Manager $wp_customize Customizer instance.
         */
        public function register( $wp_customize ) {
            
            // Section: Language Switcher.
            $description_text = sprintf(
                /* translators: %s: Link to video tutorial */
                __( 'Manage the visibility of the language switcher on the login screen.<br><br>Need help? %s', 'wporlogin' ),
                '<a href="#" target="_blank" style="color: #0073aa; text-decoration: none; font-weight: bold;">' . __( 'Watch Video Tutorial', 'wporlogin' ) . '</a>'
            );

            $wp_customize->add_section( 'wporlogin_language', array(
                'title'       => __( 'Language Switcher', 'wporlogin' ),
                'priority'    => 60,
                'panel'       => $this->panel_id,
                'description' => $description_text,
            ) );

            $this->add_language_visibility( $wp_customize );
        }

        /**
         * Add control to show/hide the language switcher.
         */
        private function add_language_visibility( $wp_customize ) {
            // Setting: remove_language_wporlogin
            $wp_customize->add_setting( 'remove_language_wporlogin', array(
                'default'           => 0,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint',
                'type'              => 'option',
            ) );

            $wp_customize->add_control( 'remove_language_wporlogin', array(
                'label'       => __( 'Language Switcher Visibility', 'wporlogin' ), // Cambio ligero en el título
                'description' => __( 'Choose whether to show or hide the language selector.', 'wporlogin' ),
                'section'     => 'wporlogin_language',
                'settings'    => 'remove_language_wporlogin',
                'type'        => 'select', // <--- CAMBIO CLAVE
                // Definimos las opciones explícitas
                'choices'     => array(
                    '0' => __( 'Show (Default)', 'wporlogin' ),
                    '1' => __( 'Hide', 'wporlogin' ),
                ),
            ) );
        }
    }
}
