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
	 * The programmatic name of the notification source
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	private $name = '';

	/**
	 * The label / title of the notification source
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	private $label = '';

	/**
	 * The description text to display in the settings section
	 * for this source, below the heading.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	private $description = '';

	/**
	 * The default settings for the source.
	 *
	 * @since   1.0.0
	 *
	 * @var     array
	 */
	private $defaults = array();

	/**
	 * Register this integration as a notification source.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $sources    Notification sources.
	 */
	public function register_source( $sources ) {

		return array_merge( $sources, array( $this->name => $this->label ) );

	}

	/**
	 * The default settings, used when a notification has not yet been saved.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $defaults   Default Settings.
	 */
	public function get_defaults( $defaults ) {

		return array_merge(
			$defaults,
			$this->defaults
		);

	}

	public function shorten_name( $name ) {

		
		
	}

}
