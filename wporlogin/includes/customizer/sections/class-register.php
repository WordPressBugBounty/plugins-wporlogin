<?php
/**
 * Class responsible for registering Register Form customization options.
 * * INTERNATIONALIZATION FIXED: All user-facing strings are now wrapped in __() functions.
 *
 * @package WPORLogin\Customizer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class WPORLogin_Customizer_Register
 */
class WPORLogin_Customizer_Register {
    
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
     * Register section and controls in the Customizer.
     * @param WP_Customize_Manager $wp_customize Customizer instance.
     */
    public function register( $wp_customize ) {

        // 1. Add Main Section
        // Use sprintf for the description to keep HTML separate from translatable text (Best Practice)
        $description_text = sprintf(
            /* translators: %s: Link to video tutorial */
            __( 'Customize the colors, borders, and spacing of the WordPress Registration Form.<br><br>Need help? %s', 'wporlogin' ),
            '<a href="#" target="_blank" style="color: #0073aa; text-decoration: none; font-weight: bold;">' . __( 'Watch Video Tutorial', 'wporlogin' ) . '</a>'
        );

        $wp_customize->add_section('wporlogin_sectionRegister', array(
            'title'       => __( '✅ Register Form', 'wporlogin' ),
            'panel'       => $this->panel_id,
            'priority'    => 100,
            'description' => $description_text,
        ));

        // ============================================================
        // GROUP 1: CONTAINER & BACKGROUND (The Box)
        // ============================================================

        // Background Color
        $this->add_color_control( $wp_customize, 'wporlogin_register_bg_color', __( 'Form Background Color', 'wporlogin' ), '#ffffff' );

        // Opacity
        $this->add_select_control( $wp_customize, 'wporlogin_register_opacity', __( 'Form Opacity (Default: 1)', 'wporlogin' ), '1', array(
            '0'   => __( '0.0 (Transparent)', 'wporlogin' ), 
            '0.1' => '0.1', 
            '0.2' => '0.2', 
            '0.3' => '0.3', 
            '0.4' => '0.4',
            '0.5' => '0.5', 
            '0.6' => '0.6', 
            '0.7' => '0.7', 
            '0.8' => '0.8', 
            '0.9' => '0.9', 
            '1'   => __( '1.0 (Solid)', 'wporlogin' )
        ));

        // Padding (Internal Spacing)
        $this->add_number_control( $wp_customize, 'wporlogin_register_padding_top', __( 'Padding Top (Default: 26px)', 'wporlogin' ), 26 );
        $this->add_number_control( $wp_customize, 'wporlogin_register_padding_right', __( 'Padding Right (Default: 24px)', 'wporlogin' ), 24 );
        $this->add_number_control( $wp_customize, 'wporlogin_register_padding_bottom', __( 'Padding Bottom (Default: 26px)', 'wporlogin' ), 26 );
        $this->add_number_control( $wp_customize, 'wporlogin_register_padding_left', __( 'Padding Left (Default: 24px)', 'wporlogin' ), 24 );

        // ============================================================
        // GROUP 2: BORDERS (The Outline)
        // ============================================================

        // Border Style
        $this->add_select_control( $wp_customize, 'wporlogin_register_border_style', __( 'Border Style (Default: Solid)', 'wporlogin' ), 'solid', array(
            'solid'  => __( 'Solid', 'wporlogin' ),  
            'dashed' => __( 'Dashed', 'wporlogin' ), 
            'dotted' => __( 'Dotted', 'wporlogin' ), 
            'double' => __( 'Double', 'wporlogin' ), 
            'groove' => __( 'Groove', 'wporlogin' ), 
            'ridge'  => __( 'Ridge', 'wporlogin' ), 
            'inset'  => __( 'Inset', 'wporlogin' ),  
            'outset' => __( 'Outset', 'wporlogin' ), 
            'none'   => __( 'None', 'wporlogin' )
        ));

        // Border Width
        $this->add_number_control( $wp_customize, 'wporlogin_register_border_width', __( 'Border Width (Default: 1px)', 'wporlogin' ), 1 );

        // Border Color
        $this->add_color_control( $wp_customize, 'wporlogin_register_border_color', __( 'Border Color', 'wporlogin' ), '#c3c4c7' );

        // Border Radius (Rounded Corners)
        $this->add_radio_control( $wp_customize, 'wporlogin_register_radio', __( 'Form Rounded Corners', 'wporlogin' ), '0px', array(
            '0px'  => __( 'Square (0px)', 'wporlogin' ), 
            '5px'  => __( 'Slightly Rounded (5px)', 'wporlogin' ), 
            '10px' => __( 'Rounded (10px)', 'wporlogin' )
        ));

        // ============================================================
        // GROUP 3: CONTENT & LABELS
        // ============================================================

        $this->add_color_control( $wp_customize, 'wporlogin_register_p_color', __( 'Form Labels Color', 'wporlogin' ), '#3c434a' );

        // ============================================================
        // GROUP 4: SUBMIT BUTTON (The Action)
        // ============================================================
        
        // 4.1 Button Colors
        $this->add_color_control( $wp_customize, 'wporlogin_register_submit_button_bgcolor', __( 'Button Background Color', 'wporlogin' ), '#135e96' );
        $this->add_color_control( $wp_customize, 'wporlogin_register_submit_button_color', __( 'Button Text Color', 'wporlogin' ), '#ffffff' );
        
        // 4.2 Button Typography
        $this->add_select_control( $wp_customize, 'wporlogin_register_submit_button_font_size', __( 'Button Font Size', 'wporlogin' ), '13px', array(
            '10px' => '10px', 
            '11px' => '11px', 
            '12px' => '12px', 
            '13px' => __( '13px (Default)', 'wporlogin' ), 
            '14px' => '14px', 
            '15px' => '15px', 
            '16px' => '16px', 
            '17px' => '17px'
        ));

        // 4.3 Button Layout (Width & Padding)
        $this->add_select_control( $wp_customize, 'wporlogin_register_submit_button_full_width', __( 'Button Width', 'wporlogin' ), 'auto|content-box', array(
            'auto|content-box' => __( 'Auto (Default)', 'wporlogin' ),
            '100%|border-box'  => __( 'Full Width (100%)', 'wporlogin' )
        ));

        $this->add_radio_control( $wp_customize, 'wporlogin_register_submit_button_padding', __( 'Button Padding', 'wporlogin' ), '0 12px', array(
            '0 12px'    => __( 'Default', 'wporlogin' ),
            '5px 12px'  => __( 'Small (5px)', 'wporlogin' ),
            '10px 12px' => __( 'Medium (10px)', 'wporlogin' )
        ));

        // 4.4 Button Borders
        $this->add_color_control( $wp_customize, 'wporlogin_register_submit_button_color_borde', __( 'Button Border Color', 'wporlogin' ), '#135e96' );
        
        $this->add_radio_control( $wp_customize, 'wporlogin_register_submit_button_radio', __( 'Button Rounded Corners', 'wporlogin' ), '3px', array(
            '0px' => __( 'Square (0px)', 'wporlogin' ), 
            '3px' => __( 'Slightly Rounded (3px)', 'wporlogin' ), 
            '6px' => __( 'Rounded (6px)', 'wporlogin' )
        ));
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    /**
     * Helper to add a Color Control.
     */
    private function add_color_control( $wp_customize, $setting_id, $label, $default ) {
        $wp_customize->add_setting( $setting_id, array(
            'default'           => $default,
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
            'type'              => 'option',
        ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "{$setting_id}_control", array(
            'label'    => $label, // Ya viene traducido desde la llamada
            'section'  => 'wporlogin_sectionRegister',
            'settings' => $setting_id,
        )));
    }

    /**
     * Helper to add a Select (Dropdown) Control.
     */
    private function add_select_control( $wp_customize, $setting_id, $label, $default, $choices ) {
        $wp_customize->add_setting( $setting_id, array(
            'default'           => $default,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
            'type'              => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label'    => $label,
            'section'  => 'wporlogin_sectionRegister',
            'settings' => $setting_id,
            'type'     => 'select',
            'choices'  => $choices,
        ));
    }

    /**
     * Helper to add a Radio Control.
     */
    private function add_radio_control( $wp_customize, $setting_id, $label, $default, $choices ) {
        $wp_customize->add_setting( $setting_id, array(
            'default'           => $default,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
            'type'              => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label'    => $label,
            'section'  => 'wporlogin_sectionRegister',
            'settings' => $setting_id,
            'type'     => 'radio',
            'choices'  => $choices,
        ));
    }

    /**
     * Helper to add a Number Input Control.
     */
    private function add_number_control( $wp_customize, $setting_id, $label, $default ) {
        $wp_customize->add_setting( $setting_id, array(
            'default'           => $default,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
            'type'              => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label'       => $label,
            'section'     => 'wporlogin_sectionRegister',
            'settings'    => $setting_id,
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 0,
                'max'  => 100,
                'step' => 1,
            ),
        ));
    }
}
