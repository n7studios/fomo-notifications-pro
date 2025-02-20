<?php
/**
 * FOMO Notifications LUM integration class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * FOMO Notifications LUM integration class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Source_Lum {

	use Fomo_Notifications_Source_Trait;

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Define source name, label, description and fields.
		$this->name        = 'lum';
		$this->label       = __( 'License Update Manager', 'fomo-notifications' );
		$this->description = __( 'Define criteria for including License Update Manager orders as notifications.', 'fomo-notifications' );
		$this->defaults    = array(
			$this->name . '_limit'     => 10,
			$this->name . '_order_age' => 90,
		);

		// Register as a source.
		add_filter( 'fomo_notifications_admin_notification_ui_get_sources', array( $this, 'register_source' ) );

		// Register fields and defaults when adding/editing a notification for this source.
		add_filter( 'fomo_notifications_admin_notification_ui_get_conditions_fields', array( $this, 'register_conditions_fields' ), 10, 2 );
		add_filter( 'fomo_notifications_notification_settings_get_defaults', array( $this, 'get_defaults' ) );

		// Frontend.
		add_filter( 'fomo_notifications_output_get_notifications_' . $this->name, array( $this, 'get_notifications' ), 10, 2 );

	}

	/**
	 * Register conditions fields for this source.
	 *
	 * @since   1.0.0
	 *
	 * @param   array                                    $fields     Fields.
	 * @param   Fomo_Notifications_Notification_Settings $settings   Notification Settings instance.
	 * @return  array
	 */
	public function register_conditions_fields( $fields, $settings ) {

		return array_merge(
			$fields,
			array(
				$this->name . '_limit'     => array(
					'label'       => __( 'Number of Notifications', 'fomo-notifications' ),

					'type'        => 'number',
					'value'       => $settings->get_by_key( $this->name . '_limit' ),
					'min'         => 1,
					'max'         => 50,
					'step'        => 1,
					'description' => esc_html__( 'The maximum number of order notifications to display.', 'fomo-notifications' ),
				),
				$this->name . '_order_age' => array(
					'label'       => __( 'Maximum Order Age', 'fomo-notifications' ),

					'type'        => 'number',
					'value'       => $settings->get_by_key( $this->name . '_order_age' ),
					'min'         => 1,
					'max'         => 9999,
					'step'        => 1,
					'unit'        => __( 'days', 'fomo-notifications' ),
					'description' => esc_html__( 'The maximum age of orders to include.', 'fomo-notifications' ),
				),
			),
		);

	}

	/**
	 * Returns notifications to display for the given notification template,
	 * if this integration is defined as the source.
	 *
	 * @since   1.0.0
	 *
	 * @param   array                                    $notifications  Notifications data to display.
	 * @param   Fomo_Notifications_Notification_Settings $settings       Settings instance for this notification template.
	 * @return  array
	 */
	public function get_notifications( $notifications, $settings ) {

		return array();

		/*
		// Get Orders.
		$order_ids = $this->get_order_ids(
			$settings->get_by_key( $this->name . '_order_status' ),
			$settings->get_by_key( $this->name . '_order_age' ),
			$settings->get_by_key( $this->name . '_limit' )
		);

		// Bail if no Orders exist.
		if ( ! $order_ids ) {
			return array();
		}

		// Build notifications from Orders.
		foreach ( $order_ids as $order_id ) {
			// Get order.
			$order = wc_get_order( $order_id );

			// Get product purchased.
			// 2 or more products will result in the first retuned with ' & X products'.
			$product_purchased = $this->get_product_purchased( $order );

			// If product purchased is false, skip.
			if ( ! $product_purchased ) {
				continue;
			}

			// Build notification.
			$notifications[] = array(
				'image'    => $product_purchased['image'],
				'name'     => sprintf(
					'%s %s',
					$order->get_billing_first_name(),
					$order->get_billing_last_name()
				),
				'location' => sprintf(
					'%s, %s',
					$order->get_billing_city(),
					$order->get_billing_country()
				),
				'action'   => __( 'purchased', 'fomo-notifications' ),
				'title'    => $product_purchased['title'],
				'date'     => $order->get_date_created()->getTimestamp(),
				'url'      => $product_purchased['url'],
			);
		}

		// Return array of notifications.
		return $notifications;
		*/

	}

}
