<?php
/**
 * FOMO Notifications settings source trait.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * FOMO Notifications settings source trait.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
trait Fomo_Notifications_Source_Trait {

	/**
	 * Options table key
	 *
	 * @since   1.0.0
	 *
	 * @var string
	 */
	public $settings_key = '';

	/**
	 * Holds the settings class for the section.
	 *
	 * @since   1.0.0
	 *
	 * @var     false|Fomo_Notifications_Settings
	 */
	public $settings;

	/**
	 * The programmatic name of the notification source
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	public $source_name = '';

	/**
	 * The label / title of the notification source
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	public $source_label = '';

	/**
	 * The description text to display in the settings section
	 * for this source, below the heading.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	public $source_description = '';

	/**
	 * Register this integration as a notification source.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $sources    Notification sources.
	 */
	public function register_source( $sources ) {

		return array_merge( $sources, array( $this->source_name => $this->source_label ) );

	}

	/**
	 * Register a settings section on the Plugin settings for this source.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $sections   Sections.
	 * @return  array
	 */
	public function register_settings_section( $sections ) {

		return array_merge(
			$sections,
			array(
				$this->source_name => array(
					'title'    => $this->source_label,
					'callback' => array( $this, 'settings_section_info' ),
					'wrap'     => true,
				),
			)
		);

	}

	/**
	 * Outputs the description in the settings section, after the title.
	 *
	 * @since   1.0.0
	 */
	public function settings_section_info() {

		?>
		<p class="description"><?php esc_html( $this->source_description ); ?></p>
		<?php

	}

}
