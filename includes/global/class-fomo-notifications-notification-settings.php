<?php
/**
 * Notification settings class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * Reads and writes settings for an individual Notification.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Notification_Settings {

	use Fomo_Notifications_Settings_Trait;

	/**
	 * Holds the Post Meta Key that stores settings on a per-Notification basis
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const SETTINGS_NAME = '_fomo_notification_settings';

	/**
	 * Holds the Notification ID
	 *
	 * @since   1.0.0
	 *
	 * @var     int
	 */
	public $post_id = 0;

	/**
	 * Constructor. Populates the settings based on the given Notification ID.
	 *
	 * @since   1.0.0
	 *
	 * @param   int $post_id    Notification ID.
	 */
	public function __construct( $post_id ) {

		// Assign Post's ID to the object.
		$this->post_id = $post_id;

		// Get Post Meta.
		$settings = get_post_meta( $post_id, self::SETTINGS_NAME, true );

		// If no Settings exist, falback to default settings.
		if ( ! $settings ) {
			$this->settings = $this->get_defaults();
		} else {
			$this->settings = array_merge( $this->get_defaults(), $settings );
		}

	}

	/**
	 * Returns the source to use for this notification.
	 *
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	public function get_source() {

		return $this->settings['source'];

	}

	/**
	 * The default settings, used when a Notification's Settings haven't been saved
	 * e.g. on a new notification.
	 *
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function get_defaults() {

		$defaults = array(
			// Source.
			'source' => '',

			// Conditions.
			'conditions_visitor_type' => '',
		);

		/**
		 * The default Plugin settings.
		 *
		 * @since   1.0.0
		 *
		 * @param   array   $defaults   Default Settings.
		 */
		$defaults = apply_filters( 'fomo_notifications_notification_settings_get_defaults', $defaults );

		return $defaults;

	}

	/**
	 * Saves Post settings to the Post.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $meta   Settings.
	 */
	public function save( $meta ) {

		update_post_meta( $this->post_id, self::SETTINGS_NAME, $meta );

		// Reload settings in class, to reflect changes.
		$this->settings = get_post_meta( $this->post_id, self::SETTINGS_NAME, true );

	}

}
