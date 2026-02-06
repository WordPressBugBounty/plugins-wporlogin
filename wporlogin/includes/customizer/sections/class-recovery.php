<?php
/**
 * Class responsible for registering Recovery Form customization options.
 * * UX OPTIMIZED: Ordered by Container -> Spacing -> Border -> Content.
 * * STANDARD: English (US) base language.
 *
 * @package WPORLogin\Customizer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class WPORLogin_Customizer_Recovery
 */
class WPORLogin_Customizer_Recovery {
    
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

        // Section: Recovery Form.
        $description_text = sprintf(
            /* translators: %s: Link to video tutorial */
            __( 'Customize the colors, borders, and spacing of the Password Recovery Form.<br><br>Need help? %s', 'wporlogin' ),
            '<a href="#" target="_blank" style="color: #0073aa; text-decoration: none; font-weight: bold;">' . __( 'Watch Video Tutorial', 'wporlogin' ) . '</a>'
        );

        $wp_customize->add_section('wporlogin_sectionRecovery', array(
            'title'       => __( '🔑 Recovery Form', 'wporlogin' ),
            'priority'    => 110,
            'panel'       => $this->panel_id,
            'description' => $description_text,
        ));

        // --- 1. BACKGROUND & EFFECTS ---
        $this->add_background_settings( $wp_customize );

        // --- 2. SPACING ---
        $this->add_padding_settings( $wp_customize );

        // --- 3. BORDERS ---
        $this->add_border_settings( $wp_customize );
        $this->add_radius_settings( $wp_customize );

        // --- 4. CONTENT & BUTTON ---
        $this->add_text_settings( $wp_customize );
        $this->add_button_settings( $wp_customize );
    }

    // ============================================================
    // GROUP 1: BACKGROUND & EFFECTS
    // ============================================================

    private function add_background_settings( $wp_customize ) {
        // Background Color
        $this->add_color_control( $wp_customize, 'wporlogin_recovery_bg_color', __( 'Background Color', 'wporlogin' ), '#ffffff' );

        // Opacity (Vital for transparency fix)
        $this->add_select_control( $wp_customize, 'wporlogin_recovery_opacity', __( 'Background Opacity (Default: 1.0)', 'wporlogin' ), '1', array(
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
                "wporlogin_recovery_padding_{$side}", 
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
        $this->add_select_control( $wp_customize, 'wporlogin_recovery_border_style', __( 'Border Style (Default: Solid)', 'wporlogin' ), 'solid', array(
            'solid'  => __( 'Solid', 'wporlogin' ), 'dashed' => __( 'Dashed', 'wporlogin' ),
            'dotted' => __( 'Dotted', 'wporlogin' ), 'double' => __( 'Double', 'wporlogin' ),
            'groove' => __( 'Groove', 'wporlogin' ), 'ridge'  => __( 'Ridge', 'wporlogin' ),
            'inset'  => __( 'Inset', 'wporlogin' ),  'outset' => __( 'Outset', 'wporlogin' ),
            'none'   => __( 'None', 'wporlogin' )
        ));

        // Width
        $this->add_number_control( $wp_customize, 'wporlogin_recovery_border_width', __( 'Border Width (Default: 1px)', 'wporlogin' ), 1 );

        // Color
        $this->add_color_control( $wp_customize, 'wporlogin_recovery_border_color', __( 'Border Color', 'wporlogin' ), '#c3c4c7' );
    }

    private function add_radius_settings( $wp_customize ) {
        $this->add_radio_control( $wp_customize, 'wporlogin_recovery_radio', __( 'Rounded Corners', 'wporlogin' ), '0px', array(
            '0px'  => __( 'Square (0px)', 'wporlogin' ), 
            '5px'  => __( 'Slightly Rounded (5px)', 'wporlogin' ), 
            '10px' => __( 'Rounded (10px)', 'wporlogin' )
        ));
    }

    // ============================================================
    // GROUP 4: CONTENT & BUTTON
    // ============================================================

    private function add_text_settings( $wp_customize ) {
        $this->add_color_control( $wp_customize, 'wporlogin_recovery_p_color', __( 'Text Color (Labels)', 'wporlogin' ), '#3c434a' );
    }

    private function add_button_settings( $wp_customize ) {
        // Colors
        $this->add_color_control( $wp_customize, 'wporlogin_recovery_submit_button_bgcolor', __( 'Button Background', 'wporlogin' ), '#135e96' );
        $this->add_color_control( $wp_customize, 'wporlogin_recovery_submit_button_color', __( 'Button Text Color', 'wporlogin' ), '#ffffff' );
        $this->add_color_control( $wp_customize, 'wporlogin_recovery_submit_button_color_borde', __( 'Button Border Color', 'wporlogin' ), '#135e96' );

        // Layout
        $this->add_select_control( $wp_customize, 'wporlogin_recovery_submit_button_font_size', __( 'Button Font Size', 'wporlogin' ), '13px', array(
            '10px' => '10px', '11px' => '11px', '12px' => '12px', 
            '13px' => __( '13px (Default)', 'wporlogin' ), 
            '14px' => '14px', '15px' => '15px', '16px' => '16px', '17px' => '17px'
        ));

        $this->add_select_control( $wp_customize, 'wporlogin_recovery_submit_button_full_width', __( 'Button Width', 'wporlogin' ), 'auto|content-box', array(
            'auto|content-box' => __( 'Auto (Default)', 'wporlogin' ),
            '100%|border-box'  => __( 'Full Width (100%)', 'wporlogin' )
        ));

        $this->add_radio_control( $wp_customize, 'wporlogin_recovery_submit_button_padding', __( 'Button Padding', 'wporlogin' ), '0 12px', array(
            '0 12px'    => __( 'Default', 'wporlogin' ),
            '5px 12px'  => __( 'Small (5px)', 'wporlogin' ),
            '10px 12px' => __( 'Medium (10px)', 'wporlogin' )
        ));

        $this->add_radio_control( $wp_customize, 'wporlogin_recovery_submit_button_radio', __( 'Button Rounded Corners', 'wporlogin' ), '3px', array(
            '0px' => __( 'Square (0px)', 'wporlogin' ), 
            '3px' => __( 'Slightly Rounded (3px)', 'wporlogin' ), 
            '6px' => __( 'Rounded (6px)', 'wporlogin' )
        ));
    }

    // ============================================================
    // HELPER METHODS (Standardized)
    // ============================================================

    private function add_color_control( $wp_customize, $setting_id, $label, $default ) {
        $wp_customize->add_setting( $setting_id, array(
            'default' => $default, 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage', 'type' => 'option',
        ));
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "{$setting_id}_control", array(
            'label' => $label, 'section' => 'wporlogin_sectionRecovery', 'settings' => $setting_id,
        )));
    }

    private function add_select_control( $wp_customize, $setting_id, $label, $default, $choices ) {
        $wp_customize->add_setting( $setting_id, array(
            'default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage', 'type' => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label' => $label, 'section' => 'wporlogin_sectionRecovery', 'settings' => $setting_id, 'type' => 'select', 'choices' => $choices,
        ));
    }

    private function add_radio_control( $wp_customize, $setting_id, $label, $default, $choices ) {
        $wp_customize->add_setting( $setting_id, array(
            'default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage', 'type' => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label' => $label, 'section' => 'wporlogin_sectionRecovery', 'settings' => $setting_id, 'type' => 'radio', 'choices' => $choices,
        ));
    }

    private function add_number_control( $wp_customize, $setting_id, $label, $default ) {
        $wp_customize->add_setting( $setting_id, array(
            'default' => $default, 'sanitize_callback' => 'absint', 'transport' => 'postMessage', 'type' => 'option',
        ));
        $wp_customize->add_control( $setting_id, array(
            'label' => $label, 'section' => 'wporlogin_sectionRecovery', 'settings' => $setting_id, 'type' => 'number',
            'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
        ));
    }
}
