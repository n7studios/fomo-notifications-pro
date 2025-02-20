<?php
/**
 * FOMO Notifications output class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * FOMO Notifications output class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Output {

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'display_notification' ) );

	}

	/**
	 * Enqueue JS on the frontend site.
	 *
	 * @since   1.0.0
	 */
	public function enqueue_scripts() {

		// @TODO Detect if home page, tax page etc etc etc.
		global $wp, $post;

		// Get notifications.
		$notifications = $this->get_notifications();

		// If no notifications exist, don't enqueue any CSS or JS.
		if ( ! count( $notifications ) ) {
			return;
		}

		// Enqueue CSS and JS.
		wp_enqueue_style(
			'fomo-notifications-style',
			FOMO_NOTIFICATIONS_PLUGIN_URL . 'assets/css/notification.css',
			array(),
			FOMO_NOTIFICATIONS_PLUGIN_VERSION
		);
		wp_enqueue_script(
			'fomo-notifications-script',
			FOMO_NOTIFICATIONS_PLUGIN_URL . 'assets/js/notification.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);

		// Localize JS with notifications data.
		wp_localize_script(
			'fomo-notifications-script',
			'fomo_notifications',
			array(
				'notifications' => $notifications,
			)
		);

	}

	/**
	 * Outputs the notifications HTML.
	 *
	 * @since   1.0.0
	 */
	public function display_notification() {

		include_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'views/frontend/notification.php';

	}

	/**
	 * Returns notifications for the source defined in the Plugin's settings.
	 *
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	private function get_notifications() {

		// Iterate through all published notifications.
		$all_possible_notification_ids = new WP_Query(
			array(
				'post_type'              => 'fomo-notification',
				'post_status'            => 'publish',

				// For performance, just return the Post ID and don't update meta or term caches.
				'fields'                 => 'ids',
				'cache_results'          => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		if ( empty( $all_possible_notification_ids->posts ) ) {
			return array();
		}

		// Iterate through notifications.
		$notifications = array();
		foreach ( $all_possible_notification_ids->posts as $notification_id ) {
			// Get settings.
			$settings = new Fomo_Notifications_Notification_Settings( $notification_id );

			// Get source.
			$source = $settings->get_source();

			/**
			 * Define the notifications to output from this template
			 * for its source.
			 *
			 * @since   1.0.0
			 *
			 * @param   array   $notifications  Notifications.
			 */
			$notifications = apply_filters( 'fomo_notifications_output_get_notifications_' . $source, $notifications, $settings );
		}

		// Update the date of each notification to a relative time.
		foreach ( $notifications as $index => $notification ) {
			$notifications[ $index ]['date'] = human_time_diff( $notification['date'], time() ) . ' ' . __( 'ago', 'fomo-notifications' );
		}

		// Return.
		return $notifications;

	}

}
