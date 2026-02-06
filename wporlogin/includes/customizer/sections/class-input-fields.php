<?php
/**
 * Class responsible for registering Input Fields (User/Pass) customization options.
 * * UX OPTIMIZED: Ordered by Background -> Text -> Border.
 * * STANDARD: English (US) base language.
 *
 * @package WPORLogin
 * @subpackage Customizer
 */

if ( ! class_exists( 'WPORLogin_Customizer_Input_Fields' ) ) {

    /**
     * Class WPORLogin_Customizer_Input_Fields
     */
    class WPORLogin_Customizer_Input_Fields {

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

            // Section: Input Fields.
            $wp_customize->add_section( 'wporlogin_input_fields', array(
                'title'       => __( 'Input Fields', 'wporlogin' ),
                'priority'    => 30,
                'panel'       => $this->panel_id,
                'description' => __( 'Customize the style of the username and password fields.', 'wporlogin' ),
            ) );

            // --- 1. BACKGROUND ---
            $this->add_background_control( $wp_customize );

            // --- 2. TEXT ---
            $this->add_text_color_control( $wp_customize );

            // --- 3. BORDERS ---
            $this->add_border_controls( $wp_customize );
        }

        // ============================================================
        // GROUP 1: BACKGROUND
        // ============================================================

        private function add_background_control( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_input_bgcolor', array(
                'default'           => '#ffffff',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_input_bgcolor_control', array(
                'label'    => __( 'Input Background Color', 'wporlogin' ),
                'section'  => 'wporlogin_input_fields',
                'settings' => 'wporlogin_input_bgcolor',
            ) ) );
        }

        // ============================================================
        // GROUP 2: TEXT COLOR
        // ============================================================

        private function add_text_color_control( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_input_color', array(
                'default'           => '#000000',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_input_color_control', array(
                'label'    => __( 'Input Text Color', 'wporlogin' ),
                'section'  => 'wporlogin_input_fields',
                'settings' => 'wporlogin_input_color',
            ) ) );
        }

        // ============================================================
        // GROUP 3: BORDERS
        // ============================================================

        private function add_border_controls( $wp_customize ) {
            // Border Color
            $wp_customize->add_setting( 'wporlogin_input_color_borde', array(
                'default'           => '#8c8f94',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_input_color_borde_control', array(
                'label'    => __( 'Border Color', 'wporlogin' ),
                'section'  => 'wporlogin_input_fields',
                'settings' => 'wporlogin_input_color_borde',
            ) ) );

            // Border Radius
            $wp_customize->add_setting( 'wporlogin_input_radio', array(
                'default'           => '4px',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_input_radio_control', array(
                'label'    => __( 'Rounded Corners (Default: 4px)', 'wporlogin' ),
                'section'  => 'wporlogin_input_fields',
                'settings' => 'wporlogin_input_radio',
                'type'     => 'radio',
                'choices'  => array(
                    '0px'  => __( 'Square (0px)', 'wporlogin' ),
                    '4px'  => __( 'Slightly Rounded (4px)', 'wporlogin' ),
                    '8px'  => __( 'Rounded (8px)', 'wporlogin' ),
                ),
            ) );
        }
    }
}
