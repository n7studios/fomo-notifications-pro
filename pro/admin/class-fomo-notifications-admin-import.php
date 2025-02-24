<?php
/**
 * FOMO Notifications Import class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * Imports configuration data from a JSON or zipped JSON file that was created
 * by this Plugin's export functionality, storing the data in the Plugin's settings
 * and notifications.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class FOMO_Notifications_Admin_Import {

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		add_filter( 'fomo_notifications_pro_import', array( $this, 'import' ), 10, 2 );

	}

	/**
	 * Import data created by this Plugin's export functionality
	 *
	 * @since   1.0.0
	 *
	 * @param   bool  $success    Success.
	 * @param   array $import     Settings.
	 * @return  WP_Error|bool
	 */
	public function import( $success, $import ) {

		// Fetch data.
		$data = $import['data'];

		// Notifications.
		if ( isset( $data['notifications'] ) && is_array( $data['notifications'] ) ) {
			$this->import_notifications( $data['notifications'] );
		}

		// Settings.
		$this->import_settings( $data );

		// Return.
		return $success;

	}

	/**
	 * Imports the given Notifications into WordPress
	 *
	 * @since   1.0.0
	 *
	 * @param   array $notifications     Notifications from Plugin's JSON File.
	 */
	private function import_notifications( $notifications ) {

		foreach ( $notifications as $notification_id => $settings ) {
			// Build args.
			$args = array(
				'post_type'    => 'fomo-notification',
				'post_status'  => 'publish',
				'post_title'   => $settings['title'],
			);

			// Create group.
			$imported_notification_id = wp_insert_post( $args, true );

			// Skip if something went wrong.
			if ( is_wp_error( $imported_notification_id ) ) {
				continue;
			}

			// Remove title from settings.
			unset( $settings['title'] );

			// Save group settings.
			$notification = new Fomo_Notifications_Notification_Settings( $imported_notification_id );
			$notification->save( $settings );
		}

	}

	/**
	 * Imports the given Plugin Settings into WordPress
	 *
	 * @since   1.0.0
	 *
	 * @param   array $data     Settings from Plugin's JSON File.
	 */
	private function import_settings( $data ) {

		/*
		// Settings: General.
		if ( isset( $data['general'] ) ) {
			$this->base->get_class( 'settings' )->update_settings( $this->base->plugin->name . '-general', $data['general'] );
		}

		// Settings: Generate.
		if ( isset( $data['generate'] ) ) {
			$this->base->get_class( 'settings' )->update_settings( $this->base->plugin->name . '-generate', $data['generate'] );
		}

		// Settings: Georocket.
		if ( isset( $data['georocket'] ) ) {
			$this->base->get_class( 'settings' )->update_settings( $this->base->plugin->name . '-georocket', $data['georocket'] );
		}

		// Settings: Integrations.
		if ( isset( $data['integrations'] ) ) {
			$this->base->get_class( 'settings' )->update_settings( $this->base->plugin->name . '-integrations', $data['integrations'] );
		}

		// Settings: Research.
		if ( isset( $data['research'] ) ) {
			$this->base->get_class( 'settings' )->update_settings( $this->base->plugin->name . '-research', $data['research'] );
		}

		// Settings: Spintax.
		if ( isset( $data['spintax'] ) ) {
			$this->base->get_class( 'settings' )->update_settings( $this->base->plugin->name . '-spintax', $data['spintax'] );
		}
		*/

	}

}
