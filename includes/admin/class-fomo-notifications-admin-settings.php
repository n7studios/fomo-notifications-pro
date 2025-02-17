<?php
/**
 * FOMO Notifications admin settings class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * Registers a screen at Settings > FOMO Notifications in the WordPress Administration
 * interface, and handles saving its data.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Admin_Settings {

	/**
	 * Settings sections
	 *
	 * @since   1.0.0
	 *
	 * @var array
	 */
	public $sections = array();

	/**
	 * Holds the Settings Page Slug
	 *
	 * @var     string
	 */
	const SETTINGS_PAGE_SLUG = 'fomo-notifications-settings';

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_sections' ) );
		add_filter( 'plugin_action_links_' . FOMO_NOTIFICATIONS_PLUGIN_FILE, array( $this, 'add_settings_page_link' ) );

	}

	/**
	 * Enqueue JavaScript in Admin
	 *
	 * @since   1.0.0
	 *
	 * @param   string $hook   Hook.
	 */
	public function enqueue_scripts( $hook ) {

		// Bail if we are not on the Settings screen.
		if ( $hook !== 'settings_page_' . self::SETTINGS_PAGE_SLUG ) {
			return;
		}

		// Get active settings section / tab that has been requested.
		$section = $this->get_active_section();

		/**
		 * Enqueue JavaScript for the Settings Screen.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $section    Settings section / tab.
		 */
		do_action( 'fomo_notifications_admin_settings_enqueue_scripts', $section );

	}

	/**
	 * Enqueue CSS for the Settings Screens
	 *
	 * @since   1.0.0
	 *
	 * @param   string $hook   Hook.
	 */
	public function enqueue_styles( $hook ) {

		// Always enqueue Settings CSS, as this is used for the UI across all settings sections and the admin menu icon.
		wp_enqueue_style( 'fomo-notifications-admin-settings', FOMO_NOTIFICATIONS_PLUGIN_URL . 'assets/css/settings.css', array(), FOMO_NOTIFICATIONS_PLUGIN_VERSION );

		// Bail if we are not on the Settings screen.
		if ( $hook !== 'toplevel_page_' . self::SETTINGS_PAGE_SLUG ) {
			return;
		}

		// Get active settings section / tab that has been requested.
		$section = $this->get_active_section();

		/**
		 * Enqueue CSS for the Settings Screen.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $section    Settings section / tab.
		 */
		do_action( 'fomo_notifications_admin_settings_enqueue_styles', $section );

	}

	/**
	 * Adds the options page
	 *
	 * @since   1.9.6
	 */
	public function add_settings_page() {

		// Define the minimum capability required to access settings.
		$minimum_capability = 'manage_options';

		/**
		 * Defines the minimum capability required to access the Plugin's
		 * Menu and Sub Menus
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $capability     Minimum Required Capability.
		 * @return  string                  Minimum Required Capability
		 */
		$minimum_capability = apply_filters( 'fomo_notifications_admin_settings_minimum_capability', $minimum_capability );

		/**
		 * Add settings menus and sub menus for the Plugin's settings.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $minimum_capability     Minimum capability required.
		 */
		do_action( 'fomo_notifications_admin_settings_add_settings_page', $minimum_capability );

	}

	/**
	 * Outputs the settings screen.
	 *
	 * @since   1.0.0
	 */
	public function display_settings_page() {

		$active_section = $this->get_active_section();
		?>

		<header style="--wpzinc-logo: url('<?php echo esc_attr( FOMO_NOTIFICATIONS_PLUGIN_URL ); // @phpstan-ignore-line ?>assets/images/icons/logo-dark.svg')">
			<h1>
				<?php echo esc_html_e( 'FOMO Notifications Pro', 'fomo-notifications' ); ?>

				<span>
					<?php esc_html_e( 'Settings', 'fomo-notifications' ); ?>
				</span>
			</h1>
		</header>

		<?php
		// WordPress' JS will automatically move any .notice elements to be immediately below .wp-header-end
		// or <h2>, whichever comes first.
		// As our <h2> is inside our .metabox-holder, we output .wp-header-end first to control the notification
		// placement to be before the white background container/box.
		?>
		<hr class="wp-header-end">

		<div class="wrap">
			<div class="wrap-inner">
				<?php
				$this->display_section_nav( $active_section );
				?>

				<form method="post" action="options.php" enctype="multipart/form-data" class="wpzinc-settings-ui">
					<?php
					// Iterate through sections to find the active section to render.
					if ( isset( $this->sections[ $active_section ] ) ) {
						$this->sections[ $active_section ]->render();
					}
					?>
				</form>

				<p class="description">
					<?php
					// Output Help link, if it exists.
					$documentation_url = $this->get_active_section_documentation_url( $active_section );
					if ( $documentation_url !== false ) {
						printf(
							'%s <a href="%s" target="_blank">%s</a>',
							esc_html__( 'If you need help setting up the plugin please refer to the', 'fomo-notifications' ),
							esc_attr( $documentation_url ),
							esc_html__( 'plugin documentation', 'fomo-notifications' )
						);
					}
					?>
				</p>
			</div>
		</div>
		<?php

	}

	/**
	 * Gets the active tab section that the user is viewing on the Plugin Settings screen.
	 *
	 * @since   1.0.0
	 *
	 * @return  string  Tab Name
	 */
	private function get_active_section() {

		if ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return sanitize_text_field( wp_unslash( $_GET['tab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		// First registered section will be the active section.
		return current( $this->sections )->name;

	}

	/**
	 * Define links to display below the Plugin Name on the WP_List_Table at in the Plugins screen.
	 *
	 * @param   array $links      Links.
	 * @return  array               Links
	 */
	public function add_settings_page_link( $links ) {

		// Add link to Plugin settings screen.
		$links['settings'] = sprintf(
			'<a href="%s">%s</a>',
			add_query_arg(
				array(
					'page' => self::SETTINGS_PAGE_SLUG,
				),
				admin_url( 'options-general.php' )
			),
			__( 'Settings', 'fomo-notifications' )
		);

		/**
		 * Define links to display below the Plugin Name on the WP_List_Table at Plugins > Installed Plugins.
		 *
		 * @since   1.0.0
		 *
		 * @param   array   $links  HTML Links.
		 */
		$links = apply_filters( 'fomo_notifications_plugin_screen_action_links', $links );

		// Return.
		return $links;

	}

	/**
	 * Output tabs, one for each registered settings section.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $active_section     Currently displayed/selected section.
	 */
	public function display_section_nav( $active_section ) {

		?>
		<h2 class="nav-tab-wrapper wpzinc-horizontal-tabbed-ui">
			<?php
			foreach ( $this->sections as $section ) {
				printf(
					'<a href="%s" class="nav-tab %s">%s</a>',
					esc_url(
						add_query_arg(
							array(
								'page' => self::SETTINGS_PAGE_SLUG,
								'tab'  => $section->name,
							),
							admin_url( 'options-general.php' )
						)
					),
					( $active_section === $section->name ? 'nav-tab-active' : '' ),
					esc_html( $section->tab_text )
				);
			}
			?>
		</h2>
		<?php

	}

	/**
	 * Registers settings sections.
	 *
	 * Each section has its own tab.
	 *
	 * @since   1.0.0
	 */
	public function register_sections() {

		// Register the General settings sections.
		$sections = array(
			'general' => new Fomo_Notifications_Admin_Section_General(),
		);

		/**
		 * Registers settings sections.
		 *
		 * @since   1.0.0
		 *
		 * @param   array   $sections   Array of settings classes that handle individual tabs e.g. General, Tools etc.
		 */
		$sections = apply_filters( 'fomo_notifications_admin_settings_register_sections', $sections );

		// With our sections now registered, assign them to this class.
		$this->sections = $sections;

	}

	/**
	 * Returns the documentation URL for the active settings section viewed by the user.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $active_section     Currently displayed/selected section.
	 * @return  bool|string
	 */
	private function get_active_section_documentation_url( $active_section ) {

		// Bail if no sections registered.
		if ( ! $this->sections ) {
			return false;
		}

		// Bail if the active section isn't registered.
		if ( ! array_key_exists( $active_section, $this->sections ) ) {
			return false;
		}

		// Pass request to section's documentation_url() function, including UTM parameters.
		return add_query_arg(
			array(
				'utm_source'  => 'wordpress',
				'utm_term'    => get_locale(),
				'utm_content' => 'fomo-notifications',
			),
			$this->sections[ $active_section ]->documentation_url()
		);

	}

}
