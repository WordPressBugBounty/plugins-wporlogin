<?php
/**
 * Class responsible for registering Login Form customization options.
 * * UX IMPROVEMENT: Labels now include (Default: values) to guide the user.
 * * STANDARD: English (US) as base language.
 *
 * @package WPORLogin
 * @subpackage Customizer
 */

if ( ! class_exists( 'WPORLogin_Customizer_Form' ) ) {

    /**
     * Class WPORLogin_Customizer_Form
     */
    class WPORLogin_Customizer_Form {
            
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
         */
        public function register( $wp_customize ) {

            // Section: Login Form Main Container.
            $wp_customize->add_section( 'wporlogin_login_form_section', array(
                'title'       => __( 'Login Form', 'wporlogin' ),
                'priority'    => 20,
                'panel'       => $this->panel_id,
                'description' => __( 'Customize the main container of the login form.', 'wporlogin' ),
            ) );
            
            // --- 1. BACKGROUND & EFFECTS ---
            $this->add_background_settings( $wp_customize );
            $this->add_box_shadow( $wp_customize );

            // --- 2. SPACING ---
            $this->add_padding_settings( $wp_customize ); 
            $this->add_margin_settings( $wp_customize );

            // --- 3. BORDERS ---
            $this->add_border_settings( $wp_customize );
            $this->add_radius_settings( $wp_customize );

            // --- 4. CONTENT ---
            $this->add_text_settings( $wp_customize );
        }
            
        // ============================================================
        // GROUP 1: BACKGROUND & EFFECTS
        // ============================================================

        private function add_background_settings( $wp_customize ) {
            // Background Color
            $wp_customize->add_setting( 'wporlogin_form_bg_color', array(
                'default'           => '#ffffff',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_form_bg_color_control', array(
                'label'    => __( 'Background Color (Default: #ffffff)', 'wporlogin' ),
                'section'  => 'wporlogin_login_form_section',
                'settings' => 'wporlogin_form_bg_color',
            ) ) );

            // Opacity
            $wp_customize->add_setting( 'wporlogin_form_opacity', array(
                'default'           => '1',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_form_opacity', array(
                'label'    => __( 'Background Opacity (Default: 1.0)', 'wporlogin' ),
                'section'  => 'wporlogin_login_form_section',
                'settings' => 'wporlogin_form_opacity',
                'type'     => 'select',
                'choices'  => array(
                    '0'   => __( '0.0 (Transparent)', 'wporlogin' ), 
                    '0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', 
                    '0.4' => '0.4', '0.5' => '0.5', '0.6' => '0.6', 
                    '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', 
                    '1'   => __( '1.0 (Solid)', 'wporlogin' )
                ),
            ) );
        }

        private function add_box_shadow( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_form_box_shadow', array(
                'default'           => 'default',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_key',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_form_box_shadow_control', array(
                'label'    => __( 'Box Shadow (Default: Yes)', 'wporlogin' ),
                'section'  => 'wporlogin_login_form_section',
                'settings' => 'wporlogin_form_box_shadow',
                'type'     => 'select',
                'choices'  => array(
                    'default' => __( 'Yes (Subtle)', 'wporlogin' ),
                    'none'    => __( 'No (Flat)', 'wporlogin' ),
                ),
            ) );
        }

        // ============================================================
        // GROUP 2: SPACING (Padding & Margin)
        // ============================================================

        private function add_padding_settings( $wp_customize ) {
            $padding_fields = array( 'top' => 26, 'right' => 24, 'bottom' => 26, 'left' => 24 );
            
            foreach ( $padding_fields as $side => $default ) {
                $setting_id = "wporlogin_form_padding_{$side}";
                
                $wp_customize->add_setting( $setting_id, array(
                    'default'           => $default,
                    'sanitize_callback' => 'absint',
                    'transport'         => 'postMessage',
                    'type'              => 'option',
                ) );
                
                // Label Example: "Padding Top (Default: 26px)"
                $wp_customize->add_control( "{$setting_id}_control", array(
                    'label'       => sprintf( __( 'Padding %s (Default: %spx)', 'wporlogin' ), ucfirst($side), $default ),
                    'section'     => 'wporlogin_login_form_section',
                    'settings'    => $setting_id,
                    'type'        => 'number',
                    'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
                ) );
            }
        }

        private function add_margin_settings( $wp_customize ) {
            $defaults = array( 'top' => 24, 'right' => 0, 'bottom' => 24, 'left' => 0 );
            
            foreach ( $defaults as $side => $default ) {
                $setting_id = "wporlogin_form_margin_{$side}";

                $wp_customize->add_setting( $setting_id, array(
                    'default'           => $default,
                    'sanitize_callback' => 'absint',
                    'transport'         => 'postMessage',
                    'type'              => 'option',
                ) );
                
                // Label Example: "Margin Top (Default: 24px)"
                $wp_customize->add_control( "{$setting_id}_control", array(
                    'label'       => sprintf( __( 'Margin %s (Default: %spx)', 'wporlogin' ), ucfirst($side), $default ),
                    'section'     => 'wporlogin_login_form_section',
                    'settings'    => $setting_id,
                    'type'        => 'number',
                    'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
                ) );
            }
        }

        // ============================================================
        // GROUP 3: BORDERS
        // ============================================================

        private function add_border_settings( $wp_customize ) {
            // Style
            $wp_customize->add_setting( 'wporlogin_form_border_style', array(
                'default'           => 'solid',
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_form_border_style', array(
                'label'    => __( 'Border Style (Default: Solid)', 'wporlogin' ),
                'section'  => 'wporlogin_login_form_section',
                'settings' => 'wporlogin_form_border_style',
                'type'     => 'select',
                'choices'  => array(
                    'solid'  => __( 'Solid', 'wporlogin' ), 
                    'dashed' => __( 'Dashed', 'wporlogin' ),
                    'dotted' => __( 'Dotted', 'wporlogin' ), 
                    'double' => __( 'Double', 'wporlogin' ),
                    'none'   => __( 'None', 'wporlogin' ),
                ),
            ) );

            // Width
            $wp_customize->add_setting( 'wporlogin_form_border_width', array(
                'default'           => '1',
                'sanitize_callback' => 'absint',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_form_border_width', array(
                'label'       => __( 'Border Width (Default: 1px)', 'wporlogin' ),
                'section'     => 'wporlogin_login_form_section',
                'settings'    => 'wporlogin_form_border_width',
                'type'        => 'number',
                'input_attrs' => array( 'min' => 0, 'step' => 1 ),
            ) );

            // Color
            $wp_customize->add_setting( 'wporlogin_form_border_color', array(
                'default'           => '#c3c4c7',
                'sanitize_callback' => 'sanitize_hex_color',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_form_border_color', array(
                'label'    => __( 'Border Color (Default: #c3c4c7)', 'wporlogin' ),
                'section'  => 'wporlogin_login_form_section',
                'settings' => 'wporlogin_form_border_color',
            ) ) );
        }

        private function add_radius_settings( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_form_border_radius', array(
                'default'           => '0px',
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_form_border_radius', array(
                'label'    => __( 'Rounded Corners (Default: Square)', 'wporlogin' ),
                'section'  => 'wporlogin_login_form_section',
                'settings' => 'wporlogin_form_border_radius',
                'type'     => 'radio',
                'choices'  => array(
                    '0px'  => __( 'Square (0px)', 'wporlogin' ),
                    '5px'  => __( 'Slightly Rounded (5px)', 'wporlogin' ),
                    '10px' => __( 'Rounded (10px)', 'wporlogin' ),
                    '20px' => __( 'Very Rounded (20px)', 'wporlogin' ),
                ),
            ) );
        }

        // ============================================================
        // GROUP 4: CONTENT (TEXT)
        // ============================================================

        private function add_text_settings( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_form_p_color', array(
                'default'           => '#3c434a',
                'sanitize_callback' => 'sanitize_hex_color',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_form_p_color', array(
                'label'    => __( 'Text Color (Default: #3c434a)', 'wporlogin' ),
                'section'  => 'wporlogin_login_form_section',
                'settings' => 'wporlogin_form_p_color',
            ) ) );
        }
    }
}