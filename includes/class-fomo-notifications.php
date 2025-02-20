<?php
/**
 * FOMO Notifications class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * FOMO Notifications class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications {

	/**
	 * Holds the class object.
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	private static $instance;

	/**
	 * Holds the plugin information object.
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	public $plugin;

	/**
	 * Holds the dashboard class object.
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	public $dashboard;

	/**
	 * Holds singleton initialized classes that include
	 * action and filter hooks.
	 *
	 * @since   1.0.0
	 *
	 * @var     array
	 */
	private $classes = array();

	/**
	 * Constructor. Acts as a bootstrap to load the rest of the plugin
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Plugin Details.
		$this->plugin                    = new stdClass();
		$this->plugin->name              = 'fomo-notifications';
		$this->plugin->displayName       = 'FOMO Notifications';
		$this->plugin->author_name       = 'WP Zinc';
		$this->plugin->version           = FOMO_NOTIFICATIONS_PLUGIN_VERSION;
		$this->plugin->buildDate         = FOMO_NOTIFICATIONS_PLUGIN_BUILD_DATE;
		$this->plugin->folder            = FOMO_NOTIFICATIONS_PLUGIN_PATH;
		$this->plugin->url               = FOMO_NOTIFICATIONS_PLUGIN_URL;
		$this->plugin->documentation_url = 'https://www.wpzinc.com/documentation/fomo-notifications';
		$this->plugin->support_url       = 'https://www.wpzinc.com/support';
		$this->plugin->logo              = FOMO_NOTIFICATIONS_PLUGIN_URL . 'assets/images/icons/logo-dark.svg';
		$this->plugin->review_name       = false;

		// Dashboard Submodule.
		if ( ! class_exists( 'WPZincDashboardWidget' ) ) {
			require_once $this->plugin->folder . '_modules/dashboard/class-wpzincdashboardwidget.php';
		}
		$this->dashboard = new WPZincDashboardWidget( $this->plugin );

		// Defer loading of Plugin Classes.
		add_action( 'init', array( $this, 'initialize' ), 1 );

		// Admin Menus.
		add_action( 'fomo_notifications_admin_settings_add_settings_page', array( $this, 'admin_menu' ) );

		// Localization.
		add_action( 'init', array( $this, 'load_language_files' ) );

	}

	/**
	 * Register menus and submenus.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $minimum_capability  Minimum capability required for access.
	 */
	public function admin_menu( $minimum_capability ) {

		add_menu_page(
			__( 'FOMO Notifications', 'fomo-notifications' ),
			__( 'FOMO Notifications', 'fomo-notifications' ),
			$minimum_capability,
			$this->plugin->name,
			array( $this->classes['admin_settings'], 'display_settings_page' ),
			FOMO_NOTIFICATIONS_PLUGIN_URL . 'assets/images/icons/logo-light.svg'
		);

		add_submenu_page(
			$this->plugin->name,
			__( 'Settings', 'fomo-notifications' ),
			__( 'Settings', 'fomo-notifications' ),
			$minimum_capability,
			$this->plugin->name,
			array( $this->classes['admin_settings'], 'display_settings_page' ),
		);

		add_submenu_page(
			$this->plugin->name,
			__( 'Notifications', 'fomo-notifications' ),
			__( 'Notifications', 'fomo-notifications' ),
			$minimum_capability,
			'edit.php?post_type=fomo-notification'
		);

	}

	/**
	 * Initializes required classes
	 *
	 * @since   1.0.0
	 */
	public function initialize() {

		$this->initialize_admin();
		$this->initialize_frontend();
		$this->initialize_global();

	}

	/**
	 * Initialize classes for the WordPress Administration interface
	 *
	 * @since   1.0.0
	 */
	private function initialize_admin() {

		// Bail if this request isn't for the WordPress Administration interface.
		if ( ! is_admin() ) {
			return;
		}

		$this->classes['admin_notification_ui'] = new Fomo_Notifications_Admin_Notification_UI();
		$this->classes['admin_settings']        = new Fomo_Notifications_Admin_Settings();

		/**
		 * Initialize integration classes for the WordPress Administration interface.
		 *
		 * @since   1.0.0
		 */
		do_action( 'fomo_notifications_initialize_admin' );

	}

	/**
	 * Initialize classes for the frontend web site
	 *
	 * @since   1.9.6
	 */
	private function initialize_frontend() {

		// Bail if this request isn't for the frontend web site.
		if ( is_admin() ) {
			return;
		}

		$this->classes['output'] = new Fomo_Notifications_Output();

		/**
		 * Initialize integration classes for the frontend web site.
		 *
		 * @since   1.0.0
		 */
		do_action( 'fomo_notifications_initialize_frontend' );

	}

	/**
	 * Initialize classes required globally, across the WordPress Administration, CLI, Cron and Frontend
	 * web site.
	 *
	 * @since   1.0.0
	 */
	private function initialize_global() {

		$this->classes['post_type']   = new Fomo_Notifications_Post_Type();
		$this->classes['woocommerce'] = new Fomo_Notifications_Source_Woocommerce();

		/**
		 * Initialize integration classes for the frontend web site.
		 *
		 * @since   1.0.0
		 */
		do_action( 'fomo_notifications_initialize_global' );

	}

	/**
	 * Loads the plugin's translated strings, if available.
	 *
	 * @since   1.0.0
	 */
	public function load_language_files() {

		// If the .mo file for a given language is available in WP_LANG_DIR/fomo-notifications
		// i.e. it's available as a translation at https://translate.wordpress.org/projects/wp-plugins/fomo-notifications/,
		// it will be used instead of the .mo file in fomo-notifications/languages.
		load_plugin_textdomain( 'fomo-notifications', false, 'fomo-notifications/languages' );

	}

	/**
	 * Returns the given class
	 *
	 * @since   1.0.0
	 *
	 * @param   string $name   Class Name.
	 * @return  object          Class Object
	 */
	public function get_class( $name ) {

		// If the class hasn't been loaded, throw a WordPress die screen
		// to avoid a PHP fatal error.
		if ( ! isset( $this->classes[ $name ] ) ) {
			// Define the error.
			$error = new WP_Error(
				'fomo_notifications_get_class',
				sprintf(
					/* translators: %1$s: PHP class name */
					__( 'FOMO Notifications Error: Could not load Plugin class <strong>%1$s</strong>', 'fomo-notifications' ),
					$name
				)
			);

			// Depending on the request, return or display an error.
			// Admin UI.
			if ( is_admin() ) {
				wp_die(
					esc_attr( $error->get_error_message() ),
					esc_html__( 'FOMO Notifications Error', 'fomo-notifications' ),
					array(
						'back_link' => true,
					)
				);
			}

			// Cron / CLI.
			return $error;
		}

		// Return the class object.
		return $this->classes[ $name ];

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since   1.0.0
	 *
	 * @return  object Class.
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}
