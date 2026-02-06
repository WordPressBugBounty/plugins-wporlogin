<?php
/**
 * Class responsible for registering Email Confirmation Form customization options.
 * * UX OPTIMIZED: Ordered by Container -> Spacing -> Border -> Content -> Buttons.
 * * STANDARD: English (US) base language.
 *
 * @package WPORLogin\Customizer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class WPORLogin_Customizer_EmailConfirm
 */
class WPORLogin_Customizer_EmailConfirm {
                                    
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
     * Register section and controls.
     * @param WP_Customize_Manager $wp_customize Customizer instance.
     */
    public function register( $wp_customize ) {

        // Section: Email Confirm Form.
        $description_text = sprintf(
            /* translators: %s: Link to video tutorial */
            __( 'Customize the colors, borders, and spacing of the Email Confirmation Form.<br><br>Need help? %s', 'wporlogin' ),
            '<a href="#" target="_blank" style="color: #0073aa; text-decoration: none; font-weight: bold;">' . __( 'Watch Video Tutorial', 'wporlogin' ) . '</a>'
        );

        $wp_customize->add_section('wporlogin_sectionEmailConfirm', array(
            'title'       => __( '✅ Email Confirm Form', 'wporlogin' ),
            'panel'       => $this->panel_id,
            'priority'    => 120,
            'description' => $description_text,
        ));

        // --- 1. BACKGROUND & EFFECTS (Container) ---
        $this->add_background_settings( $wp_customize );

        // --- 2. SPACING (Dimensions) ---
        $this->add_padding_settings( $wp_customize );

        // --- 3. BORDERS (Outline) ---
        $this->add_border_settings( $wp_customize );
        $this->add_radius_settings( $wp_customize );

        // --- 4. TEXT CONTENT ---
        $this->add_text_settings( $wp_customize );

        // --- 5. BUTTONS ---
        $this->add_buttons_settings( $wp_customize );
    }

    // ============================================================
    // GROUP 1: BACKGROUND & EFFECTS
    // ============================================================

    private function add_background_settings( $wp_customize ) {
        // Background Color
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_bg_color', __( 'Background Color', 'wporlogin' ), '#ffffff' );

        // Opacity (Critical for transparency fix)
        $this->add_select_control( $wp_customize, 'wporlogin_emailconfirm_opacity', __( 'Background Opacity (Default: 1.0)', 'wporlogin' ), '1', array(
            '0'   => __( '0.0 (Transparent)', 'wporlogin' ), 
            '0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', 
            '0.4' => '0.4', '0.5' => '0.5', '0.6' => '0.6', 
            '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', 
            '1'   => __( '1.0 (Solid)', 'wporlogin' )
        ));
    }

    // ============================================================
    // GROUP 2: SPACING
    // ============================================================

    private function add_padding_settings( $wp_customize ) {
        $padding_fields = array( 'top' => 26, 'right' => 24, 'bottom' => 26, 'left' => 24 );
        foreach ( $padding_fields as $side => $default ) {
            $this->add_number_control( 
                $wp_customize, 
                "wporlogin_emailconfirm_padding_{$side}", 
                sprintf( __( 'Padding %s (Default: %spx)', 'wporlogin' ), ucfirst($side), $default ), 
                $default 
            );
        }
    }

    // ============================================================
    // GROUP 3: BORDERS
    // ============================================================

    private function add_border_settings( $wp_customize ) {
        // Style
        $this->add_select_control( $wp_customize, 'wporlogin_emailconfirm_border_style', __( 'Border Style (Default: Solid)', 'wporlogin' ), 'solid', array(
            'solid'  => __( 'Solid', 'wporlogin' ), 'dashed' => __( 'Dashed', 'wporlogin' ),
            'dotted' => __( 'Dotted', 'wporlogin' ), 'double' => __( 'Double', 'wporlogin' ),
            'groove' => __( 'Groove', 'wporlogin' ), 'ridge'  => __( 'Ridge', 'wporlogin' ),
            'inset'  => __( 'Inset', 'wporlogin' ),  'outset' => __( 'Outset', 'wporlogin' )
        ));

        // Width
        $this->add_number_control( $wp_customize, 'wporlogin_emailconfirm_border_width', __( 'Border Width (Default: 1px)', 'wporlogin' ), 1 );

        // Color
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_border_color', __( 'Border Color', 'wporlogin' ), '#c3c4c7' );
    }

    private function add_radius_settings( $wp_customize ) {
        $this->add_radio_control( $wp_customize, 'wporlogin_emailconfirm_radio', __( 'Form Rounded Corners', 'wporlogin' ), '0px', array(
            '0px'  => __( 'Square (0px)', 'wporlogin' ), 
            '5px'  => __( 'Slightly Rounded (5px)', 'wporlogin' ), 
            '10px' => __( 'Rounded (10px)', 'wporlogin' )
        ));
    }

    // ============================================================
    // GROUP 4: TEXT CONTENT
    // ============================================================

    private function add_text_settings( $wp_customize ) {
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_h1_color', __( 'Heading Color (H1)', 'wporlogin' ), '#50575e' );
        $this->add_text_control( $wp_customize, 'wporlogin_emailconfirm_h1_size', __( 'Heading Font Size (Default: 27px)', 'wporlogin' ), '27px' );
        
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_p_color', __( 'Paragraph Text Color', 'wporlogin' ), '#3c434a' );
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_link_color', __( 'Link Color', 'wporlogin' ), '#135e96' );
    }

    // ============================================================
    // GROUP 5: BUTTONS
    // ============================================================

    private function add_buttons_settings( $wp_customize ) {
        // Button 1 (The link button)
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_button_one_bgcolor', __( 'Button 1: Background', 'wporlogin' ), '#2271b1' );
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_button_one_color', __( 'Button 1: Text Color', 'wporlogin' ), '#0a4b78' );
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_button_one_color_borde', __( 'Button 1: Border Color', 'wporlogin' ), '#0a4b78' );
        
        $this->add_radio_control( $wp_customize, 'wporlogin_emailconfirm_button_one_radio', __( 'Button 1: Radius', 'wporlogin' ), '3px', array(
            '0px' => __( 'Square', 'wporlogin' ), '3px' => __( 'Slight', 'wporlogin' ), '6px' => __( 'Rounded', 'wporlogin' )
        ));

        // Button 2 (The input button)
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_button_two_bgcolor', __( 'Button 2: Background', 'wporlogin' ), '#135e96' );
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_button_two_color', __( 'Button 2: Text Color', 'wporlogin' ), '#ffffff' );
        $this->add_color_control( $wp_customize, 'wporlogin_emailconfirm_button_two_color_borde', __( 'Button 2: Border Color', 'wporlogin' ), '#135e96' );

        $this->add_radio_control( $wp_customize, 'wporlogin_emailconfirm_button_two_radio', __( 'Button 2: Radius', 'wporlogin' ), '3px', array(
            '0px' => __( 'Square', 'wporlogin' ), '3px' => __( 'Slight', 'wporlogin' ), '6px' => __( 'Rounded', 'wporlogin' )
        ));
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    private function add_color_control( $wp_customize, $setting_id, $label, $default ) {
        $wp_customize->add_setting( $setting_id, array(
            'default' => $default, 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage', 'type' => 'option',
        ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "{$setting_id}_control", array(
            'label' => $label, 'section' => 'wporlogin_sectionEmailConfirm', 'settings' => $setting_id,
        )));
    }

    private function add_select_control( $wp_customize, $setting_id, $label, $default, $choices ) {
        $wp_customize->add_setting( $setting_id, array(
            'default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage', 'type' => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label' => $label, 'section' => 'wporlogin_sectionEmailConfirm', 'settings' => $setting_id, 'type' => 'select', 'choices' => $choices,
        ));
    }

    private function add_radio_control( $wp_customize, $setting_id, $label, $default, $choices ) {
        $wp_customize->add_setting( $setting_id, array(
            'default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage', 'type' => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label' => $label, 'section' => 'wporlogin_sectionEmailConfirm', 'settings' => $setting_id, 'type' => 'radio', 'choices' => $choices,
        ));
    }

    private function add_number_control( $wp_customize, $setting_id, $label, $default ) {
        $wp_customize->add_setting( $setting_id, array(
            'default' => $default, 'sanitize_callback' => 'absint', 'transport' => 'postMessage', 'type' => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label' => $label, 'section' => 'wporlogin_sectionEmailConfirm', 'settings' => $setting_id, 'type' => 'number',
            'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
        ));
    }

    private function add_text_control( $wp_customize, $setting_id, $label, $default ) {
        $wp_customize->add_setting( $setting_id, array(
            'default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage', 'type' => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label' => $label, 'section' => 'wporlogin_sectionEmailConfirm', 'settings' => $setting_id, 'type' => 'text',
        ));
    }
}