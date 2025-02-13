<?php
/**
 * FOMO Notifications settings section general class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * Registers General Settings that can be edited at Settings > FOMO Notifications > General.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Admin_Section_General {

	use Fomo_Notifications_Admin_Section_Trait;
	use Fomo_Notifications_Admin_Section_Fields_Trait;

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Define the class that reads/writes settings.
		$this->settings = new Fomo_Notifications_Settings();

		// Define the programmatic name, title, tab and settings key.
		$this->name         = 'general';
		$this->title        = __( 'General Settings', 'fomo-notifications' );
		$this->tab_text     = __( 'General', 'fomo-notifications' );
		$this->settings_key = $this->settings::SETTINGS_NAME;

		// Define settings sections.
		$settings_sections = array(
			'general' => array(
				'title'    => $this->title,
				'callback' => array( $this, 'print_section_info' ),
				'wrap'     => true,
			),
		);

		/**
		 * Define settings sections for the General screen.
		 *
		 * @since   1.0.0
		 *
		 * @param   array   $settings_sections  Settings sections.
		 */
		$settings_sections = apply_filters( 'fomo_notifications_admin_section_general_sections', $settings_sections );

		// Assign to class.
		$this->settings_sections = $settings_sections;
		unset( $settings_sections );

		// Enqueue CSS.
		add_action( 'fomo_notifications_admin_settings_enqueue_styles', array( $this, 'enqueue_styles' ) );

		// If tab text is not defined, use the title for the tab's text.
		if ( empty( $this->tab_text ) ) {
			$this->tab_text = $this->title;
		}

		// Register the settings section.
		$this->register_section();

	}

	/**
	 * Enqueues styles for the Settings > General screen.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $section    Settings section / tab.
	 */
	public function enqueue_styles( $section ) {

		// Bail if we're not on the general section.
		if ( $section !== $this->name ) {
			return;
		}

	}

	/**
	 * Registers settings fields for this section.
	 *
	 * @since   1.0.0
	 */
	public function register_fields() {

		// Fetch registered notification sources.
		$sources = apply_filters( 'fomo_notifications_get_sources', array() );

		// Define fields.
		$fields = array(
			'limit'  => array(
				'title'   => __( 'Number of Notifications', 'fomo-notifications' ),
				'section' => $this->name,
				'props'   => array(
					'type'        => 'number',
					'value'       => $this->settings->limit(),
					'min'         => 1,
					'max'         => 50,
					'step'        => 1,
					'description' => esc_html__( 'The maximum number of notifications to display.', 'fomo-notifications' ),
				),
			),
			'loop'   => array(
				'title'   => __( 'Loop Notifications', 'fomo-notifications' ),
				'section' => $this->name,
				'props'   => array(
					'type'        => 'checkbox',
					'value'       => $this->settings->loop(),
					'description' => esc_html__( 'If enabled, notifications will continue to display in a loop.', 'fomo-notifications' ),
				),
			),
			'source' => array(
				'title'   => __( 'Source', 'fomo-notifications' ),
				'section' => $this->name,
				'props'   => array(
					'type'        => 'select',
					'value'       => $this->settings->source(),
					'options'     => $sources,
					'description' => esc_html__( 'The source to use for notification data.', 'fomo-notifications' ),
				),
			),
		);

		/**
		 * Register settings fields for the general settings screen.
		 *
		 * @since   1.0.0
		 *
		 * @param   array                           $fields     Fields.
		 * @param   Fomo_Notifications_Settings     $settings   Settings class.
		 */
		$fields = apply_filters( 'fomo_notifications_admin_section_general_register_fields', $fields, $this->settings );

		// Add settings fields.
		foreach ( $fields as $id => $field ) {
			add_settings_field(
				$id,
				$field['title'],
				( array_key_exists( 'callback', $field ) ? $field['callback'] : array( $this, $field['props']['type'] . '_field_callback' ) ),
				$this->settings_key,
				$field['section'],
				array_merge(
					$field['props'],
					array(
						'name' => $id,
					)
				)
			);
		}

	}

	/**
	 * Prints help info for the general section of the settings screen.
	 *
	 * @since   1.0.0
	 */
	public function print_section_info() {

		?>
		<p class="description"><?php esc_html_e( 'Define the source of notifications, number of notifications to display and whether to loop notifications.', 'fomo-notifications' ); ?></p>
		<?php

	}

	/**
	 * Returns the URL for the Plugin documentation for this setting section.
	 *
	 * @since   2.0.8
	 *
	 * @return  string  Documentation URL.
	 */
	public function documentation_url() {

		return 'https://www.wpzinc.com/documentation/fomo-notifications';

	}

}
