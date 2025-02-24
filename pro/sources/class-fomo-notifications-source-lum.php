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

		// Bail if LUM isn't active.
		if ( ! class_exists( 'LUM_Product' ) ) {
			return;
		}

		// Define source name, label, description and fields.
		$this->name        = 'lum';
		$this->label       = __( 'License Update Manager', 'fomo-notifications' );
		$this->description = __( 'Define criteria for including License Update Manager orders as notifications.', 'fomo-notifications' );
		$this->defaults    = array(
			$this->name . '_limit'     => 10,
			$this->name . '_order_age' => 90,
			$this->name . '_products'  => array(),
		);

		// Register as a source.
		add_filter( 'fomo_notifications_admin_notification_ui_get_sources', array( $this, 'register_source' ) );

		// Register fields and defaults when adding/editing a notification for this source.
		add_filter( 'fomo_notifications_admin_notification_ui_get_display_fields', array( $this, 'register_display_fields' ), 10, 2 );
		add_filter( 'fomo_notifications_notification_settings_get_defaults', array( $this, 'get_defaults' ) );

		// Frontend.
		add_filter( 'fomo_notifications_output_get_notifications_items_' . $this->name, array( $this, 'get_notifications' ), 10, 2 );

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
	public function register_display_fields( $fields, $settings ) {

		return array_merge(
			$fields,
			array(
				$this->name . '_limit'     => array(
					'label'       => __( 'Number of Notifications', 'fomo-notifications' ),
					'source'      => 'lum',
					'type'        => 'number',
					'value'       => $settings->get_by_key( $this->name . '_limit' ),
					'min'         => 1,
					'max'         => 50,
					'step'        => 1,
					'description' => esc_html__( 'The maximum number of order notifications to display.', 'fomo-notifications' ),
				),
				$this->name . '_order_age' => array(
					'label'       => __( 'Maximum Order Age', 'fomo-notifications' ),
					'source'      => 'lum',
					'type'        => 'number',
					'value'       => $settings->get_by_key( $this->name . '_order_age' ),
					'min'         => 1,
					'max'         => 9999,
					'step'        => 1,
					'unit'        => __( 'days', 'fomo-notifications' ),
					'description' => esc_html__( 'The maximum age of orders to include.', 'fomo-notifications' ),
				),
				$this->name . '_products'     => array(
					'label'       => __( 'Products', 'fomo-notifications' ),
					'source'      => 'lum',
					'type'        => 'select_multiple',
					'value'       => $settings->get_by_key( $this->name . '_products' ),
					'options'     => $this->get_products(),
					'description' => esc_html__( 'The products to display. If no products are selected, all orders from products will be included.', 'fomo-notifications' ),
				),
			),
		);

	}

	private function get_products() {

		$products = array();
		foreach( LUM_Product::get_all() as $product ) {
			$products[ $product->ID ] = $product->post_title;
		}

		return $products;

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

		// Get licenses by custom field key and value.
		$licenses = new WP_Query(
			array(
				'post_type'      => LUM_License::$name,
				'post_status'    => 'publish',
				'posts_per_page' => $settings->get_by_key( $this->name . '_limit' ),
			)
		);

		// If no orders found, return false.
		if ( ! $licenses->have_posts() ) {
			return false;
		}

		// Build notifications from Licenses.
		foreach ( $licenses->posts as $license ) {
			// Get License Type.
			$license_type = get_field( 'license_type', $license->ID );

			// Get payment.
			$payments = LUM_Payment::get_by_license_id( $license->ID );

			// Get product.
			$products = get_field( 'products', $license_type->ID );

			// If license type or payments are false, skip.
			if ( ! $license_type ) {
				continue;
			}
			if ( ! $payments ) {
				continue;
			}
			if ( ! $products ) {
				continue;
			}

			// Pluck first payment.
			$payment = reset( $payments );

			// Build notification.
			$notifications[] = array(
				'image'    => '',
				'name'     => $payment->attributes['business_name'],
				'location' => sprintf(
					'%s, %s',
					$payment->attributes['city'],
					$payment->attributes['country_name']
				),
				'action'   => __( 'purchased', 'fomo-notifications' ),
				'title'    => $license_type->post_title,
				'date'     => strtotime( $license->post_date ),
				'url'      => get_permalink( $products[0]->ID ) . '#pricing',
			);
		}

		// Return array of notifications.
		return $notifications;

	}

}
