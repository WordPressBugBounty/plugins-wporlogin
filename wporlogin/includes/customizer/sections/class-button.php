<?php
/**
 * Class responsible for registering Login Button customization options.
 * * UX OPTIMIZED: Ordered by Layout -> Colors -> Typography -> Radius.
 * * STANDARD: English (US) base language.
 *
 * @package WPORLogin
 * @subpackage Customizer
 */

if ( ! class_exists( 'WPORLogin_Customizer_Button' ) ) {

    /**
     * Class WPORLogin_Customizer_Button
     */
    class WPORLogin_Customizer_Button {
            
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
            
            // Section: Login Button.
            $description_text = sprintf(
                /* translators: %s: Link to video tutorial */
                __( 'Customize the style and size of the login submit button.<br><br>Need help? %s', 'wporlogin' ),
                '<a href="#" target="_blank" style="color: #0073aa; text-decoration: none; font-weight: bold;">' . __( 'Watch Video Tutorial', 'wporlogin' ) . '</a>'
            );

            $wp_customize->add_section( 'wporlogin_button', array(
                'title'       => __( 'Login Button', 'wporlogin' ),
                'priority'    => 40,
                'panel'       => $this->panel_id,
                'description' => $description_text,
            ) );

            // --- 1. LAYOUT & SIZING ---
            $this->add_layout_settings( $wp_customize );

            // --- 2. COLORS ---
            $this->add_color_settings( $wp_customize );

            // --- 3. TYPOGRAPHY ---
            $this->add_typography_settings( $wp_customize );

            // --- 4. BORDERS ---
            $this->add_radius_settings( $wp_customize );
        }

        // ============================================================
        // GROUP 1: LAYOUT & SIZING
        // ============================================================

        private function add_layout_settings( $wp_customize ) {
            // Width
            $wp_customize->add_setting( 'wporlogin_submit_button_full_width', array(
                'default'           => 'auto|content-box|0',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_submit_button_full_width', array(
                'label'    => __( 'Button Width', 'wporlogin' ),
                'section'  => 'wporlogin_button',
                'settings' => 'wporlogin_submit_button_full_width',
                'type'     => 'select',
                'choices'  => array(
                    'auto|content-box|0'    => __( 'Auto (Default)', 'wporlogin' ),
                    '100%|border-box|7px'   => __( 'Full Width (100%)', 'wporlogin' ),
                ),
            ) );

            // Padding
            $wp_customize->add_setting( 'wporlogin_submit_button_padding', array(
                'default'           => '0 12px',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_submit_button_padding_control', array(
                'label'    => __( 'Internal Padding', 'wporlogin' ),
                'section'  => 'wporlogin_button',
                'settings' => 'wporlogin_submit_button_padding',
                'type'     => 'radio',
                'choices'  => array(
                    '0 12px'    => __( 'Default', 'wporlogin' ),
                    '5px 12px'  => __( 'Small (5px)', 'wporlogin' ),
                    '10px 12px' => __( 'Medium (10px)', 'wporlogin' ),
                ),
            ) );
        }

        // ============================================================
        // GROUP 2: COLORS
        // ============================================================

        private function add_color_settings( $wp_customize ) {
            // Background Color
            $wp_customize->add_setting( 'wporlogin_submit_button_bgcolor', array(
                'default'           => '#135e96',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_submit_button_bgcolor_control', array(
                'label'    => __( 'Background Color (Default: #135e96)', 'wporlogin' ),
                'section'  => 'wporlogin_button',
                'settings' => 'wporlogin_submit_button_bgcolor',
            ) ) );

            // Text Color
            $wp_customize->add_setting( 'wporlogin_submit_button_color', array(
                'default'           => '#ffffff',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_submit_button_color_control', array(
                'label'    => __( 'Text Color (Default: #ffffff)', 'wporlogin' ),
                'section'  => 'wporlogin_button',
                'settings' => 'wporlogin_submit_button_color',
            ) ) );

            // Border Color
            $wp_customize->add_setting( 'wporlogin_submit_button_color_borde', array(
                'default'           => '#135e96',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_submit_button_color_borde_control', array(
                'label'    => __( 'Border Color (Default: #135e96)', 'wporlogin' ),
                'section'  => 'wporlogin_button',
                'settings' => 'wporlogin_submit_button_color_borde',
            ) ) );
        }

        // ============================================================
        // GROUP 3: TYPOGRAPHY
        // ============================================================

        private function add_typography_settings( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_submit_button_font_size', array(
                'default'           => '13px',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            
            // Build Font Size Array
            $font_choices = array();
            for ($i = 10; $i <= 17; $i++) {
                $val = $i . 'px';
                $label = ($i === 13) ? $val . ' (' . __( 'Default', 'wporlogin' ) . ')' : $val;
                $font_choices[$val] = $label;
            }

            $wp_customize->add_control( 'wporlogin_submit_button_font_size', array(
                'label'    => __( 'Font Size', 'wporlogin' ),
                'section'  => 'wporlogin_button',
                'settings' => 'wporlogin_submit_button_font_size',
                'type'     => 'select',
                'choices'  => $font_choices,
            ) );
        }

        // ============================================================
        // GROUP 4: BORDERS (RADIUS)
        // ============================================================

        private function add_radius_settings( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_submit_button_radio', array(
                'default'           => '3px',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_submit_button_radio_control', array(
                'label'    => __( 'Rounded Corners (Default: 3px)', 'wporlogin' ),
                'section'  => 'wporlogin_button',
                'settings' => 'wporlogin_submit_button_radio',
                'type'     => 'radio',
                'choices'  => array(
                    '0px' => __( 'Square (0px)', 'wporlogin' ),
                    '3px' => __( 'Slightly Rounded (3px)', 'wporlogin' ),
                    '6px' => __( 'Moderadamente redondeado (6px)', 'wporlogin' ),
                ),
            ) );
        }
    }
}
