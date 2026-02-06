<?php
/**
 * Class responsible for registering Links & Extra settings options.
 * * UX OPTIMIZED: Ordered by Color -> Alignment -> Language Switcher.
 * * STANDARD: English (US) base language.
 *
 * @package WPORLogin
 * @subpackage Customizer
 */

if ( ! class_exists( 'WPORLogin_Customizer_Links' ) ) {

    /**
     * Class WPORLogin_Customizer_Links
     */
    class WPORLogin_Customizer_Links {
                        
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
            
            // Section: Links & Extras.
            $description_text = sprintf(
                /* translators: %s: Link to video tutorial */
                __( 'Customize the login links ("Lost your password?", "Back to site") and other extra settings.<br><br>Need help? %s', 'wporlogin' ),
                '<a href="#" target="_blank" style="color: #0073aa; text-decoration: none; font-weight: bold;">' . __( 'Watch Video Tutorial', 'wporlogin' ) . '</a>'
            );

            $wp_customize->add_section( 'wporlogin_links', array(
                'title'       => __( 'Links & Extras', 'wporlogin' ),
                'priority'    => 50,
                'panel'       => $this->panel_id,
                'description' => $description_text,
            ) );

            // --- 1. LINKS STYLE ---
            $this->add_link_style( $wp_customize );
        }

        // ============================================================
        // GROUP 1: LINKS STYLE
        // ============================================================

        private function add_link_style( $wp_customize ) {
            // Link Color
            $wp_customize->add_setting( 'wporlogin_link_color', array(
                'default'           => '#50575e',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_link_color_control', array(
                'label'    => __( 'Link Color', 'wporlogin' ),
                'section'  => 'wporlogin_links',
                'settings' => 'wporlogin_link_color',
            ) ) );

            // Link Alignment
            $wp_customize->add_setting( 'wporlogin_link_align', array(
                'default'           => 'left',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_link_align', array(
                'label'    => __( 'Link Alignment', 'wporlogin' ),
                'section'  => 'wporlogin_links',
                'settings' => 'wporlogin_link_align',
                'type'     => 'select',
                'choices'  => array(
                    'left'   => __( 'Left (Default)', 'wporlogin' ),
                    'center' => __( 'Center', 'wporlogin' ),
                    'right'  => __( 'Right', 'wporlogin' ),
                ),
            ) );
        }
    }
}
