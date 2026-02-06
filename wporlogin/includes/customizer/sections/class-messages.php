<?php
/**
 * Class responsible for registering Error Messages customization options.
 * * UX OPTIMIZED: Ordered by Visibility -> Border -> Background -> Text -> Links.
 * * STANDARD: English (US) base language.
 *
 * @package WPORLogin
 * @subpackage Customizer
 */

if ( ! class_exists( 'WPORLogin_Customizer_Messages' ) ) {

    /**
     * Class WPORLogin_Customizer_Messages
     */
    class WPORLogin_Customizer_Messages {
                        
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
            
            // Section: Messages & Notices.
            $description_text = sprintf(
                /* translators: %s: Link to video tutorial */
                __( 'Customize the appearance of authentication messages (errors, success, notices).<br><br>Need help? %s', 'wporlogin' ),
                '<a href="#" target="_blank" style="color: #0073aa; text-decoration: none; font-weight: bold;">' . __( 'Watch Video Tutorial', 'wporlogin' ) . '</a>'
            );

            $wp_customize->add_section( 'wporlogin_messages', array(
                'title'       => __( 'Messages & Notices', 'wporlogin' ),
                'priority'    => 90,
                'panel'       => $this->panel_id,
                'description' => $description_text,
            ) );

            // --- 1. PREVIEW CONTROL ---
            $this->add_error_visibility( $wp_customize );

            // --- 2. BORDER STYLE ---
            $this->add_border_style( $wp_customize );

            // --- 3. BACKGROUND ---
            $this->add_background_style( $wp_customize );

            // --- 4. TEXT & LINKS ---
            $this->add_text_style( $wp_customize );
        }

        // ============================================================
        // GROUP 1: PREVIEW CONTROL
        // ============================================================

        /**
         * Control to toggle error message visibility for editing purposes.
         */
        private function add_error_visibility( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_error_visible', array(
                'default'           => 'none',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
                'type'              => 'option',
            ) );
            
            $wp_customize->add_control( 'wporlogin_error_visible', array(
                'label'       => __( 'Show Error Message (Preview Mode)', 'wporlogin' ),
                'description' => __( 'Enable this to see a sample error message while you design.', 'wporlogin' ),
                'section'     => 'wporlogin_messages',
                'settings'    => 'wporlogin_error_visible',
                'type'        => 'select',
                'choices'     => array(
                    'block' => __( 'Show', 'wporlogin' ),
                    'none'  => __( 'Hide (Default)', 'wporlogin' ),
                ),
            ) );
        }

        // ============================================================
        // GROUP 2: BORDER STYLE
        // ============================================================

        private function add_border_style( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_color_border_left_error', array(
                'default'           => '#d63638',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'              => 'option',
            ) );
            
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_color_border_left_error_control', array(
                'label'    => __( 'Left Border Color (Default: #d63638)', 'wporlogin' ),
                'section'  => 'wporlogin_messages',
                'settings' => 'wporlogin_color_border_left_error',
            ) ) );
        }

        // ============================================================
        // GROUP 3: BACKGROUND
        // ============================================================

        private function add_background_style( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_color_bg_error', array(
                'default'           => '#ffffff',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'              => 'option',
            ) );
            
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_color_bg_error_control', array(
                'label'    => __( 'Background Color (Default: #ffffff)', 'wporlogin' ),
                'section'  => 'wporlogin_messages',
                'settings' => 'wporlogin_color_bg_error',
            ) ) );
        }

        // ============================================================
        // GROUP 4: TEXT & LINKS
        // ============================================================

        private function add_text_style( $wp_customize ) {
            // Text Color
            $wp_customize->add_setting( 'wporlogin_color_parrafo_error', array(
                'default'           => '#3c434a',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'              => 'option',
            ) );
            
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_color_parrafo_error_control', array(
                'label'    => __( 'Text Color (Default: #3c434a)', 'wporlogin' ),
                'section'  => 'wporlogin_messages',
                'settings' => 'wporlogin_color_parrafo_error',
            ) ) );

            // Link Color
            $wp_customize->add_setting( 'wporlogin_color_link_error', array(
                'default'           => '#2271b1',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'              => 'option',
            ) );
            
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_color_link_error_control', array(
                'label'    => __( 'Link Color (Default: #2271b1)', 'wporlogin' ),
                'section'  => 'wporlogin_messages',
                'settings' => 'wporlogin_color_link_error',
            ) ) );
        }
    }
}