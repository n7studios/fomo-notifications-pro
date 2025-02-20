<?php
/**
 * FOMO Notifications plugin settings class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * FOMO Notifications plugin settings class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Plugin_Settings {

	use Fomo_Notifications_Settings_Trait;

	/**
	 * Holds the Settings Key that stores Plugin settings
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const SETTINGS_NAME = '_fomo_notifications_settings';

	/**
	 * Constructor. Reads settings from options table, falling back to defaults
	 * if no settings exist.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Get Settings.
		$settings = get_option( self::SETTINGS_NAME );

		// If no Settings exist, falback to default settings.
		if ( ! $settings ) {
			$this->settings = $this->get_defaults();
		} else {
			$this->settings = array_merge( $this->get_defaults(), $settings );
		}

	}

	/**
	 * Returns the number of notifications setting.
	 *
	 * @since   1.0.0
	 *
	 * @return  int
	 */
	public function limit() {

		return (int) $this->settings['limit'];

	}

	/**
	 * Returns whether to loop notifications.
	 *
	 * @since   1.0.0
	 *
	 * @return  bool
	 */
	public function loop() {

		return (bool) $this->settings['loop'];

	}

	/**
	 * Returns the source setting.
	 *
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	public function source() {

		return $this->settings['source'];

	}

	/**
	 * The default settings, used when the Plugin Settings haven't been saved
	 * e.g. on a new installation.
	 *
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function get_defaults() {

		$defaults = array(
			'limit'  => 10,
			'loop'   => true,
			'source' => 'woocommerce',
		);

		/**
		 * The default Plugin settings.
		 *
		 * @since   1.0.0
		 *
		 * @param   array   $defaults   Default Settings.
		 */
		$defaults = apply_filters( 'fomo_notifications_plugin_settings_get_defaults', $defaults );

		return $defaults;

	}

	/**
	 * Saves the given array of settings to the WordPress options table.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $settings   Settings.
	 */
	public function save( $settings ) {

		update_option( self::SETTINGS_NAME, array_merge( $this->get(), $settings ) );

		// Reload settings in class, to reflect changes.
		$this->settings = get_option( self::SETTINGS_NAME );

	}

}
