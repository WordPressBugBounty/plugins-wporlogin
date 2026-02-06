<?php
/**
 * Class responsible for registering Authentication Section options.
 * (The main container #login).
 * * * UX OPTIMIZED: Ordered by Identity (Logo) -> Layout -> Background -> Spacing -> Borders.
 * * STANDARD: English (US) base language.
 *
 * @package WPORLogin
 * @subpackage Customizer
 */

if ( ! class_exists( 'WPORLogin_Customizer_Authentication' ) ) {

    /**
     * Class WPORLogin_Customizer_Authentication
     */
    class WPORLogin_Customizer_Authentication {

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

            // Section: Authentication (Main Wrapper).
            $wp_customize->add_section( 'wporlogin_authentication_section', array(
                'title'       => __( 'Authentication Section', 'wporlogin' ),
                'priority'    => 20,
                'panel'       => $this->panel_id,
                'description' => __( 'Customize the main wrapper and logo of the login page.', 'wporlogin' ),
            ) );

            // --- 1. IDENTITY (LOGO) ---
            // Users usually want to set their brand first.
            $this->add_logo_image( $wp_customize );
            $this->add_logo_controls( $wp_customize );

            // --- 2. LAYOUT (POSITION) ---
            $this->add_centering_option( $wp_customize );

            // --- 3. BACKGROUND & EFFECTS ---
            $this->add_background_settings( $wp_customize );

            // --- 4. SPACING (PADDING) ---
            $this->add_padding_controls( $wp_customize );

            // --- 5. BORDERS ---
            $this->add_border_controls( $wp_customize );
            $this->add_border_radius( $wp_customize );
        }
                
        // ============================================================
        // GROUP 1: IDENTITY (LOGO)
        // ============================================================
        
        private function add_logo_image( $wp_customize ) {
            // Logo Image
            $wp_customize->add_setting( 'wporlogin_logo_image', array(
                'default'   => '',
                'transport' => 'postMessage',
                'type'      => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'wporlogin_logo_image_control', array(
                'label'    => __( 'Login Logo', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_logo_image',
            ) ) );

            // Visibility
            $wp_customize->add_setting( 'wporlogin_logo_visible', array(
                'default'   => 'block',
                'transport' => 'postMessage',
                'type'      => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_logo_visible', array(
                'label'    => __( 'Show/Hide Logo', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_logo_visible',
                'type'     => 'select',
                'choices'  => array(
                    'block' => __( 'Show (Default)', 'wporlogin' ),
                    'none'  => __( 'Hide', 'wporlogin' ),
                ),
            ) );
        }
        
        private function add_logo_controls( $wp_customize ) {
            // Width
            $wp_customize->add_setting( 'wporlogin_logo_width', array(
                'default'           => 84,
                'sanitize_callback' => 'absint',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_logo_width', array(
                'label'       => __( 'Logo Width (px)', 'wporlogin' ),
                'section'     => 'wporlogin_authentication_section',
                'settings'    => 'wporlogin_logo_width',
                'type'        => 'number',
                'input_attrs' => array( 'min' => 0, 'step' => 1 ),
            ) );

            // Height
            $wp_customize->add_setting( 'wporlogin_logo_height', array(
                'default'           => 84,
                'sanitize_callback' => 'absint',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_logo_height', array(
                'label'       => __( 'Logo Height (px)', 'wporlogin' ),
                'section'     => 'wporlogin_authentication_section',
                'settings'    => 'wporlogin_logo_height',
                'type'        => 'number',
                'input_attrs' => array( 'min' => 0, 'step' => 1 ),
            ) );

            // Position
            $wp_customize->add_setting( 'wporlogin_logo_position', array(
                'default'           => 'center top',
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_logo_position', array(
                'label'    => __( 'Logo Position', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_logo_position',
                'type'     => 'select',
                'choices'  => array(
                    'left top'   => __( 'Left', 'wporlogin' ),
                    'center top' => __( 'Center (Default)', 'wporlogin' ),
                    'right top'  => __( 'Right', 'wporlogin' ),
                ),
            ) );

            // Background Size
            $wp_customize->add_setting( 'wporlogin_logo_background_size', array(
                'default'           => 'contain',
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_logo_background_size', array(
                'label'    => __( 'Logo Fit', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_logo_background_size',
                'type'     => 'select',
                'choices'  => array(
                    'contain' => __( 'Contain (No Crop)', 'wporlogin' ),
                    'cover'   => __( 'Cover (May Crop)', 'wporlogin' ),
                    'auto'    => __( 'Auto', 'wporlogin' ),
                ),
            ) );
        }

        // ============================================================
        // GROUP 2: LAYOUT
        // ============================================================

        private function add_centering_option( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_auth_center_content', array(
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_auth_center_content', array(
                'label'    => __( 'Form Layout', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_auth_center_content',
                'type'     => 'select',
                'choices'  => array(
                    'default' => __( 'Default (Top)', 'wporlogin' ),
                    'center'  => __( 'Vertically Centered', 'wporlogin' ),
                ),
            ) );
        }

        // ============================================================
        // GROUP 3: BACKGROUND & OPACITY
        // ============================================================

        private function add_background_settings( $wp_customize ) {
            // Background Color
            $wp_customize->add_setting( 'wporlogin_auth_bg_color', array(
                'default'   => '#f0f0f1',
                'transport' => 'postMessage',
                'type'      => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_auth_bg_color', array(
                'label'    => __( 'Background Color', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_auth_bg_color',
            ) ) );

            // Opacity (Added explicitly here for the JS fix to work)
            $choices = array();
            for ( $i = 0; $i <= 10; $i++ ) {
                $val = (string)( $i / 10 );
                $label = ( $val === '1' ) ? $val . ' (' . __( 'Default', 'wporlogin' ) . ')' : $val;
                $choices[ $val ] = $label;
            }

            $wp_customize->add_setting( 'wporlogin_auth_opacity', array(
                'default'           => '1',
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_auth_opacity', array(
                'label'    => __( 'Background Opacity', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_auth_opacity',
                'type'     => 'select',
                'choices'  => $choices,
            ) );
        }

        // ============================================================
        // GROUP 4: SPACING (PADDING)
        // ============================================================

        private function add_padding_controls( $wp_customize ) {
            $positions = array(
                'top'    => array( 'label' => 'Top',    'default' => '5%' ),
                'right'  => array( 'label' => 'Right',  'default' => '0px' ),
                'bottom' => array( 'label' => 'Bottom', 'default' => '0px' ),
                'left'   => array( 'label' => 'Left',   'default' => '0px' ),
            );

            // Generate px options
            $base_choices = array();
            for ( $i = 0; $i <= 40; $i += 5 ) {
                $base_choices[ $i . 'px' ] = $i . 'px';
            }

            foreach ( $positions as $key => $data ) {
                $setting_id = "wporlogin_auth_padding_{$key}";

                // Default choice logic
                $choices = array(
                    $data['default'] => $data['default'] . ' (' . __( 'Default', 'wporlogin' ) . ')'
                ) + $base_choices;

                $wp_customize->add_setting( $setting_id, array(
                    'default'           => $data['default'],
                    'transport'         => 'postMessage',
                    'type'              => 'option',
                ) );

                $wp_customize->add_control( $setting_id, array(
                    'label'    => sprintf( __( 'Padding %s', 'wporlogin' ), $data['label'] ),
                    'section'  => 'wporlogin_authentication_section',
                    'settings' => $setting_id,
                    'type'     => 'select',
                    'choices'  => $choices,
                ) );
            }
        }

        // ============================================================
        // GROUP 5: BORDERS
        // ============================================================

        private function add_border_controls( $wp_customize ) {
            // Width
            $wp_customize->add_setting( 'wporlogin_auth_border_width', array(
                'default'           => '0',
                'sanitize_callback' => 'absint',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_auth_border_width', array(
                'label'       => __( 'Border Width (px)', 'wporlogin' ),
                'section'     => 'wporlogin_authentication_section',
                'settings'    => 'wporlogin_auth_border_width',
                'type'        => 'number',
                'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
            ) );

            // Style
            $wp_customize->add_setting( 'wporlogin_auth_border_style', array(
                'default'           => 'solid',
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_auth_border_style', array(
                'label'    => __( 'Border Style', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_auth_border_style',
                'type'     => 'select',
                'choices'  => array(
                    'solid'  => __( 'Solid', 'wporlogin' ), 'dashed' => __( 'Dashed', 'wporlogin' ),
                    'dotted' => __( 'Dotted', 'wporlogin' ), 'double' => __( 'Double', 'wporlogin' ),
                    'groove' => __( 'Groove', 'wporlogin' ), 'ridge'  => __( 'Ridge', 'wporlogin' ),
                    'inset'  => __( 'Inset', 'wporlogin' ),  'outset' => __( 'Outset', 'wporlogin' ),
                ),
            ) );

            // Color
            $wp_customize->add_setting( 'wporlogin_auth_border_color', array(
                'default'           => '#000000',
                'sanitize_callback' => 'sanitize_hex_color',
                'transport'         => 'postMessage',
                'type'              => 'option',
            ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_auth_border_color', array(
                'label'    => __( 'Border Color', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_auth_border_color',
            ) ) );
        }

        private function add_border_radius( $wp_customize ) {
            $wp_customize->add_setting( 'wporlogin_auth_border_radius', array(
                'default'   => '0px',
                'transport' => 'postMessage',
                'type'      => 'option',
            ) );
            $wp_customize->add_control( 'wporlogin_auth_border_radius', array(
                'label'    => __( 'Border Radius', 'wporlogin' ),
                'section'  => 'wporlogin_authentication_section',
                'settings' => 'wporlogin_auth_border_radius',
                'type'     => 'select',
                'choices'  => array(
                    '0px'  => __( 'Square (Default)', 'wporlogin' ),
                    '5px'  => __( '5px', 'wporlogin' ),
                    '10px' => __( '10px', 'wporlogin' ),
                ),
            ) );
        }
    }
}