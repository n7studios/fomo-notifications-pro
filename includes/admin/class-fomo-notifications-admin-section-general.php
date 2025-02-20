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
		$this->settings = new Fomo_Notifications_Plugin_Settings();

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

		// Define fields.
		$fields = array(
			'display_multiple_templates' => array(
				'title'   => __( 'Display multiple?', 'fomo-notifications' ),
				'section' => $this->name,
				'props'   => array(
					'type'        => 'checkbox',
					'value'       => $this->settings->loop(),
					'description' => esc_html__( 'If enabled, multiple notification templates may be displayed.', 'fomo-notifications' ),
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
		<p class="description"><?php esc_html_e( 'Define the behaviour of notification templates.', 'fomo-notifications' ); ?></p>
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
