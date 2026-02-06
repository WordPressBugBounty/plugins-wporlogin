<?php
/**
 * Class responsible for registering background and general design options
 * in the WordPress Customizer for WPORLogin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ================================================================
// 1. CONTROL MULTI-IMAGEN (DISEÑO V2 - IMPLEMENTACIÓN CON HOOK)
// ================================================================
if ( ! class_exists( 'WPORLogin_Multi_Image_Control' ) ) {
    class WPORLogin_Multi_Image_Control extends WP_Customize_Control {
        public $type = 'wporlogin-multi-image';

        /**
         * Encolar scripts y estilos.
         * Usamos el hook 'customize_controls_print_styles' como solicitaste
         * para inyectar el CSS en el head del personalizador.
         */
        public function enqueue() {
            // 1. Inyectar CSS
            add_action( 'customize_controls_print_styles', array( $this, 'print_custom_styles' ) );

            // 2. Cargar Media Uploader y JS
            wp_enqueue_media();
            wp_enqueue_script( 
                'wporlogin-customizer-background-control-js', 
                WPORLOGIN_URL . 'assets/js/customizer/wporlogin-customizer-background-control.js', 
                array( 'jquery' ), 
                WPORLOGIN_VERSION, 
                true 
            );
        }

        /**
         * Función callback que imprime los estilos CSS.
         */
        public function print_custom_styles() {
            ?>
            <style>
                /* --- WPORLOGIN MULTI IMAGE CONTROL CSS --- */
                
                /* Contenedor Principal */
                .wporlogin-multi-box { 
                    border: 1px solid #e2e4e7 !important;
                    background: #fcfcfc !important;
                    padding: 12px !important; 
                    border-radius: 6px !important;
                    box-sizing: border-box !important;
                }

                /* Grid de 3 columnas (Estilo Instagram) */
                .wporlogin-multi-images { 
                    display: grid !important; 
                    grid-template-columns: repeat(3, 1fr) !important; 
                    gap: 10px !important; 
                    margin-bottom: 15px !important; 
                    width: 100% !important;
                }

                /* Tarjeta de imagen INDIVIDUAL */
                .wporlogin-thumb { 
                    position: relative !important; 
                    border-radius: 6px !important; 
                    overflow: hidden !important; 
                    /*cursor: move !important; */
                    /* Cuadrado perfecto */
                    aspect-ratio: 1 / 1 !important; 
                    height: auto !important; 
                    width: 100% !important;
                    background: #fff;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
                    border: 2px solid transparent !important;
                    transition: all 0.2s ease !important;
                }

                /* Efecto Hover */
                .wporlogin-thumb:hover {
                    transform: translateY(-2px) !important;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
                    border-color: #2271b1 !important; /* Azul WordPress */
                    z-index: 5 !important;
                }

                /* Imagen */
                .wporlogin-thumb img { 
                    width: 100% !important; 
                    height: 100% !important; 
                    object-fit: cover !important; 
                    display: block !important; 
                    margin: 0 !important;
                }

                /* Botón Eliminar (Círculo Flotante) */
                .wporlogin-remove { 
                    position: absolute !important; 
                    top: 4px !important; 
                    right: 4px !important; 
                    width: 20px !important; 
                    height: 20px !important; 
                    background: #fff !important; 
                    color: #d63638 !important; 
                    border-radius: 50% !important; 
                    display: flex !important; 
                    align-items: center !important; 
                    justify-content: center !important; 
                    cursor: pointer !important; 
                    font-size: 11px !important; 
                    font-weight: 900 !important;
                    opacity: 0; /* Oculto */
                    transform: scale(0.8);
                    transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
                    z-index: 10 !important;
                    line-height: 1 !important;
                }

                /* Mostrar botón al pasar el mouse */
                .wporlogin-thumb:hover .wporlogin-remove { 
                    opacity: 1 !important; 
                    transform: scale(1) !important;
                }

                .wporlogin-remove:hover { 
                    background: #d63638 !important; 
                    color: #fff !important; 
                }

                /* Botón Agregar */
                .wporlogin-add-btn { 
                    width: 100% !important; 
                    text-align: center !important; 
                    margin-top: 5px !important;
                    display: inline-block !important;
                }
            </style>
            <?php
        }

        public function render_content() {
            $image_ids = $this->value();
            $ids_array = !empty($image_ids) ? explode(',', $image_ids) : array();
            ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php if ( ! empty( $this->description ) ) : ?>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php endif; ?>

            <div class="wporlogin-multi-box">
                <div class="wporlogin-multi-images" id="container-<?php echo esc_attr($this->id); ?>">
                    <?php 
                    if(!empty($ids_array)) {
                        foreach($ids_array as $id) {
                            if(!is_numeric($id)) continue;
                            $url = wp_get_attachment_image_url($id, 'thumbnail'); // Usamos thumbnail para velocidad
                            if($url) {
                                echo '<div class="wporlogin-thumb" data-id="'.esc_attr($id).'">
                                        <img src="'.esc_url($url).'">
                                        <span class="wporlogin-remove" title="'.__('Remove', 'wporlogin').'">✕</span>
                                      </div>';
                            }
                        }
                    }
                    ?>
                </div>
                
                <button type="button" class="button button-secondary wporlogin-add-btn wporlogin-add-images" data-target="<?php echo esc_attr($this->id); ?>">
                    <?php _e('Add/Edit Images', 'wporlogin'); ?>
                </button>
                <input type="hidden" id="<?php echo esc_attr($this->id); ?>" name="<?php echo esc_attr($this->id); ?>" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?>>
            </div>
            <?php
        }
    }
}

// ================================================================
// 2. CONTROL GALERÍA
// ================================================================
if ( ! class_exists( 'WPORLogin_Image_Gallery_Control' ) ) {
    class WPORLogin_Image_Gallery_Control extends WP_Customize_Control {
        public $type = 'wporlogin_gallery';
        public function enqueue() { add_action('customize_controls_print_styles', array($this, 'print_styles')); }
        public function print_styles() {
            ?>
            <style>
                .wporlogin-gallery-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; background: #fff; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 5px; }
                .wporlogin-gallery-item { cursor: pointer; position: relative; display: block; overflow: hidden; border-radius: 4px; border: 1px solid #eee; }
                .wporlogin-gallery-item img { width: 100%; height: 60px; object-fit: cover; display: block; transition: transform 0.3s ease; }
                .wporlogin-overlay-check { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s ease; }
                .wporlogin-gallery-item input:checked ~ .wporlogin-overlay-check { opacity: 1; }
                .wporlogin-gallery-item input:checked ~ img { transform: scale(1.05); }
                .wporlogin-check-icon { width: 24px; height: 24px; background: #2271b1; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
                .wporlogin-check-icon::after { content: ''; width: 5px; height: 10px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); margin-bottom: 2px; }
                .wporlogin-gallery-item:hover { border-color: #2271b1; }
            </style>
            <?php
        }
        public function render_content() {
            if ( empty( $this->choices ) ) return;
            ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <div class="wporlogin-gallery-container">
                <?php foreach ( $this->choices as $url ) : ?>
                    <label class="wporlogin-gallery-item">
                        <input type="radio" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_url( $url ); ?>" <?php $this->link(); ?> <?php checked( $this->value(), $url ); ?> style="display:none;">
                        <img src="<?php echo esc_url( $url ); ?>">
                        <div class="wporlogin-overlay-check"><span class="wporlogin-check-icon"></span></div>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php
        }
    }
}

// ================================================================
// 3. CLASE PRINCIPAL
// ================================================================
if ( ! class_exists( 'WPORLogin_Customizer_Background' ) ) {
    class WPORLogin_Customizer_Background {
        
        protected $panel_id;

        public function __construct( $panel_id ) {
            $this->panel_id = $panel_id;
            add_action( 'customize_register', array( $this, 'register' ) );
            // Este script gestiona mostrar/ocultar controles en el panel izquierdo (UX)
            add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_control_logic_scripts' ) );
        }

        // 1. Añadimos helpers para los callbacks (Active Callbacks)
        public function is_video_mode( $control ) {
            return $control->manager->get_setting('wporlogin_background_mode')->value() === 'video';
        }

        // Callbacks para alternar visibilidad según la fuente
        public function is_video_external( $control ) {
            return $this->is_video_mode($control) && $control->manager->get_setting('wporlogin_video_source')->value() === 'external';
        }
        public function is_video_local( $control ) {
            return $this->is_video_mode($control) && $control->manager->get_setting('wporlogin_video_source')->value() === 'local';
        }

        private function get_default_mode_from_legacy() {
            $legacy_enabled = get_option( 'wporlogin_enable_bg_image', false );
            return ( $legacy_enabled ) ? 'static' : 'disabled';
        }

        // --- ACTIVE CALLBACKS (Para PHP en server-side initial load) ---
        public function is_static_mode( $control ) {
            return $control->manager->get_setting('wporlogin_background_mode')->value() === 'static';
        }
        public function is_random_mode( $control ) {
            return $control->manager->get_setting('wporlogin_background_mode')->value() === 'random';
        }
        public function is_static_gallery_visible( $control ) {
            if ( ! $this->is_static_mode( $control ) ) return false;
            $custom_img = $control->manager->get_setting( 'wporlogin_login_background_image' );
            return ( ! $custom_img || ! $custom_img->value() );
        }

        public function register( $wp_customize ) {
            $section = 'wporlogin_login_background_section';
            $wp_customize->add_section( $section, array( 'title' => __( 'Background & Design', 'wporlogin' ), 'priority' => 10, 'panel' => $this->panel_id ) );

            // --- GLOBALES (Transport: postMessage = Instantáneo) ---
            
            // 1. Color de Fondo
            $wp_customize->add_setting( 'wporlogin_login_background_color', array( 'default' => '#f0f0f1', 'type' => 'option', 'transport' => 'postMessage', 'sanitize_callback' => 'sanitize_hex_color' ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_login_background_color', array( 'label' => __( 'Base Background Color', 'wporlogin' ), 'section' => $section, 'priority' => 10 ) ) );

            // 2. Overlay
            $wp_customize->add_setting( 'wporlogin_bg_overlay_color', array( 'default' => '#000000', 'type' => 'option', 'transport' => 'postMessage', 'sanitize_callback' => 'sanitize_hex_color' ) );
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wporlogin_bg_overlay_color', array( 'label' => __( 'Overlay Color', 'wporlogin' ), 'section' => $section, 'priority' => 20 ) ) );

            $wp_customize->add_setting( 'wporlogin_bg_overlay_opacity', array( 'default' => '0', 'type' => 'option', 'transport' => 'postMessage', 'sanitize_callback' => 'absint' ) );
            $wp_customize->add_control( 'wporlogin_bg_overlay_opacity', array( 'label' => __( 'Overlay Opacity (%)', 'wporlogin' ), 'section' => $section, 'type' => 'range', 'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ), 'priority' => 30 ) );

            // 3. SELECTOR DE MODO
            // NOTA: Usamos 'refresh' para garantizar que el DOM se limpie correctamente entre modos.
            /*$wp_customize->add_setting( 'wporlogin_background_mode', array(
                'default'   => $this->get_default_mode_from_legacy(),
                'type'      => 'option',
                'transport' => 'refresh', // Importante para cargar/descargar scripts JS
                'sanitize_callback' => 'sanitize_key',
            ) );
            $wp_customize->add_control( 'wporlogin_background_mode', array(
                'label'       => __( 'Background Mode', 'wporlogin' ),
                'section'     => $section,
                'type'        => 'radio',
                'choices'     => array(
                    'disabled' => __( 'Disabled (Color Only)', 'wporlogin' ),
                    'static'   => __( 'Static Image', 'wporlogin' ),
                    'random'   => __( 'Random Slideshow', 'wporlogin' ),
                    'video'    => __( 'Video Background', 'wporlogin' ),
                ),
                'priority'    => 40,
            ) );*/
             // 3. SELECTOR DE MODO (VISUAL STYLE)
            $wp_customize->add_setting( 'wporlogin_background_mode', array(
                'default'   => $this->get_default_mode_from_legacy(),
                'type'      => 'option',
                'transport' => 'refresh', 
                'sanitize_callback' => 'sanitize_key',
            ) );

            // Usamos la nueva clase WPORLogin_Visual_Mode_Control
            $wp_customize->add_control( new WPORLogin_Visual_Mode_Control( $wp_customize, 'wporlogin_background_mode', array(
                'label'       => __( 'Background Mode', 'wporlogin' ),
                'section'     => $section,
                'choices'     => array(
                    'disabled' => array(
                        'label' => __( 'Color Only', 'wporlogin' ),
                        'icon'  => 'dashicons-art', // Icono de paleta
                    ),
                    'static'   => array(
                        'label' => __( 'Static Image', 'wporlogin' ), // "Imagen Estática" es muy largo, mejor "Image"
                        'icon'  => 'dashicons-format-image',
                    ),
                    'random'   => array(
                        'label' => __( 'Random Images', 'wporlogin' ),
                        'icon'  => 'dashicons-images-alt2',
                    ),
                    'video'    => array(
                        'label' => __( 'Video Background', 'wporlogin' ),
                        'icon'  => 'dashicons-video-alt3',
                    ),
                ),
                'priority'    => 40,
            ) ) );

            // --- GRUPO A: STATIC IMAGE (Transport: postMessage) ---
            
            $wp_customize->add_setting( 'wporlogin_login_background_image', array( 'default' => '', 'type' => 'option', 'transport' => 'postMessage', 'sanitize_callback' => 'esc_url_raw' ) );
            $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'wporlogin_login_background_image', array(
                'label' => __( 'Upload Static Image', 'wporlogin' ), 'section' => $section, 'active_callback' => array( $this, 'is_static_mode' ), 'priority' => 50,
            ) ) );

            $wp_customize->add_setting( 'wporlogin_bg_gallery', array( 'default' => '', 'type' => 'option', 'transport' => 'postMessage', 'sanitize_callback' => 'esc_url_raw' ) );
            $wp_customize->add_control( new WPORLogin_Image_Gallery_Control( $wp_customize, 'wporlogin_bg_gallery', array(
                'label' => __( 'Or Select from Gallery', 'wporlogin' ), 'section' => $section, 'choices' => Wporlogin::get_default_backgrounds(), 'active_callback' => array( $this, 'is_static_gallery_visible' ), 'priority' => 60,
            ) ) );

            $this->add_legacy_select($wp_customize, 'repeat', 'Background Repeat', array('no-repeat'=>'No Repeat','repeat'=>'Repeat','repeat-x'=>'Repeat X','repeat-y'=>'Repeat Y'), 70);
            $this->add_legacy_select($wp_customize, 'size', 'Background Size', array('cover'=>'Cover','contain'=>'Contain','auto'=>'Auto'), 80);
            $this->add_legacy_select($wp_customize, 'position', 'Background Position', array('center center'=>'Center Center','left top'=>'Left Top','right bottom'=>'Right Bottom'), 90);


            // --- GRUPO B: RANDOM IMAGE (Transport: refresh) ---
            // El cambio de imágenes o duración requiere regenerar las variables del script JS, por eso refresh.
            
            $wp_customize->add_setting( 'wporlogin_random_images', array(
                'default' => '', 
                'type' => 'option', 
                'transport' => 'refresh', 
                'sanitize_callback' => 'sanitize_text_field',
            ) );
            $wp_customize->add_control( new WPORLogin_Multi_Image_Control( $wp_customize, 'wporlogin_random_images', array(
                'label' => __( 'Select Random Images', 'wporlogin' ), 'description' => __( 'Select multiple images.', 'wporlogin' ), 'section' => $section, 'active_callback' => array( $this, 'is_random_mode' ), 'priority' => 100,
            ) ) );

            $wp_customize->add_setting( 'wporlogin_random_duration', array(
                'default' => 5, 
                'type' => 'option', 
                'transport' => 'postMessage', 
                'sanitize_callback' => 'absint',
            ) );
            $wp_customize->add_control( 'wporlogin_random_duration', array(
                'label' => __( 'Slide Duration (Seconds)', 'wporlogin' ), 'section' => $section, 'type' => 'number', 'input_attrs' => array('min'=>2, 'max'=>60), 'active_callback' => array( $this, 'is_random_mode' ), 'priority' => 110,
            ) );

            // --- GRUPO VIDEO (HIBRIDO: EXTERNAL + LOCAL) ---
            
            // 1. Selector de Fuente
            $wp_customize->add_setting( 'wporlogin_video_source', array(
                'default' => 'external', 'type' => 'option', 'transport' => 'refresh', 'sanitize_callback' => 'sanitize_key'
            ));
            $wp_customize->add_control( 'wporlogin_video_source', array(
                'label' => __( 'Video Source', 'wporlogin' ),
                'section' => $section,
                'type' => 'select',
                'choices' => array(
                    'external' => __( 'YouTube / Vimeo', 'wporlogin' ),
                    'local'    => __( 'Local MP4 (Media Library)', 'wporlogin' ),
                ),
                'active_callback' => array( $this, 'is_video_mode' ),
                'priority' => 115,
            ));
            
            // 2. URL Externa (YouTube / Vimeo)
            $wp_customize->add_setting( 'wporlogin_video_external_url', array(
                'default' => '', 'type' => 'option', 'transport' => 'postMessage', 'sanitize_callback' => 'esc_url_raw'
            ));
            $wp_customize->add_control( 'wporlogin_video_external_url', array(
                'label' => __( 'Video URL', 'wporlogin' ),
                'description' => __( 'YouTube or Vimeo link.', 'wporlogin' ),
                'section' => $section,
                'type' => 'url',
                //'active_callback' => array( $this, 'is_video_mode' ),
                'active_callback' => array( $this, 'is_video_external' ), // CAMBIADO: Usar callback específico
                'priority' => 120,
            ));

            // 3. Video Local (Media Uploader) [NUEVO]
            $wp_customize->add_setting( 'wporlogin_video_local', array(
                'default' => '', 'type' => 'option', 'transport' => 'refresh', 'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'wporlogin_video_local', array(
                'label' => __( 'Upload Video (MP4)', 'wporlogin' ),
                'description' => __( 'Upload a lightweight MP4 file.', 'wporlogin' ),
                'section' => $section,
                'mime_type' => 'video',
                'active_callback' => array( $this, 'is_video_local' ),
                'priority' => 125,
            )));

            // Fallback Image
            $wp_customize->add_setting( 'wporlogin_video_fallback', array(
                'default' => '', 'type' => 'option', 'transport' => 'postMessage', 'sanitize_callback' => 'esc_url_raw'
            ));
            $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'wporlogin_video_fallback', array(
                'label' => __( 'Mobile Fallback Image', 'wporlogin' ),
                'description' => __( 'Video backgrounds do not autoplay on mobile. This image will be shown instead.', 'wporlogin' ),
                'section' => $section,
                'active_callback' => array( $this, 'is_video_mode' ),
                'priority' => 130,
            )));
        }

        private function add_legacy_select($wp_customize, $suffix, $label, $choices, $priority) {
            $id = 'wporlogin_login_background_' . $suffix;
            $wp_customize->add_setting( $id, array(
                'default' => array_key_first($choices), 'type' => 'option', 'transport' => 'postMessage', 'sanitize_callback' => array( $this, 'sanitize_choices' ),
            ));
            $wp_customize->add_control( $id, array(
                'label' => __( $label, 'wporlogin' ), 'section' => 'wporlogin_login_background_section', 'type' => 'select', 'choices' => $choices, 
                'active_callback' => array( $this, 'is_static_mode' ), 'priority' => $priority,
            ));
        }

        public function sanitize_choices( $input, $setting ) {
            $control = $setting->manager->get_control( $setting->id );
            return ( array_key_exists( $input, $control->choices ) ) ? $input : $setting->default;
        }

        // --- JS PARA EL PANEL IZQUIERDO (UX) ---
        public function print_control_logic_scripts() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    
                    // ==========================================
                    // 1. DEFINICIÓN DE VARIABLES (IDs)
                    // ==========================================
                    
                    // Controles para el modo "Imagen Estática"
                    var staticCtrls = [
                        'wporlogin_login_background_image', 
                        'wporlogin_bg_gallery', 
                        'wporlogin_login_background_repeat', 
                        'wporlogin_login_background_size', 
                        'wporlogin_login_background_position'
                    ];

                    // Controles para el modo "Aleatorio"
                    var randomCtrls = [
                        'wporlogin_random_images', 
                        'wporlogin_random_duration'
                    ];
                    
                    // Controles de Video
                    var videoSourceCtrlId = 'wporlogin_video_source';   // El selector (YouTube vs Local)
                    var videoCommonId     = 'wporlogin_video_fallback'; // Imagen de respaldo
                    var videoExtId        = 'wporlogin_video_external_url'; // Input URL
                    var videoLocalId      = 'wporlogin_video_local';    // Input Upload

                    // ==========================================
                    // 2. FUNCIÓN PRINCIPAL (LA LÓGICA)
                    // ==========================================
                    function toggleLogic() {
                        // A. Obtenemos el MODO actual (Static, Random, Video o Disabled)
                        var modeSetting = wp.customize('wporlogin_background_mode');
                        var mode = modeSetting ? modeSetting.get() : 'disabled';

                        // B. Obtenemos si hay una IMAGEN SUBIDA manualmente (para ocultar la galería)
                        var imgSetting = wp.customize('wporlogin_login_background_image');
                        var customImg = imgSetting ? imgSetting.get() : '';

                        // --- CASO 1: MODO IMAGEN ESTÁTICA ---
                        $.each(staticCtrls, function(i, id) {
                            var ctrl = wp.customize.control(id);
                            if (ctrl) {
                                if (mode === 'static') {
                                    // Lógica especial para la Galería:
                                    // Si el usuario subió su propia foto, ocultamos la galería predefinida para no confundir.
                                    if (id === 'wporlogin_bg_gallery') {
                                        if ( customImg && customImg !== '' ) {
                                            $(ctrl.container).slideUp(100); // Ocultar galería
                                        } else {
                                            $(ctrl.container).slideDown(100); // Mostrar galería
                                        }
                                    } else {
                                        // El resto de controles estáticos se muestran siempre
                                        $(ctrl.container).slideDown(100);
                                    }
                                } else {
                                    // Si no es modo estático, ocultar todo
                                    $(ctrl.container).hide(); 
                                }
                            }
                        });

                        // --- CASO 2: MODO ALEATORIO ---
                        $.each(randomCtrls, function(i, id) {
                            var ctrl = wp.customize.control(id);
                            if (ctrl) {
                                if (mode === 'random') {
                                    $(ctrl.container).slideDown(100);
                                } else {
                                    $(ctrl.container).hide();
                                }
                            }
                        });

                        // --- CASO 3: MODO VIDEO (HÍBRIDO) ---
                        var vSourceCtrl = wp.customize.control(videoSourceCtrlId);
                        var vCommonCtrl = wp.customize.control(videoCommonId);
                        var vExtCtrl    = wp.customize.control(videoExtId);
                        var vLocCtrl    = wp.customize.control(videoLocalId);

                        if (mode === 'video') {
                            // 1. Mostrar el selector maestro y la imagen de respaldo
                            if(vSourceCtrl) $(vSourceCtrl.container).slideDown(100);
                            if(vCommonCtrl) $(vCommonCtrl.container).slideDown(100);

                            // 2. Decidir qué input mostrar (Local vs Externo)
                            var sourceSetting = wp.customize('wporlogin_video_source');
                            var currentSource = sourceSetting ? sourceSetting.get() : 'external';

                            if (currentSource === 'local') {
                                // Usuario eligió Local -> Mostrar subida, ocultar URL
                                if(vLocCtrl) $(vLocCtrl.container).slideDown(100);
                                if(vExtCtrl) $(vExtCtrl.container).hide();
                            } else {
                                // Usuario eligió Externo -> Mostrar URL, ocultar subida
                                if(vLocCtrl) $(vLocCtrl.container).hide();
                                if(vExtCtrl) $(vExtCtrl.container).slideDown(100);
                            }

                        } else {
                            // Si NO es modo video, ocultar absolutamente todo lo de video
                            if(vSourceCtrl) $(vSourceCtrl.container).hide();
                            if(vCommonCtrl) $(vCommonCtrl.container).hide();
                            if(vExtCtrl)    $(vExtCtrl.container).hide();
                            if(vLocCtrl)    $(vLocCtrl.container).hide();
                        }
                    }

                    // ==========================================
                    // 3. EVENTOS (DETECTAR CAMBIOS)
                    // ==========================================
                    
                    // Cuando cambie el Modo (Static/Video/Random/etc)
                    wp.customize('wporlogin_background_mode', function(val) { 
                        val.bind(toggleLogic); 
                    });

                    // Cuando suban o borren la imagen principal (para mostrar/ocultar galería)
                    wp.customize('wporlogin_login_background_image', function(val) { 
                        val.bind(toggleLogic); 
                    });

                    // Cuando cambie la fuente de video (YouTube <-> Local)
                    wp.customize('wporlogin_video_source', function(val) { 
                        val.bind(function(newVal) {
                            toggleLogic(); 
                        }); 
                    });

                    // Ejecutar una vez al inicio (con un pequeño retraso para asegurar que WordPress cargó)
                    setTimeout(toggleLogic, 500);
                });
            </script>
            <?php
        }
    }
}

// ================================================================
// 4. CONTROL VISUAL (APPLE STYLE CARDS)
// ================================================================
if ( ! class_exists( 'WPORLogin_Visual_Mode_Control' ) ) {
    class WPORLogin_Visual_Mode_Control extends WP_Customize_Control {
        public $type = 'wporlogin-visual-mode';

        public function enqueue() {
            // Inyectamos el CSS directamente aquí para que cargue con el control
            add_action( 'customize_controls_print_styles', array( $this, 'print_styles' ) );
        }

        public function print_styles() {
            ?>
            <style>
                /* Contenedor Grid */
                .wporlogin-mode-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr; /* 2 columnas */
                    gap: 12px;
                    margin-top: 5px;
                }

                /* La Tarjeta (Label) */
                .wporlogin-mode-card {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    background: #fff;
                    border: 1px solid #dcdcde;
                    border-radius: 8px; /* Radio estilo Apple */
                    padding: 15px 10px;
                    cursor: pointer;
                    transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
                    text-align: center;
                    position: relative;
                }

                /* Icono */
                .wporlogin-mode-card .dashicons {
                    font-size: 24px;
                    width: 24px;
                    height: 24px;
                    margin-bottom: 8px;
                    color: #50575e;
                    transition: color 0.2s ease;
                }

                /* Texto */
                .wporlogin-mode-card span {
                    font-size: 12px;
                    font-weight: 500;
                    color: #646970;
                    line-height: 1.2;
                }

                /* Hover */
                .wporlogin-mode-card:hover {
                    border-color: #2271b1;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
                }
                .wporlogin-mode-card:hover .dashicons {
                    color: #2271b1;
                }

                /* Estado Activo (Checked) */
                input:checked + .wporlogin-mode-card {
                    border-color: #2271b1;
                    background-color: #f0f6fc; /* Azul muy suave */
                    box-shadow: 0 0 0 1px #2271b1; /* Doble borde sutil */
                    z-index: 1;
                }
                input:checked + .wporlogin-mode-card .dashicons {
                    color: #2271b1;
                }
                input:checked + .wporlogin-mode-card span {
                    color: #1d2327;
                    font-weight: 600;
                }

                /* Ocultar el input nativo pero mantenerlo accesible */
                .wporlogin-hidden-input {
                    opacity: 0;
                    position: absolute;
                    z-index: -1;
                }
            </style>
            <?php
        }

        public function render_content() {
            if ( empty( $this->choices ) ) return;
            ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php if ( ! empty( $this->description ) ) : ?>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php endif; ?>

            <div class="wporlogin-mode-grid">
                <?php foreach ( $this->choices as $value => $args ) : ?>
                    <label>
                        <input type="radio" class="wporlogin-hidden-input" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); ?> <?php checked( $this->value(), $value ); ?>>
                        <div class="wporlogin-mode-card">
                            <span class="dashicons <?php echo esc_attr( $args['icon'] ); ?>"></span>
                            <span><?php echo esc_html( $args['label'] ); ?></span>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php
        }
    }
}