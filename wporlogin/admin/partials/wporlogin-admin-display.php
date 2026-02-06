<?php
/**
 * Provee la vista de administración para el plugin.
 * ESTILO: "Apple Clean & Professional" - Grid 4 Columnas
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/admin/partials
 */

// -----------------------------------------------------------------------------
// 1. CONFIGURACIÓN DE DISEÑOS (DATA)
// -----------------------------------------------------------------------------
$available_designs = [
    'wporlogin_design_basic' => [
        'img'           => 'wporlogin-design-basic.jpg',
        'label'         => __( 'Basic', 'wporlogin' ),
        'hide_settings' => true,
    ],
    'wporlogin_design_standard' => [
        'img'           => 'wporlogin-design-standard.jpg',
        'label'         => __( 'Standard', 'wporlogin' ),
        'hide_settings' => false,
    ],
    'wporlogin_design_img_premium_one' => [
        'img'           => 'wporlogin-design-premium-one.jpg',
        'label'         => __( 'Premium One', 'wporlogin' ),
        'hide_settings' => false,
    ],
    'wporlogin_design_img_premium_two' => [
        'img'           => 'wporlogin-design-premium-two.jpg',
        'label'         => __( 'Premium Two', 'wporlogin' ),
        'hide_settings' => false,
    ],
    'wporlogin_design_img_premium_three' => [
        'img'           => 'wporlogin-design-premium-three.jpg',
        'label'         => __( 'Premium Three', 'wporlogin' ),
        'hide_settings' => false,
    ],
        'wporlogin_design_img_premium_four' => [
        'img'           => 'wporlogin-design-premium-four.jpg',
        'label'         => __( 'Premium Four', 'wporlogin' ),
        'hide_settings' => false,
    ],
        'wporlogin_design_img_premium_five' => [
        'img'           => 'wporlogin-design-premium-five.jpg',
        'label'         => __( 'Premium Five', 'wporlogin' ),
        'hide_settings' => false,
    ],
        'wporlogin_design_img_premium_six' => [
        'img'           => 'wporlogin-design-premium-six.jpg',
        'label'         => __( 'Premium Six', 'wporlogin' ),
        'hide_settings' => false,
    ],
];

// Recuperamos opción actual guardada en DB
$active_design = get_option( 'wporlogin_active_design', 'wporlogin_design_basic' );

// LÓGICA DE VISIBILIDAD INICIAL (PHP)
$brand_display_style = ($active_design === 'wporlogin_design_img_premium_zero') ? 'display: none;' : '';
$settings_display_style = ($active_design === 'wporlogin_design_img_premium_zero' || $active_design === 'wporlogin_design_basic') ? 'display: none;' : '';
?>

<style type="text/css">
    /* Reset básico */
    .wporlogin-design-wrapper {
        background-color: #F5F5F7;
        padding: 20px 40px 40px 40px;
        border-radius: 16px;
        margin-top: 20px;
        box-sizing: border-box;
    }

    .wporlogin-section-title {
        text-align: center;
        font-size: 24px;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 30px;
        letter-spacing: -0.01em;
    }

    /* --- HERO CARD (CUSTOMIZER) --- */
    .wporlogin-hero-container {
        display: flex;
        justify-content: center;
        margin-bottom: 40px;
    }

    .wporlogin-card-hero {
        position: relative;
        width: 100%;
        max-width: 600px;
        height: 320px; /* Un poco más alto para los mensajes */
        border-radius: 16px;
        overflow: hidden;
        background: #2c3338;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        cursor: pointer;
        border: 4px solid transparent; /* Borde más visible */
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease, border-color 0.3s ease;
    }

    .wporlogin-card-hero:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    }

    /* Estado Seleccionado */
    .wporlogin-card-hero.selected-design {
        border-color: #0071e3;
    }

    .wporlogin-card-hero img.bg-hero {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.4);
        transition: filter 0.5s ease, transform 0.8s ease;
    }

    .wporlogin-card-hero:hover img.bg-hero {
        transform: scale(1.02);
    }

    .wporlogin-hero-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        text-align: center;
        z-index: 10;
        color: white;
        padding: 0 20px;
        box-sizing: border-box;
    }

    /* --- BOTÓN INTELIGENTE --- */
    .wporlogin-btn-hero {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 28px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 15px;
        text-decoration: none;
        margin-top: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        min-width: 200px;
    }

    /* Estado: Listo (Azul) */
    .wporlogin-btn-hero.state-ready {
        background-color: #02a335ff;
        color: white;
    }
    .wporlogin-btn-hero.state-ready:hover {
        background-color: #03a01aff;
        transform: scale(1.05);
    }

    /* Estado: Advertencia (Amarillo - Necesita Guardar) */
    .wporlogin-btn-hero.state-warning {
        background-color: #FFB900;
        color: #1d1d1f;
        cursor: not-allowed; /* Indicamos que no debe clicar el link, sino guardar */
    }
    
    /* Estado: Inactivo (Gris - Solo selección) */
    .wporlogin-btn-hero.state-inactive {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .wporlogin-btn-hero.state-inactive:hover {
        background-color: rgba(255, 255, 255, 0.4);
    }

    /* Mensaje de ayuda */
    .wporlogin-hero-hint {
        margin-top: 10px;
        font-size: 13px;
        color: #FFB900;
        font-weight: 500;
        opacity: 0;
        transition: opacity 0.3s;
        height: 20px;
    }
    .wporlogin-hero-hint.visible {
        opacity: 1;
    }

    /* --- GRID Y BADGES --- */
    .wporlogin-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        width: 100%;
    }
    
    @media (max-width: 1400px) { .wporlogin-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 1000px) { .wporlogin-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px)  { .wporlogin-grid { grid-template-columns: 1fr; } }

    .wporlogin-card-preset {
        position: relative;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 16/9;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .wporlogin-card-preset:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    .wporlogin-card-preset.selected-design {
        border-color: #0071e3;
        box-shadow: 0 0 0 1px #0071e3;
    }
    .wporlogin-card-preset img.preset-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center;
        transition: transform 0.6s ease;
    }
    .wporlogin-card-preset:hover img.preset-img { transform: scale(1.04); }
    
    .wporlogin-preset-overlay {
        position: absolute; bottom: 0; left: 0; width: 100%; padding: 15px;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
        display: flex; align-items: flex-end; pointer-events: none;
    }
    .wporlogin-preset-title { color: white; font-weight: 500; font-size: 14px; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }

    .wporlogin-check-badge {
        position: absolute; bottom: 15px; right: 15px; width: 28px; height: 28px;
        background-color: #0071e3; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2); opacity: 0; transform: scale(0.5);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); z-index: 20; pointer-events: none;
    }
    .wporlogin-check-badge span { color: #fff; font-size: 18px; margin-top: 1px; }
    .selected-design .wporlogin-check-badge { opacity: 1; transform: scale(1); }
    
    .wporlogin-radio-hidden { position: absolute; opacity: 0; width: 0; height: 0; }
    .wp-picker-container { vertical-align: middle; }
</style>

<div class="wrap"> 
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; background-color: #ffffff; padding-top: 5px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; margin-top: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.16), 0 1px 2px rgba(0,0,0,0.23);">
        <img src="<?php echo WPORLOGIN_URL . 'assets/img/logo-wporlogin.png'; ?>" style="margin-left: 20px; height: 48px;">
    </div>
    
    <div style="width: 95%; margin-left: auto; margin-right: auto; position: relative;">       
        
        <h1 style="text-align: center; font-size: 34px; padding-top: 30px; font-weight: bold; font-family: 'Roboto', sans-serif;"><strong><?php _e('Appearance', 'wporlogin'); ?></strong></h1>  
        
        <p style="margin-bottom: 20px; text-align: center; font-family: 'Roboto', sans-serif; font-size: 16px; margin-top: 5px; margin-bottom: 40px;"><?php _e('<strong>WPORLogin</strong> allows you to customize the appearance of the WordPress login page.', 'wporlogin'); ?></p>
    
        <?php settings_errors(); ?>
                
        <form method="post" action="<?php echo esc_url(admin_url('options.php') ); ?>">
            
            <?php 
            wp_nonce_field(basename(__FILE__), 'wporlogin_form_nonce'); 
            ?>
            
            <?php settings_fields( 'wporlogin_custom_admin_settings_group' ); ?>
            <?php do_settings_sections( 'wporlogin_custom_admin_settings_group' ); ?>

            <div style="padding-bottom: 50px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
                
                <div class="wporlogin-design-wrapper">
                    
                    <h2 class="wporlogin-section-title"><?php _e('Choose your Login Style', 'wporlogin'); ?></h2>

                    <?php
                    // URL del Personalizador
                    $ghost_page_id = get_option( 'wporlogin_page_id' );
                    $preview_url   = $ghost_page_id ? get_permalink( $ghost_page_id ) : home_url();
                    $customize_url = add_query_arg(
                        array('autofocus[panel]' => 'wporlogin_panel_login', 'url' => urlencode( $preview_url )),
                        admin_url( 'customize.php' )
                    );
                    $is_custom_active = ($active_design === 'wporlogin_design_img_premium_zero');
                    ?>

                    <div class="wporlogin-hero-container">
                        <div id="wporlogin-hero-card" class="wporlogin-card-hero wporlogin-card-trigger <?php echo $is_custom_active ? 'selected-design' : ''; ?>">
                            
                            <input <?php checked( 'wporlogin_design_img_premium_zero', $active_design ); ?>
                                value="wporlogin_design_img_premium_zero" 
                                name="wporlogin_active_design" 
                                id="wporlogin_design_img_premium_zero"
                                type="radio"
                                class="wporlogin-radio-hidden"
                                data-hide="true">
                            
                            <img class="bg-hero" src="<?php echo WPORLOGIN_URL . 'assets/img/wporlogin-design-premium-custom.png'; ?>">

                            <div class="wporlogin-hero-content">
                                <div style="font-size: 48px; margin-bottom: 10px; opacity: 0.9;">
                                    <span class="dashicons dashicons-art" style="font-size: 48px; width: 48px; height: 48px;"></span>
                                </div>
                                <h3 style="margin: 0; font-size: 24px; color: white;"><?php _e('Custom Design Builder', 'wporlogin'); ?></h3>
                                <p style="margin: 10px 0 20px 0; opacity: 0.8; font-size: 14px;"><?php _e('Full control. Create your unique style using the live customizer.', 'wporlogin'); ?></p>
                                
                                <a id="wporlogin-hero-btn" class="wporlogin-btn-hero state-inactive wporlogin-prevent-select" href="#">
                                    <span id="wporlogin-hero-icon" class="dashicons dashicons-admin-customizer" style="margin-right: 8px; display: none;"></span>
                                    <span id="wporlogin-hero-text"><?php _e('Select Custom Design', 'wporlogin'); ?></span>
                                </a>
                                
                                <div id="wporlogin-hero-hint" class="wporlogin-hero-hint">
                                    ⚠️ <?php _e('Don\'t forget to click "Save Changes" below first!', 'wporlogin'); ?>
                                </div>
                            </div>

                            <div class="wporlogin-check-badge">
                                <span class="dashicons dashicons-yes"></span>
                            </div>
                        </div>
                    </div>

                    <div class="wporlogin-grid">
                        <?php foreach ( $available_designs as $design_key => $design_data ) : 
                            $data_hide_attr = $design_data['hide_settings'] ? 'true' : 'false';
                            $is_selected    = ($design_key === $active_design);
                        ?>
                        <div class="wporlogin-card-preset wporlogin-card-trigger <?php echo $is_selected ? 'selected-design' : ''; ?>"
                            onclick="wporloginSelectRadio('<?php echo esc_js( $design_key ); ?>')">
                            
                            <input <?php checked( $design_key, $active_design ); ?> 
                                value="<?php echo esc_attr( $design_key ); ?>" 
                                name="wporlogin_active_design" 
                                id="<?php echo esc_attr( $design_key ); ?>" 
                                type="radio" 
                                class="wporlogin-radio-hidden"
                                data-hide="<?php echo esc_attr( $data_hide_attr ); ?>">
                            
                            <img class="preset-img" 
                                src="<?php echo WPORLOGIN_URL . 'assets/img/' . esc_attr( $design_data['img'] ); ?>" 
                                alt="<?php echo esc_attr( $design_data['label'] ); ?>">

                            <div class="wporlogin-preset-overlay">
                                <span class="wporlogin-preset-title"><?php echo esc_html( $design_data['label'] ); ?></span>
                            </div>

                            <div class="wporlogin-check-badge">
                                <span class="dashicons dashicons-yes"></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="wporlogin-container-design-premium" style="padding-top: 40px; width: 90%; margin-left: auto; margin-right: auto;">
                    
                    <table id="wporlogin-table-brand-color" class="form-table" role="presentation" style="margin-bottom: 0; <?php echo $brand_display_style; ?>">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label style="font-size: 1.5em;"><strong><?php _e('Brand Color', 'wporlogin'); ?></strong></label>
                                </th>
                                <td><hr></td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="wporlogin_color_principal_marca"><?php _e('Primary Color', 'wporlogin'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="wporlogin_color_principal_marca" name="wporlogin_color_principal_marca" 
                                        value="<?php echo esc_attr(get_option('wporlogin_color_principal_marca', '#1a73e8')); ?>" 
                                        class="wporlogin-color-picker" data-default-color="#1a73e8" />
                                    <p class="description"><?php _e('Define the primary color of your brand.', 'wporlogin'); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div id="wporlogin-container-settings-table" style="<?php echo $settings_display_style; ?>">
                        <table class="form-table" role="presentation">    
                            <tbody>
                                <tr>
                                    <th scope="row"><label style="font-size: 1.5em;"><strong><?php _e('Logo', 'wporlogin'); ?></strong></label></th>
                                    <td><hr></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="wporlogin_url_logotipo_text"><?php _e('Logo', 'wporlogin'); ?></label></th>
                                    <td>
                                        <?php if(esc_url(get_option('wporlogin_url_logotipo'))){ ?>
                                            <img id="wporlogin_url_logotipo_img" src="<?php echo esc_url(get_option('wporlogin_url_logotipo')); ?>" style="background-color: #ffffff; margin-bottom: 10px; width: 220px; padding: 10px;  border: 2px dashed rgba(0,0,0,.1);"><br>
                                        <?php } ?>
                                        <input id="wporlogin_url_logotipo_text" type="text" name="wporlogin_url_logotipo" class="regular-text" style="margin-bottom: 10px;" value="<?php echo esc_url(get_option('wporlogin_url_logotipo')); ?>"/><br>
                                        <input id="wporlogin_url_logotipo_button" type="button" class="button" value="<?php _e('Upload logo', 'wporlogin'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="wporlogin_ruta_url_logotipo_text"><?php _e('Logo URL', 'wporlogin'); ?></label></th>
                                    <td><input id="wporlogin_ruta_url_logotipo_text" type="text" name="wporlogin_ruta_url_logotipo" class="regular-text" value="<?php echo esc_html(get_option('wporlogin_ruta_url_logotipo')); ?>"/></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="wporlogin_titulo_logotipo_text"><?php _e('Logo title', 'wporlogin'); ?></label></th>
                                    <td><input id="wporlogin_titulo_logotipo_text" type="text" name="wporlogin_titulo_logotipo" class="regular-text" value="<?php echo esc_html(get_option('wporlogin_titulo_logotipo')); ?>"/></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label style="font-size: 1.5em;"><strong><?php _e('Background image', 'wporlogin'); ?></strong></label></th>
                                    <td><hr></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label><?php _e('Background image', 'wporlogin'); ?></label></th>
                                    <td>
                                        <div style="margin-bottom: 20px;">
                                            <input type="radio" id="wporlogin_free_images" name="wporlogin_background_images" value="wporlogin_free_images" <?php checked(get_option('wporlogin_background_images'), 'wporlogin_free_images'); ?>>
                                            <label for="wporlogin_free_images"><?php _e('Free images', 'wporlogin'); ?></label>
                                            <input type="radio" id="wporlogin_my_images" name="wporlogin_background_images" value="wporlogin_my_images" <?php checked(get_option('wporlogin_background_images'), 'wporlogin_my_images'); ?> style="margin-left: 20px;">
                                            <label for="wporlogin_my_images"><?php _e('My images', 'wporlogin'); ?></label>
                                        </div>
                                        <div id="wporlogin-container-background-my-image" style="<?php if( (get_option('wporlogin_background_images') == 'wporlogin_free_images' )){ echo 'display: none;'; } ?>">
                                            <?php if(esc_url(get_option('wporlogin_url_img_fondo'))){ ?>
                                                <img id="wporlogin_url_img_fondo_img" src="<?php echo esc_url(get_option('wporlogin_url_img_fondo')); ?>" style="width: 220px; padding: 10px; background-color: #ffffff; border: 2px dashed rgba(0,0,0,.1);"><br>
                                            <?php } ?>
                                            <input id="wporlogin_url_img_fondo_text" type="text" name="wporlogin_url_img_fondo" class="regular-text" value="<?php echo esc_html(get_option('wporlogin_url_img_fondo')); ?>"/><br>
                                            <input id="wporlogin_url_img_fondo_button" type="button" class="button" value="<?php _e('Upload Background', 'wporlogin'); ?>" />
                                        </div>
                                        <div id="wporlogin-container-background-free-image" style="<?php if( get_option('wporlogin_background_images') == 'wporlogin_my_images' ){ echo 'display: none;'; } ?>">
                                            <div style="overflow: hidden;">
                                                <?php 
                                                if ( ! empty( $default_backgrounds ) && is_array( $default_backgrounds ) ) {
                                                    $count_images = count($default_backgrounds);
                                                    $saved_free_image = get_option('wporlogin-background-free-image');
                                                    $saved_filename = basename($saved_free_image);
                                                    for($i=0; $i<$count_images; $i++){ 
                                                        $current_url = $default_backgrounds[$i];
                                                        $current_filename = basename($current_url);
                                                        $is_checked = ($saved_filename === $current_filename) || ($i == 0 && empty($saved_free_image));
                                                    ?>
                                                    <div style="float: left;">
                                                        <input type="radio" id="wporlogin-background-free-image-<?php echo $i; ?>" name="wporlogin-background-free-image" value="<?php echo esc_url($current_url); ?>" <?php checked( $is_checked, true ); ?>>
                                                        <label for="wporlogin-background-free-image-<?php echo $i; ?>"><?php _e('Image ', 'wporlogin'); ?><?php echo $i+1; ?></label>
                                                        <div style="padding-top: 10px; margin-right: 15px;">
                                                            <img onclick="document.getElementById('wporlogin-background-free-image-<?php echo $i; ?>').checked=true;" src="<?php echo esc_url($current_url); ?>" style="width: 220px; padding: 10px; background-color: #ffffff; border: 2px dashed rgba(0,0,0,.1);">
                                                        </div>
                                                    </div>
                                                    <?php } 
                                                } 
                                                ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>                              
                            </tbody>
                        </table>  
                    </div>
                </div>

                <?php
                if ( defined( 'WPORLOGIN_PATH' ) && file_exists( WPORLOGIN_PATH . 'includes/templates/wporlogin-paypal-done.php' ) ) {
                    include( WPORLOGIN_PATH . 'includes/templates/wporlogin-paypal-done.php' ); 
                }
                ?>
            </div>
            
            <?php submit_button(); ?>
            
        </form>
    </div>    
</div>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    
    // --- VARIABLES DE ESTADO ---
    const savedDesign = "<?php echo esc_js($active_design); ?>";
    const customizeUrl = "<?php echo esc_url_raw($customize_url); ?>";
    
    // Elementos
    const heroBtn = document.getElementById('wporlogin-hero-btn');
    const heroIcon = document.getElementById('wporlogin-hero-icon');
    const heroText = document.getElementById('wporlogin-hero-text');
    const heroHint = document.getElementById('wporlogin-hero-hint');

    function wporlogin_update_visibility() {
        var selected = document.querySelector('input[name="wporlogin_active_design"]:checked');
        
        var tableBrand = document.getElementById('wporlogin-table-brand-color');
        var tableSettings = document.getElementById('wporlogin-container-settings-table');
        
        if (selected) {
            var val = selected.value;

            // 1. Manejo de Tablas (Visibilidad)
            if (val === 'wporlogin_design_img_premium_zero') {
                if (tableBrand) tableBrand.style.display = 'none';
                if (tableSettings) tableSettings.style.display = 'none';
            } else if (val === 'wporlogin_design_basic') {
                if (tableBrand) tableBrand.style.display = 'table';
                if (tableSettings) tableSettings.style.display = 'none';
            } else {
                if (tableBrand) tableBrand.style.display = 'table';
                if (tableSettings) tableSettings.style.display = 'block';
            }
            
            // 2. Manejo Visual de Tarjetas
            document.querySelectorAll('.wporlogin-card-hero, .wporlogin-card-preset').forEach(el => {
                el.classList.remove('selected-design');
            });
            var parentCard = selected.closest('.wporlogin-card-hero, .wporlogin-card-preset');
            if (parentCard) parentCard.classList.add('selected-design');

            // 3. LÓGICA INTELIGENTE DEL BOTÓN HERO (CORREGIDA) 🧠✅
            if (val === 'wporlogin_design_img_premium_zero') {
                
                if (savedDesign === 'wporlogin_design_img_premium_zero') {
                    // CASO A: Ya estaba guardado como Custom -> ¡LISTO!
                    heroBtn.className = "wporlogin-btn-hero state-ready wporlogin-prevent-select";
                    heroBtn.href = customizeUrl;
                    heroBtn.target = "_self";
                    heroBtn.onclick = null; // <--- ¡AQUÍ ESTÁ EL TRUCO! Borramos comportamientos viejos
                    
                    heroIcon.style.display = "inline-block";
                    heroText.innerText = "<?php _e('Open Live Builder', 'wporlogin'); ?>";
                    heroHint.classList.remove('visible');
                    
                } else {
                    // CASO B: Seleccionado pero NO guardado -> ADVERTENCIA
                    heroBtn.className = "wporlogin-btn-hero state-warning wporlogin-prevent-select";
                    heroBtn.removeAttribute('href');
                    heroIcon.style.display = "none";
                    heroText.innerText = "<?php _e('Save Changes to Activate', 'wporlogin'); ?>";
                    heroHint.classList.add('visible');
                    
                    // Asignamos comportamiento de scroll
                    heroBtn.onclick = function(e) {
                        e.preventDefault();
                        var submitBtn = document.querySelector('input[type="submit"]');
                        if(submitBtn) {
                            submitBtn.scrollIntoView({behavior: "smooth"});
                            submitBtn.style.animation = "pulse 0.5s 2";
                        }
                    };
                }

            } else {
                // CASO C: Inactivo (Diseño predefinido seleccionado)
                heroBtn.className = "wporlogin-btn-hero state-inactive wporlogin-prevent-select";
                heroBtn.href = "#";
                
                // Asignamos comportamiento de selección
                heroBtn.onclick = function(e) {
                    e.preventDefault();
                    var customRadio = document.getElementById('wporlogin_design_img_premium_zero');
                    if(customRadio) {
                        customRadio.checked = true;
                        customRadio.dispatchEvent(new Event('change'));
                    }
                };
                
                heroIcon.style.display = "none";
                heroText.innerText = "<?php _e('Select Custom Design', 'wporlogin'); ?>";
                heroHint.classList.remove('visible');
            }
        }
    }

    // Inicializar
    wporlogin_update_visibility();

    // Listeners
    document.querySelectorAll('input[name="wporlogin_active_design"]').forEach(radio => {
        radio.addEventListener('change', wporlogin_update_visibility);
    });

    document.querySelectorAll('.wporlogin-card-trigger').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('.wporlogin-prevent-select')) return;
            var radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        });
    });

    window.wporloginSelectRadio = function(id) {
        var radio = document.getElementById(id);
        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        }
    };
});
</script>

<style>
@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); box-shadow: 0 0 10px #0071e3; }
  100% { transform: scale(1); }
}
</style>