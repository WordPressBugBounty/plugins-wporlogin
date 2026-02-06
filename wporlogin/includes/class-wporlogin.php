<?php

/**
 * La Clase Central del Plugin (The Core Class).
 *
 * Se encarga de cargar todas las dependencias, inicializar el orquestador de hooks 
 * y registrar los hooks (acciones y filtros) del plugin en WordPress.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/includes
 * @author     Oregoom <tu-email@oregoom.com>
 */
class Wporlogin {

    /**
     * Nombre del archivo CSS generado dinámicamente.
     * Fuente única de verdad para todo el plugin.
     */
    const CUSTOM_CSS_FILENAME = 'wporlogin-style-design-premium-zero.css';

    /**
     * El loader se encarga de coordinar todos los hooks del plugin.
     *
     * @access protected
     * @var    Wporlogin_Loader    $loader    Mantiene y registra los hooks.
     */
    protected $loader;

    /**
     * El identificador único del plugin.
     *
     * @access protected
     * @var    string    $plugin_name    El string usado para identificar este plugin.
     */
    protected $plugin_name;

    /**
     * La versión actual del plugin.
     *
     * @access protected
     * @var    string    $version    La versión actual del plugin.
     */
    protected $version;

    /**
     * Inicializa la clase y define las propiedades principales del plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'WPORLOGIN_VERSION' ) ) {
            $this->version = WPORLOGIN_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'wporlogin';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        // [NUEVO] Comprobador de actualizaciones de Base de Datos
        //$this->check_version();

        // [OPTIMIZACIÓN EXPERTA]
        // Usamos 'plugins_loaded' para el chequeo de versión.
        // Es el primer momento seguro para leer opciones de la BD.
        $this->loader->add_action( 'plugins_loaded', $this, 'check_version' );
    }

    /**
     * Carga todos los archivos dependientes del núcleo del plugin.
     *
     * @access private
     */
    private function load_dependencies() {
/*
        // Clases del núcleo y utilidades
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wporlogin-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wporlogin-i18n.php';

        // --- NUEVO: Cargamos el Protector (Includes) ---
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wporlogin-page-protector.php';

        // --- NUEVO: Cargamos el Template Loader (Public) ---
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-template.php';

        // Cargar la clase de visibilidad
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-visibility.php';

        // Clases del área de administración
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wporlogin-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wporlogin-admin-notices.php'; // [NUEVO]

        // Clases de la parte pública (Frontend/Login)
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-design.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-security.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-redirects.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-limit-login.php'; // [NUEVO]

        // [NUEVO] Módulo Hide Login
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-hide-login.php';

        // ... tus otros requires ...
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-social.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wporlogin-admin-dashboard.php';

        // Nombre más descriptivo: "Puente al Personalizador"
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wporlogin-customizer-bridge.php';

        $this->loader = new Wporlogin_Loader();*/

/**
         * 1. NÚCLEO Y UTILIDADES (Carga Global)
         * Estos archivos son ligeros y necesarios en ambos contextos.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wporlogin-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wporlogin-i18n.php';
        
        // [GLOBAL] Protector: Evita borrado accidental (Admin) y protege lógica interna.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wporlogin-page-protector.php';
        
        // [GLOBAL] Visibilidad: CRÍTICO para tu objetivo.
        // Se necesita en Admin (para ocultar de Menús/Nav-Menus.php)
        // Se necesita en Front (para ocultar de Search/Archives)
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-visibility.php';

        // [GLOBAL] Puente Customizer: Redirige tanto desde el editor (Admin) como desde la vista (Front).
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wporlogin-customizer-bridge.php';

        // Inicializamos el Loader
        $this->loader = new Wporlogin_Loader();

        /**
         * 2. LÓGICA DE ADMINISTRACIÓN (Solo carga en wp-admin)
         * Evitamos cargar esto en el login o frontend.
         */
        if ( is_admin() ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wporlogin-admin.php';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wporlogin-admin-notices.php';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wporlogin-admin-dashboard.php';
        }

        /**
         * 3. LÓGICA PÚBLICA / FRONTEND (No cargar en Admin excepto AJAX)
         * Incluye el diseño del login, seguridad frontend y plantillas.
         * Nota: La página wp-login.php NO es is_admin(), así que esto cargará correctamente allí.
         */
        if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-template.php';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-design.php';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-security.php';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-redirects.php';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-limit-login.php';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-hide-login.php';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-social.php';
        }

        // [NUEVO] Cargador del Personalizador (Customizer)
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/class-wporlogin-customizer.php';
        // CARGAR SIEMPRE (No solo en customizer)
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/class-wporlogin-dynamic-css-generator.php';

        // Cargar Clase Helper [NUEVO]
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wporlogin-helper.php';

        // --- GESTOR UNIFICADO DE ACTIVOS (CSS, JS, Limpieza) ---
        // Reemplaza a WPORLogin_Custom_CSS_Loader y Wporlogin_Public_Background
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wporlogin-public-assets.php';

    }

    /**
     * FASE 1: EL CHEQUEO (Ligero)
     * Comprueba la versión en la base de datos contra la versión del archivo.
     * Se ejecuta en 'plugins_loaded'.
     */
    public function check_version() {
        // Obtenemos la versión guardada (Es rápido porque WP usa caché interna)
        $installed_version = get_option( 'wporlogin_version' );

        // Si es una instalación nueva O la versión es antigua...
        if ( ! $installed_version || version_compare( $installed_version, $this->version, '<' ) ) {
            
            // ¡AJÁ! Detectamos que necesitamos actualizar.
            // Pero NO ejecutamos el código pesado aquí porque es muy temprano (causaría el error de permalinks).
            // En su lugar, "agendamos" la actualización para el momento 'init'.
            
            add_action( 'init', array( $this, 'run_updater_logic' ) );
        }
    }

    /**
     * FASE 2: LA EJECUCIÓN (Pesada)
     * Realiza la migración, creación de tablas y páginas.
     * Se ejecuta en 'init', cuando WP ya cargó sus funciones de Post y Rewrite.
     */
    public function run_updater_logic() {
        
        // 1. Cargar el activador solo cuando es necesario (Ahorro de memoria)
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wporlogin-activator.php';
        
        // 2. Ejecutar la lógica de activación (Crear tablas, páginas, etc.)
        Wporlogin_Activator::activate();

        // 3. Actualizar la versión en la BD para que esto no se ejecute en la próxima carga
        update_option( 'wporlogin_version', $this->version );
    }

    /**
     * Define la configuración regional para la internacionalización (i18n).
     *
     * @access private
     */
    private function set_locale() {
        $plugin_i18n = new Wporlogin_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Registra todos los hooks (acciones y filtros) del área de administración.
     *
     * @access private
     */
    private function define_admin_hooks() {
        /*

        $plugin_admin   = new Wporlogin_Admin( $this->get_plugin_name(), $this->get_version() );
        $plugin_notices = new Wporlogin_Admin_Notices( $this->get_plugin_name(), $this->get_version() ); // [NUEVO]

        // --- MÓDULO DE PROTECCIÓN DE PÁGINA ---
        // Instanciamos la clase
        $protector = new Wporlogin_Page_Protector();

        // 1. Evitar borrado (Papelera y Borrado definitivo)
        $this->loader->add_action( 'wp_trash_post', $protector, 'prevent_deletion' );
        $this->loader->add_action( 'before_delete_post', $protector, 'prevent_deletion' );
        
        // 2. Mostrar aviso si lo intentaron
        $this->loader->add_action( 'admin_notices', $protector, 'show_warning_notice' );

        // 3. [NUEVO] Quitar Edición Rápida del listado de páginas
        // El hook 'page_row_actions' maneja los enlaces debajo del título de la página
        $this->loader->add_filter( 'page_row_actions', $protector, 'remove_quick_edit_link', 10, 2 );


        // --- GESTIÓN DE MENÚS Y AJUSTES (Admin Class) ---
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );

        // Hook para procesar el desbloqueo de IP antes de que se envíe HTML
        $this->loader->add_action( 'admin_init', $plugin_admin, 'process_unlock_ip_action' );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_assets' );

        // --- GESTIÓN DE AVISOS Y NOTIFICACIONES (Notices Class) ---
        
        // Alerta de configuración reCAPTCHA
        $this->loader->add_action( 'admin_notices', $plugin_notices, 'recaptcha_config_warning' );
        
        // Alerta de caché
        $this->loader->add_action( 'admin_notices', $plugin_notices, 'show_cache_warning' );

        //[NUEVO] Aviso de Novedad (Social Login) - AGREGAR ESTA LÍNEA
        $this->loader->add_action( 'admin_notices', $plugin_notices, 'notice_new_feature_social_login' );
        // AJAX para cerrar aviso de novedad
        $this->loader->add_action( 'wp_ajax_wporlogin_dismiss_notice_generic', $plugin_notices, 'ajax_dismiss_notice' );

        // Aviso de Reseña (5 Estrellas)
        $this->loader->add_action( 'admin_notices', $plugin_notices, 'show_review_notice' );
        // AJAX para cerrar aviso de reseña
        $this->loader->add_action( 'wp_ajax_delete-notice-wp', $plugin_notices, 'ajax_delete_notice' );

        // [NUEVO] Hook para limpiar el historial al guardar la Lista Blanca
        // Se ejecuta cuando se actualiza la opción 'wporlogin_limit_whitelist'
        $this->loader->add_action( 'update_option_wporlogin_limit_whitelist', $plugin_admin, 'reset_counters_on_whitelist_save', 10, 2 );

        // Dashboard Widget (Nueva Instancia para separar responsabilidades)
        $plugin_dashboard = new Wporlogin_Admin_Dashboard( $this->get_plugin_name(), $this->get_version() );

        // Hook directo: La clase dashboard se encarga de setup
        $this->loader->add_action( 'wp_dashboard_setup', $plugin_dashboard, 'add_dashboard_widget' );

        $customizer_bridge = new Wporlogin_Customizer_Bridge();
        $this->loader->add_action( 'load-post.php', $customizer_bridge, 'redirect_from_editor' );
        */


        // --- PROTECTOR (Global Admin Logic) ---
        // Instanciamos el protector para evitar borrados desde el listado de páginas
        $protector = new Wporlogin_Page_Protector();
        $this->loader->add_action( 'wp_trash_post', $protector, 'prevent_deletion' );
        $this->loader->add_action( 'before_delete_post', $protector, 'prevent_deletion' );
        $this->loader->add_action( 'admin_notices', $protector, 'show_warning_notice' );
        $this->loader->add_filter( 'page_row_actions', $protector, 'remove_quick_edit_link', 10, 2 );

        // --- PUENTE CUSTOMIZER (Admin Redirect) ---
        $customizer_bridge = new Wporlogin_Customizer_Bridge();
        // Redirige si intentan entrar al editor clásico/bloques
        $this->loader->add_action( 'load-post.php', $customizer_bridge, 'redirect_from_editor' );

        // --- LÓGICA PURA DE ADMIN ---
        // Solo instanciamos estas clases si estamos en Admin para respetar la carga condicional
        if ( is_admin() ) {
            
            $plugin_admin   = new Wporlogin_Admin( $this->get_plugin_name(), $this->get_version() );
            $plugin_notices = new Wporlogin_Admin_Notices( $this->get_plugin_name(), $this->get_version() );
            
            // Menús y Ajustes
            $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
            $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
            $this->loader->add_action( 'admin_init', $plugin_admin, 'process_unlock_ip_action' );
            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_assets' );
            $this->loader->add_action( 'update_option_wporlogin_limit_whitelist', $plugin_admin, 'reset_counters_on_whitelist_save', 10, 2 );

            // Avisos
            $this->loader->add_action( 'admin_notices', $plugin_notices, 'recaptcha_config_warning' );
            $this->loader->add_action( 'admin_notices', $plugin_notices, 'show_cache_warning' );
            $this->loader->add_action( 'admin_notices', $plugin_notices, 'notice_new_feature_social_login' );
            $this->loader->add_action( 'wp_ajax_wporlogin_dismiss_notice_generic', $plugin_notices, 'ajax_dismiss_notice' );
            $this->loader->add_action( 'admin_notices', $plugin_notices, 'show_review_notice' );
            $this->loader->add_action( 'wp_ajax_delete-notice-wp', $plugin_notices, 'ajax_delete_notice' );

            // Dashboard Widget
            $plugin_dashboard = new Wporlogin_Admin_Dashboard( $this->get_plugin_name(), $this->get_version() );
            $this->loader->add_action( 'wp_dashboard_setup', $plugin_dashboard, 'add_dashboard_widget' );
        }

        // [NUEVO] Ejecutar migración de base de datos en admin_init
        $this->loader->add_action( 'admin_init', $this, 'wporlogin_migration_unified_design' );


        // --- INSTANCIA DEL PERSONALIZADOR ---
        $customizer = new WPORLogin_Customizer();
        $customizer->init(); // Esto registra el hook 'customize_register'
        // Inicializar el generador para que escuche el "update_option"
        WPORLogin_Dynamic_CSS_Generator::init();
    }

    /**
     * Registra todos los hooks del área pública.
     *
     * @access private
     */
    private function define_public_hooks() {
        /*
        $public_design    = new Wporlogin_Public_Design( $this->get_plugin_name(), $this->get_version() );
        $public_security  = new Wporlogin_Public_Security( $this->get_plugin_name(), $this->get_version() );
        $public_redirects = new Wporlogin_Public_Redirects( $this->get_plugin_name(), $this->get_version() );
        $public_limit     = new Wporlogin_Public_Limit_Login( $this->get_plugin_name(), $this->get_version() ); // [NUEVO]

        // --- 1. REDIRECCIONES ---
        $this->loader->add_filter( 'login_redirect', $public_redirects, 'custom_login_redirect', 10, 3 );
        $this->loader->add_action( 'wp_logout', $public_redirects, 'custom_logout_redirect' );

        // --- 2. DISEÑO ---
        $this->loader->add_action( 'login_init', $public_design, 'remove_language_dropdown' );
        $this->loader->add_action( 'login_head', $public_design, 'inject_background_url' );
        $this->loader->add_action( 'login_head', $public_design, 'inject_brand_colors' );
        $this->loader->add_action( 'login_head', $public_design, 'inject_logo_styles' );
        $this->loader->add_action( 'login_enqueue_scripts', $public_design, 'apply_design_styles' );
        $this->loader->add_filter( 'login_headertext', $public_design, 'custom_login_header_title' ); 
        $this->loader->add_filter( 'login_headerurl', $public_design, 'custom_login_header_url' );

        // --- 3. SEGURIDAD (RECAPTCHA) ---
        $this->loader->add_action( 'login_enqueue_scripts', $public_security, 'enqueue_recaptcha_scripts' );
        $this->loader->add_action( 'login_form', $public_security, 'render_login_recaptcha' );
        $this->loader->add_filter( 'wp_authenticate_user', $public_security, 'verify_login_recaptcha', 10, 2 );
        $this->loader->add_action( 'register_form', $public_security, 'render_register_recaptcha' );
        $this->loader->add_filter( 'registration_errors', $public_security, 'verify_register_recaptcha', 10, 3 );
        $this->loader->add_action( 'lostpassword_form', $public_security, 'render_lostpassword_recaptcha' );
        $this->loader->add_filter( 'lostpassword_errors', $public_security, 'verify_lostpassword_recaptcha', 10, 1 );

        // --- 4. SEGURIDAD (LIMIT LOGIN ATTEMPTS) [NUEVO] ---
        // Verificar bloqueo antes de autenticar (Prioridad 1)
        $this->loader->add_filter( 'wp_authenticate_user', $public_limit, 'check_lockout', 1, 2 );
        // Registrar fallos
        $this->loader->add_action( 'wp_login_failed', $public_limit, 'handle_failed_login' );
        // Resetear contador al tener éxito
        $this->loader->add_action( 'wp_login', $public_limit, 'reset_counter_on_success', 10, 2 );
        // Mostrar aviso de intentos restantes
        $this->loader->add_filter( 'login_errors', $public_limit, 'show_remaining_attempts' );
        // [NUEVO] Tarea Programada (Cron) para limpieza de BD
        $this->loader->add_action( 'wporlogin_daily_maintenance_hook', $public_limit, 'purge_old_logs' );

        // -----------------------------------------------------------------
        // Lógica Hide Login (Ubicada en Public)
        // -----------------------------------------------------------------
        $plugin_hide_login = new Wporlogin_Public_Hide_Login( $this->get_plugin_name(), $this->get_version() );
        
        // Opción A: Si usas el método init() interno de la clase (recomendado para mantener limpio el orquestador)
        $plugin_hide_login->init();

        $plugin_social = new Wporlogin_Public_Social( $this->get_plugin_name(), $this->get_version() );

        // 1. Mostrar botones
        $this->loader->add_action( 'login_form', $plugin_social, 'render_social_buttons' );
        $this->loader->add_action( 'register_form', $plugin_social, 'render_social_buttons' );
        
        // 2. Cargar estilos CSS del botón
        $this->loader->add_action( 'login_enqueue_scripts', $plugin_social, 'enqueue_social_styles' );
        // 3. Cargar Script JS (En el FOOTER)
        $this->loader->add_action( 'login_footer', $plugin_social, 'output_social_footer_script' );

        // 4. Callback de retorno
        $this->loader->add_action( 'init', $plugin_social, 'handle_social_callback' );
        
        // [NUEVO] Manejo de errores de login (Mensaje rojo)
        $this->loader->add_filter( 'wp_login_errors', $plugin_social, 'custom_login_error_message' );

        // [NUEVO] Reemplazar Avatar (El efecto WOW)
        // Prioridad 10, acepta 5 argumentos (estándar de WP)
        $this->loader->add_filter( 'get_avatar', $plugin_social, 'replace_gravatar_with_social_image', 10, 5 );

        // --- MÓDULO DE PLANTILLAS (VISUALIZACIÓN) ---
        $template_engine = new Wporlogin_Public_Template();

        // 1. Reemplazar la plantilla del tema por la nuestra
        $this->loader->add_filter( 'template_include', $template_engine, 'load_custom_template' );
        
        // 2. Limpieza (Opcional)
        $this->loader->add_filter( 'theme_page_templates', $template_engine, 'hide_from_editor' );

        $customizer_bridge = new Wporlogin_Customizer_Bridge();
        $this->loader->add_action( 'template_redirect', $customizer_bridge, 'redirect_from_frontend' );

		// --- VISIBILIDAD ---
		$visibility = new Wporlogin_Public_Visibility();

		// 1. Selector Admin
        // Usamos 'pre_get_posts' que intercepta la consulta a la base de datos
        // Nota que el último número es 1, porque solo pasamos 1 variable ($query)
        $this->loader->add_action( 'pre_get_posts', $visibility, 'hide_from_menu_query', 999, 1 );

		// 2. Menú Manual (Frontend)
		$this->loader->add_filter( 'wp_nav_menu_objects', $visibility, 'hide_from_nav_menu' );

		// 3. [NUEVO] Menú Automático / Fallback (Frontend)
		// Este hook intercepta cuando WP lista las páginas automáticamente en el menú
		$this->loader->add_filter( 'wp_page_menu_args', $visibility, 'hide_from_fallback_menu' );

		// 4. Listados de páginas (Widgets)
		$this->loader->add_filter( 'wp_list_pages_excludes', $visibility, 'hide_from_page_list' );

		// 5. Búsquedas y Archivos
		$this->loader->add_action( 'pre_get_posts', $visibility, 'exclude_from_queries' );

        // 6. [NUEVO] Bloques de Navegación (Temas Modernos/FSE)
		// Filtramos directamente la función de bajo nivel get_pages()
		$this->loader->add_filter( 'get_pages', $visibility, 'hide_from_get_pages' );

        // 7. [NUEVO DEFINITIVO] Ocultar en API REST (Soluciona el problema de la imagen)
        // Esto filtra las páginas antes de que lleguen al Editor de Sitio
        $this->loader->add_filter( 'rest_page_query', $visibility, 'hide_from_rest_api', 10, 2 );
        */


        // --- VISIBILIDAD (SHARED) ---
        // ESTA ES LA CLAVE DE TU PREGUNTA:
        // Instanciamos Visibility aquí para que sus hooks se registren.
        // Aunque algunos hooks corren en Admin (como pre_get_posts para menús),
        // es seguro instanciarlo aquí porque el archivo ya fue incluido en load_dependencies.
        $visibility = new Wporlogin_Public_Visibility();

        // 1. Ocultar del Selector de Menús (ADMIN - Nav Menus)
        // Usamos la prioridad 999 para garantizar que funcione
        $this->loader->add_action( 'pre_get_posts', $visibility, 'hide_from_menu_query', 999, 1 );

        // 2. Ocultar del Menú Frontend
        $this->loader->add_filter( 'wp_nav_menu_objects', $visibility, 'hide_from_nav_menu' );
        $this->loader->add_filter( 'wp_page_menu_args', $visibility, 'hide_from_fallback_menu' );
        $this->loader->add_filter( 'wp_list_pages_excludes', $visibility, 'hide_from_page_list' );
        
        // 3. Ocultar de Búsquedas y Archivos
        $this->loader->add_action( 'pre_get_posts', $visibility, 'exclude_from_queries' );
        
        // 4. Compatibilidad FSE/Bloques/API
        $this->loader->add_filter( 'get_pages', $visibility, 'hide_from_get_pages' );
        $this->loader->add_filter( 'rest_page_query', $visibility, 'hide_from_rest_api', 10, 2 );


        // --- PUENTE CUSTOMIZER (Frontend Redirect) ---
        $customizer_bridge = new Wporlogin_Customizer_Bridge();
        $this->loader->add_action( 'template_redirect', $customizer_bridge, 'redirect_from_frontend' );


        // --- LÓGICA PURA DE FRONTEND ---
        // Solo instanciamos si NO es admin, o si es AJAX
        if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

            $public_design    = new Wporlogin_Public_Design( $this->get_plugin_name(), $this->get_version() );
            
            $public_assets = new Wporlogin_Public_Assets( $this->get_plugin_name(), $this->get_version() );
            // Pasamos el $loader para que la clase registre sus propios hooks (diseño más limpio)
            $public_assets->init_hooks( $this->loader );

            $public_security  = new Wporlogin_Public_Security( $this->get_plugin_name(), $this->get_version() );
            $public_redirects = new Wporlogin_Public_Redirects( $this->get_plugin_name(), $this->get_version() );
            $public_limit     = new Wporlogin_Public_Limit_Login( $this->get_plugin_name(), $this->get_version() );

            // Redirecciones
            $this->loader->add_filter( 'login_redirect', $public_redirects, 'custom_login_redirect', 10, 3 );
            $this->loader->add_action( 'wp_logout', $public_redirects, 'custom_logout_redirect' );

            // Diseño
            $this->loader->add_action( 'login_init', $public_design, 'remove_language_dropdown' );
            $this->loader->add_action( 'login_head', $public_design, 'inject_background_url' );
            $this->loader->add_action( 'login_head', $public_design, 'inject_brand_colors' );
            $this->loader->add_action( 'login_head', $public_design, 'inject_logo_styles' );
            $this->loader->add_action( 'login_enqueue_scripts', $public_design, 'apply_design_styles' );
            $this->loader->add_filter( 'login_headertext', $public_design, 'custom_login_header_title' ); 
            $this->loader->add_filter( 'login_headerurl', $public_design, 'custom_login_header_url' );

            // Seguridad (reCAPTCHA)
            $this->loader->add_action( 'login_enqueue_scripts', $public_security, 'enqueue_recaptcha_scripts' );
            $this->loader->add_action( 'login_form', $public_security, 'render_login_recaptcha' );
            $this->loader->add_filter( 'wp_authenticate_user', $public_security, 'verify_login_recaptcha', 10, 2 );
            $this->loader->add_action( 'register_form', $public_security, 'render_register_recaptcha' );
            $this->loader->add_filter( 'registration_errors', $public_security, 'verify_register_recaptcha', 10, 3 );
            $this->loader->add_action( 'lostpassword_form', $public_security, 'render_lostpassword_recaptcha' );
            $this->loader->add_filter( 'lostpassword_errors', $public_security, 'verify_lostpassword_recaptcha', 10, 1 );

            // Limit Login Attempts
            $this->loader->add_filter( 'wp_authenticate_user', $public_limit, 'check_lockout', 1, 2 );
            $this->loader->add_action( 'wp_login_failed', $public_limit, 'handle_failed_login' );
            $this->loader->add_action( 'wp_login', $public_limit, 'reset_counter_on_success', 10, 2 );
            $this->loader->add_filter( 'login_errors', $public_limit, 'show_remaining_attempts' );
            $this->loader->add_action( 'wporlogin_daily_maintenance_hook', $public_limit, 'purge_old_logs' );

            // Hide Login
            $plugin_hide_login = new Wporlogin_Public_Hide_Login( $this->get_plugin_name(), $this->get_version() );
            $plugin_hide_login->init();

            // Social Login
            $plugin_social = new Wporlogin_Public_Social( $this->get_plugin_name(), $this->get_version() );
            $this->loader->add_action( 'login_form', $plugin_social, 'render_social_buttons' );
            $this->loader->add_action( 'register_form', $plugin_social, 'render_social_buttons' );
            $this->loader->add_action( 'login_enqueue_scripts', $plugin_social, 'enqueue_social_styles' );
            $this->loader->add_action( 'login_footer', $plugin_social, 'output_social_footer_script' );
            $this->loader->add_action( 'init', $plugin_social, 'handle_social_callback' );
            $this->loader->add_filter( 'wp_login_errors', $plugin_social, 'custom_login_error_message' );
            $this->loader->add_filter( 'get_avatar', $plugin_social, 'replace_gravatar_with_social_image', 10, 5 );
            // [NUEVO] Módulo de restricción de registro
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/social/class-wporlogin-social-only-register.php';
            $social_restriction = new Wporlogin_Social_Only_Register();
            $social_restriction->init();

            // Template Engine
            $template_engine = new Wporlogin_Public_Template();
            $this->loader->add_filter( 'template_include', $template_engine, 'load_custom_template' );
            //$this->loader->add_filter( 'theme_page_templates', $template_engine, 'hide_from_editor' );

        }

    }

    /**
     * Ejecuta el loader para registrar todos los hooks con WordPress.
     * @since 1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Devuelve el nombre del plugin.
     * @return string
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Devuelve el loader.
     * @return Wporlogin_Loader
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Devuelve la versión.
     * @return string
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Obtiene la lista de URLs de imágenes de fondo predeterminadas.
     * @static
     * @return array
     */
    public static function get_default_backgrounds() {
        return array(
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-0.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-1.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-2.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-3.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-4.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-5.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-6.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-7.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-8.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-9.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-10.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-11.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-12.jpg',
            WPORLOGIN_URL . 'assets/img/wporlogin-img-fondo-13.jpg'
        );
    }

    /**
     * Migración de Base de Datos para WPOrLogin 3.0.0.
     * Unifica las opciones antiguas en 'wporlogin_active_design'.
     */
    public function wporlogin_migration_unified_design() {
        
        // 1. Verificamos la versión de la BD
        $db_version = get_option( 'wporlogin_db_version', '1.0.0' );
        
        // Solo ejecutamos si es una versión anterior a la 3.0.0
        if ( version_compare( $db_version, '3.0.0', '<' ) ) {
            
            $old_main_design = get_option( 'wporlogin_design' );             // basic, standard, premium
            $old_premium_img = get_option( 'wporlogin-design-img-premium' ); // wporlogin_design_img_premium_one, etc.
            
            // Valor por defecto ante cualquier duda
            $new_value = 'wporlogin_design_basic'; 

            // A. Lógica de conversión
            if ( $old_main_design === 'wporlogin_design_basic' ) {
                $new_value = 'wporlogin_design_basic';
            } 
            elseif ( $old_main_design === 'wporlogin_design_standard' ) {
                $new_value = 'wporlogin_design_standard';
            } 
            elseif ( $old_main_design === 'wporlogin_design_premium' ) {
                // Si es premium, usamos el ID de la imagen antigua
                if ( ! empty( $old_premium_img ) ) {
                    $new_value = $old_premium_img; 
                } else {
                    // Fallback de seguridad: Si era premium pero no tenía imagen guardada,
                    // lo mandamos a la primera plantilla o a básico.
                    $new_value = 'wporlogin_design_img_premium_one'; 
                }
            }

            // B. Guardamos la nueva opción unificada
            update_option( 'wporlogin_active_design', $new_value );
            
            // C. Actualizamos la versión para que no se ejecute de nuevo
            update_option( 'wporlogin_db_version', '3.0.0' );

            // Opcional: Borrar opciones viejas (Recomiendo esperar una versión más para esto por seguridad)
            // delete_option( 'wporlogin_design' );
            // delete_option( 'wporlogin-design-img-premium' );
        }
    }
}