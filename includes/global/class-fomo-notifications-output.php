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

		// AJAX.
		add_action( 'wp_ajax_nopriv_fomo_notifications_get_notifications', array( $this, 'get_notifications' ) );
		add_action( 'wp_ajax_fomo_notifications_get_notifications', array( $this, 'get_notifications' ) );

		// Scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'display_notification' ) );

		// Conditions.
		add_filter( 'fomo_notifications_output_get_notifications_conditions_met', array( $this, 'conditions_met' ), 10, 2 );

	}

	/**
	 * Enqueue JS on the frontend site.
	 *
	 * @since   1.0.0
	 */
	public function enqueue_scripts() {

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
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'fomo_notifications_get_notifications',
				'nonce'   => wp_create_nonce( 'fomo_notifications_get_notifications' ),
			)
		);

	}

	/**
	 * Returns the block's output, based on the supplied configuration attributes,
	 * when requested via AJAX.
	 *
	 * @since   1.0.0
	 */
	public function get_notifications() {

		// Check nonce.
		check_ajax_referer( 'fomo_notifications_get_notifications', 'nonce' );

		// Define an array of items.
		$items = array();

		// Get all notifications.
		$notifications = new Fomo_Notifications_Notifications;
		$notifications_ids = $notifications->get_ids();

		// Bail if no notifications exist.
		if ( ! $notifications_ids ) {
			return false;
		}

		// Iterate through notifications.
		foreach ( $notifications_ids as $notification_id ) {
			// Get settings.
			$settings = new Fomo_Notifications_Notification_Settings( $notification_id );

			// Get source.
			$source = $settings->get_source();

			// Assume conditions are met for this notification to display.
			$conditions_met = true;

			// Run the notification through conditions.
			$conditions_met = apply_filters( 'fomo_notifications_output_get_notifications_conditions_met', $conditions_met, $settings );

			// Ignore this notification if the conditions for display were not met.
			if ( ! $conditions_met ) {
				continue;
			}

			// Run the notification through any additional conditions the source may specify.
			$conditions_met = apply_filters( 'fomo_notifications_output_get_notifications_conditions_met_' . $source, $conditions_met, $settings );

			// Ignore this notification if the conditions for display were not met.
			if ( ! $conditions_met ) {
				continue;
			}

			/**
			 * Define the items to output for this notification.
			 *
			 * @since   1.0.0
			 *
			 * @param   array   $items  Items to display in this notification.
			 */
			$items = apply_filters( 'fomo_notifications_output_get_notifications_items_' . $source, $items, $settings );
		}

		// If no items exist, bail.
		if ( ! count( $items ) ) {
			return false;
		}

		// Update the date of each notification to a relative time.
		foreach ( $items as $index => $item ) {
			$items[ $index ]['date'] = human_time_diff( $item['date'], time() ) . ' ' . __( 'ago', 'fomo-notifications' );
		}

		// Return.
		return wp_send_json_success( $items );

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
	 * Determines whether the given notification settings conditions are met.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	bool 										$conditions_met 	Conditions met.
	 * @param 	Fomo_Notifications_Notification_Settings 	$settings 			Notification settings.
	 * @return 	bool
	 */
	public function conditions_met( $conditions_met, $settings ) {

		// Visitor type.
		$conditions_met = $this->conditions_visitor_type( $settings->get_by_key( 'conditions_visitor_type' ) );
		if ( ! $conditions_met ) {
			return $conditions_met;
		}

		// Date and time.
		// @TODO.

		return $conditions_met;

	}

	/**
	 * Determines whether the request meets one or more of the specified visitor types.
	 * 
	 * @since 	1.0.0
	 * 
	 * @param 	array 	$visitor_types 	Visitor types.
	 * @return 	bool
	 */
	private function conditions_visitor_type( $visitor_type ) {

		switch ( $visitor_type ) {
			/**
			 * Logged in
			 */
			case 'logged_in':
				return is_user_logged_in();

			/**
			 * Logged out
			 */
			case 'logged_out':
				return ! is_user_logged_in();

			/**
			 * All visitors
			 */
			case '':
				return true;

			/**
			 * Role
			 */
			default:
				if ( ! is_user_logged_in() ) {
					return false;
				}

				// Get user.
				$user = wp_get_current_user();
				foreach ( $user->roles as $role ) {
					if ( $visitor_type !== $role ) {
						return true;
					}
				}
				
				// If here, the user does not have the required role
				// to view this notification.
				return false;
		}

	}

}
