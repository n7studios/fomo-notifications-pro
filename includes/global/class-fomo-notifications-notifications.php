<?php
/**
 * Notifications class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * Handles creating, editing and deleting Notifications.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Notifications {

	/**
	 * Returns all Notifications
	 * 
	 * @since 	1.0.0
	 */
	public function get_all() {

		// Get all notifications.
		$notifications = new WP_Query(
			array(
				'post_type'              => 'fomo-notification',
				'post_status'            => 'publish',
				'cache_results'          => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		// Bail if none exist.
		if ( count( $notifications->posts ) === 0 ) {
			return false;
		}

		// Build array.
		$notifications_arr = array();
		foreach ( $notifications->posts as $notification ) {
			// Get settings.
			$notification_settings = new Fomo_Notifications_Notification_Settings( $notification->ID );

			// Skip if an error occured.
			if ( is_wp_error( $notification_settings ) ) {
				continue;
			}

			$notifications_arr[ $notification->ID ] = array_merge(
				array(
					'title' => $notification->post_title,
				),
				$notification_settings->get()
			);
		}

		/**
		 * Filters the Notifications to return.
		 *
		 * @since   1.0.0
		 *
		 * @param   array       $notifications_arr Notifications.
		 * @param   WP_Query    $notifications     Notifications Query.
		 */
		$notifications_arr = apply_filters( 'fomo_notifications_notifications_get_all', $notifications_arr, $notifications );

		// Return filtered results.
		return $notifications_arr;


	}

}
