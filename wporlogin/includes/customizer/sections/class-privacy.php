<?php
/**
 * Class responsible for registering Privacy Policy customization options.
 * * UX OPTIMIZED: Ordered by Color -> Spacing.
 * * STANDARD: English (US) base language.
 *
 * @package WPORLogin
 * @subpackage Customizer
 */

if ( ! class_exists( 'WPORLogin_Customizer_Privacy' ) ) {

    /**
     * Class WPORLogin_Customizer_Privacy
     */
    class WPORLogin_Customizer_Privacy {
            
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
            
            // Section: Privacy Policy.
            $description_text = sprintf(
                /* translators: %s: Link to video tutorial */
                __( 'Customize the privacy policy link to comply with data protection regulations.<br><br>Need help? %s', 'wporlogin' ),
                '<a href="#" target="_blank" style="color: #0073aa; text-decoration: none; font-weight: bold;">' . __( 'Watch Video Tutorial', 'wporlogin' ) . '</a>'
            );

            $wp_customize->add_section( 'wporlogin_privacy', array(
                'title'       => __( 'Privacy Policy', 'wporlogin' ),
                'priority'    => 80,
                'panel'       => $this->panel_id,
                'description' => $description_text,
            ) );

            // --- 1. VISUAL STYLE ---
            $this->add_link_style( $wp_customize );

            // --- 2. SPACING (Margins) ---
            $this->add_spacing_settings( $wp_customize );
        }

        // ============================================================
        // GROUP 1: VISUAL STYLE
        // ============================================================

        /**
         * Add color setting for the privacy link.
         */
        private function add_link_style( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_privacy_color', array(
                'default'   => '#2271b1',
                'transport' => 'postMessage',
                'type'      => 'option',
                'sanitize_callback' => 'sanitize_hex_color',
            ) );

            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_privacy_color_control', array(
                'label'    => __( 'Link Color', 'wporlogin' ),
                'section'  => 'wporlogin_privacy',
                'settings' => 'wporlogin_privacy_color',
            ) ) );
        }

        // ============================================================
        // GROUP 2: SPACING (MARGINS)
        // ============================================================

        /**
         * Add margin controls (Top and Bottom).
         */
        private function add_spacing_settings( $wp_customize ) {
            
            // Margin Top
            $wp_customize->add_setting( 'wporlogin_privacy_margin_top', array(
                'default'           => 39,
                'transport'         => 'postMessage',
                'type'              => 'option',
                'sanitize_callback' => 'absint',
            ) );

            $wp_customize->add_control( 'wporlogin_privacy_margin_top_control', array(
                'label'       => __( 'Margin Top (Default: 39px)', 'wporlogin' ),
                'section'     => 'wporlogin_privacy',
                'settings'    => 'wporlogin_privacy_margin_top',
                'type'        => 'number',
                'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
            ) );

            // Margin Bottom
            $wp_customize->add_setting( 'wporlogin_privacy_margin_bottom', array(
                'default'           => 26,
                'transport'         => 'postMessage',
                'type'              => 'option',
                'sanitize_callback' => 'absint',
            ) );

            $wp_customize->add_control( 'wporlogin_privacy_margin_bottom_control', array(
                'label'       => __( 'Margin Bottom (Default: 26px)', 'wporlogin' ),
                'section'     => 'wporlogin_privacy',
                'settings'    => 'wporlogin_privacy_margin_bottom',
                'type'        => 'number',
                'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
            ) );
        }
    }
}
