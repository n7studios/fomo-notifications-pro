<?php
/**
 * FOMO Notifications Pro LUM integration class.
 *
 * @package Fomo_Notifications_Pro
 * @author WP Zinc
 */

/**
 * FOMO Notifications Pro LUM integration class.
 *
 * @package Fomo_Notifications_Pro
 * @author WP Zinc
 */
class Fomo_Notifications_Source_LUM {

	use Fomo_Notifications_Admin_Section_Fields_Trait;
	use Fomo_Notifications_Source_Trait;

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Define source name, label and description.
		$this->source_name        = 'lum';
		$this->source_label       = __( 'License Update Manager', 'fomo-notifications' );
		$this->source_description = __( 'Define criteria for including License Update Manager orders as notifications.', 'fomo-notifications' );

		// Register integration as a source.
		add_filter( 'fomo_notifications_get_sources', array( $this, 'register_source' ) );

		// Register settings section and fields.
		add_filter( 'fomo_notifications_admin_section_general_sections', array( $this, 'register_settings_section' ) );
		add_filter( 'fomo_notifications_admin_section_general_register_fields', array( $this, 'register_fields' ), 10, 2 );

		// Define defaults.
		add_filter( 'fomo_notifications_settings_get_defaults', array( $this, 'get_defaults' ) );

		// Frontend.
		add_filter( 'fomo_notifications_output_get_notifications_' . $this->source_name, array( $this, 'get_notifications' ) );

	}

	/**
	 * Register settings fields for this source.
	 *
	 * @since   1.0.0
	 *
	 * @param   array                       $fields     Fields.
	 * @param   Fomo_Notifications_Settings $settings   Settings class.
	 * @return  array
	 */
	public function register_fields( $fields, $settings ) {

		return array_merge(
			$fields,
			array(
				$this->source_name . '_order_days'   => array(
					'title'   => __( 'Maximum Order Age', 'fomo-notifications' ),
					'section' => $this->source_name,
					'props'   => array(
						'type'        => 'number',
						'value'       => $settings->get_by_key( $this->source_name . '_order_age' ),
						'min'         => 1,
						'max'         => 9999,
						'step'        => 1,
						'unit'        => __( 'days', 'fomo-notifications' ),
						'description' => esc_html__( 'The maximum age of Orders to include.', 'fomo-notifications' ),
					),
				),
			)
		);

	}

	/**
	 * The default settings, used when the Plugin Settings haven't been saved
	 * e.g. on a new installation.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $defaults   Default Settings.
	 */
	public function get_defaults( $defaults ) {

		return array_merge(
			$defaults,
			array(
				$this->source_name . '_order_age'    => 90,
			)
		);

	}

	/**
	 * Returns notifications to display for this source.
	 *
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function get_notifications() {

		// @TODO.
		return array();

		// Define the class that reads/writes settings.
		$settings = new Fomo_Notifications_Settings();

		// Get Orders.
		$order_ids = $this->get_order_ids(
			$settings->get_by_key( $this->source_name . '_order_status' ),
			$settings->get_by_key( $this->source_name . '_order_age' ),
			$settings->limit()
		);

		// Bail if no Orders exist.
		if ( ! $order_ids ) {
			return array();
		}

		// Build notifications from Orders.
		$notifications = array();
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

	}

}
