<?php
/**
 * FOMO Notifications Export class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * Fetches configuration data from the Plugin, such as settings and notifications,
 * based on the user's selection, writing it to an export JSON / ZIP file.
 *
 * This export file can then be used on another installation.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class FOMO_Notifications_Admin_Export {

	/**
	 * Constructor
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		add_action( 'fomo_notifications_pro_export_view', array( $this, 'output_export_options' ) );
		add_filter( 'fomo_notifications_pro_export', array( $this, 'export' ), 10, 2 );

	}

	/**
	 * Outputs options on the Export screen to choose what data to include
	 * in the export
	 *
	 * @since   1.0.0
	 */
	public function output_export_options() {

		// Get Notifications.
		$notifications = new Fomo_Notifications_Notifications();
		$notifications = $notifications->get_all();

		// Load view.
		include_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'pro/views/admin/export.php';

	}

	/**
	 * Export data
	 *
	 * @since   1.0.0
	 *
	 * @param   array $data   Export Data.
	 * @param   array $params Export Parameters (define what data to export).
	 * @return  array           Export Data
	 */
	public function export( $data, $params ) {

		// Notifications.
		if ( isset( $params['notifications'] ) ) {
			$notifications    = new Fomo_Notifications_Notifications();
			$notification_ids = array_keys( $params['notifications'] );

			$data['notifications'] = array();
			foreach ( $notifications->get_all() as $notification_id => $notification ) {
				// Skip if not a Notification ID we're exporting.
				if ( ! in_array( $notification_id, $notification_ids, true ) ) {
					continue;
				}

				// Add notification to export data.
				$data['notifications'][ $notification_id ] = $notification;
			}
		}

		// Settings.
		/*
		if ( isset( $params['settings'] ) ) {
			$data['general']      = $this->base->get_class( 'settings' )->get_settings( $this->base->plugin->name . '-general' );
			$data['generate']     = $this->base->get_class( 'settings' )->get_settings( $this->base->plugin->name . '-generate' );
			$data['georocket']    = $this->base->get_class( 'settings' )->get_settings( $this->base->plugin->name . '-georocket' );
			$data['integrations'] = $this->base->get_class( 'settings' )->get_settings( $this->base->plugin->name . '-integrations' );
			$data['research']     = $this->base->get_class( 'settings' )->get_settings( $this->base->plugin->name . '-research' );
			$data['spintax']      = $this->base->get_class( 'settings' )->get_settings( $this->base->plugin->name . '-spintax' );
		}
		*/

		return $data;

	}

}
