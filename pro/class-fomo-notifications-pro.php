<?php
/**
 * FOMO Notifications Pro class.
 *
 * @package Fomo_Notifications_Pro
 * @author WP Zinc
 */

/**
 * FOMO Notifications class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Pro {

	/**
	 * Holds the class object.
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	private static $instance;

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
		$this->plugin->name              = 'fomo-notifications-pro';
		$this->plugin->displayName       = 'FOMO Notifications Pro';
		$this->plugin->author_name       = 'WP Zinc';
		$this->plugin->version           = FOMO_NOTIFICATIONS_PRO_PLUGIN_VERSION;
		$this->plugin->buildDate         = FOMO_NOTIFICATIONS_PRO_PLUGIN_BUILD_DATE;
		$this->plugin->requires          = '5.0';
		$this->plugin->tested            = '6.1.1';
		$this->plugin->php_requires      = '7.4';
		$this->plugin->folder            = FOMO_NOTIFICATIONS_PRO_PLUGIN_PATH;
		$this->plugin->url               = FOMO_NOTIFICATIONS_PRO_PLUGIN_URL;
		$this->plugin->documentation_url = 'https://www.wpzinc.com/documentation/fomo-notifications-pro';
		$this->plugin->support_url       = 'https://www.wpzinc.com/support';
		$this->plugin->upgrade_url       = 'https://www.wpzinc.com/plugins/fomo-notifications-pro';
		$this->plugin->review_name       = 'fomo-notifications';
		$this->plugin->review_notice     = sprintf(
			/* translators: Plugin Name */
			__( 'Thanks for using %s to increase sales on your web site!', 'fomo-notifications' ),
			$this->plugin->displayName
		);

		// Licensing Submodule.
		if ( ! class_exists( 'LicensingUpdateManager' ) ) {
			require_once $this->plugin->folder . '_modules/licensing/class-licensingupdatemanager.php';
		}
		$this->licensing = new LicensingUpdateManager( $this->plugin, 'https://www.wpzinc.com' );

		// Run Plugin Display Name, URLs through Whitelabelling if available.
		$this->plugin->displayName       = $this->licensing->get_feature_parameter( 'whitelabelling', 'display_name', $this->plugin->displayName );
		$this->plugin->support_url       = $this->licensing->get_feature_parameter( 'whitelabelling', 'support_url', $this->plugin->support_url );
		$this->plugin->documentation_url = $this->licensing->get_feature_parameter( 'whitelabelling', 'documentation_url', $this->plugin->documentation_url );

		// Dashboard Submodule.
		if ( ! class_exists( 'WPZincDashboardWidget' ) ) {
			require_once $this->plugin->folder . '_modules/dashboard/class-wpzincdashboardwidget.php';
		}
		$this->dashboard = new WPZincDashboardWidget( $this->plugin, 'https://www.wpzinc.com/wp-content/plugins/lum-deactivation' );

		// Show Support Menu and hide Upgrade Menu.
		$this->dashboard->show_support_menu();
		$this->dashboard->hide_upgrade_menu();

		// Disable Review Notification if whitelabelling is enabled.
		if ( $this->licensing->has_feature( 'whitelabelling' ) ) {
			$this->dashboard->disable_review_request();
		}

		// Defer loading of Plugin Classes.
		add_action( 'admin_init', array( $this, 'deactivate_free_version' ) );

		// Defer loading of Plugin Classes.
		add_action( 'init', array( $this, 'initialize' ), 1 );

		// Localization.
		add_action( 'init', array( $this, 'load_language_files' ) );

	}

	/**
	 * Detects if the Free version of the Plugin is running, and if so,
	 * deactivates it.
	 *
	 * @since   1.0.0
	 */
	public function deactivate_free_version() {

		// Bail if the function is not available.
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			return;
		}

		// Bail if the Free version is not active.
		if ( ! is_plugin_active( 'fomo-notifications/fomo-notifications.php' ) ) {
			return;
		}

		// Deactivate the Free version.
		deactivate_plugins( 'fomo-notifications/fomo-notifications.php' );

	}

	/**
	 * Initializes required and licensed classes
	 *
	 * @since   1.9.8
	 */
	public function initialize() {

		$this->classes = new stdClass();

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

		$this->classes['admin_settings'] = new Fomo_Notifications_Admin_Settings();

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

		// Bail if not licensed.
		if ( ! $this->licensing->check_license_key_valid() ) {
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

		// Bail if not licensed.
		if ( ! $this->licensing->check_license_key_valid() ) {
			return;
		}

		$this->classes['lum'] = new Fomo_Notifications_Source_Lum();
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

		load_plugin_textdomain( 'fomo-notifications', false, $this->plugin->name . '/languages' );

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
