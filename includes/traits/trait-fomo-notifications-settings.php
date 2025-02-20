<?php
/**
 * FOMO Notifications settings trait.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * FOMO Notifications settings trait.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
trait Fomo_Notifications_Settings_Trait {

	/**
	 * Holds the Settings
	 *
	 * @since   1.0.0
	 *
	 * @var     array
	 */
	private $settings = array();

	/**
	 * Returns Plugin settings.
	 *
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function get() {

		return $this->settings;

	}

	/**
	 * Returns the source setting for the given key.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $key    Setting key.
	 * @return  string|int|array
	 */
	public function get_by_key( $key ) {

		return $this->settings[ $key ];

	}

}
