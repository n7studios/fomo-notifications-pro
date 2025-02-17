<?php
/**
 * FOMO Notifications Woocommerce integration class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * FOMO Notifications Woocommerce integration class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Source_Woocommerce {

	use Fomo_Notifications_Admin_Section_Fields_Trait;
	use Fomo_Notifications_Source_Trait;

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Define source name, label and description.
		$this->source_name        = 'woocommerce';
		$this->source_label       = __( 'WooCommerce', 'fomo-notifications' );
		$this->source_description = __( 'Define criteria for including WooCommerce orders as notifications.', 'fomo-notifications' );

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
				$this->source_name . '_order_status' => array(
					'title'   => __( 'Order Status', 'fomo-notifications' ),
					'section' => $this->source_name,
					'props'   => array(
						'type'        => 'multiple_select',
						'value'       => $settings->get_by_key( $this->source_name . '_order_status' ),
						'options'     => wc_get_order_statuses(),
						'description' => esc_html__( 'Only include orders with the defined statuses.', 'fomo-notifications' ),
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
				$this->source_name . '_order_status' => array( 'wc-completed' ),
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

	/**
	 * Return the first product purchased in the WooCommerce Order,
	 * with its URL.
	 *
	 * @since   1.0.0
	 *
	 * @param   WC_Order $order  WooCommerce Order.
	 * @return  bool|array
	 */
	private function get_product_purchased( $order ) {

		// If no products exist, return false.
		if ( ! $order->get_item_count() ) {
			return false;
		}

		// Get first product.
		foreach ( $order->get_items() as $item_key => $item ) {
			$product           = $item->get_product();
			$product_purchased = array(
				'image' => ( $product ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) : false ),
				'title' => $item->get_name(),
				'url'   => ( $product ? get_permalink( $product->get_id() ) : false ),
			);
			break;
		}

		// If additional products exist in the Order, append the title.
		if ( $order->get_item_count() > 1 ) {
			$product_purchased['title'] .= sprintf(
				/* translators: Number of products */
				_n(
					' and %s additional product',
					' and %s additional products',
					( $order->get_item_count() - 1 ),
					'fomo-notifications'
				),
				number_format_i18n( ( $order->get_item_count() - 1 ) )
			);
		}

		return $product_purchased;

	}

	/**
	 * Return WooCommerce Order IDs for the given statuses and limit.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $order_statuses     Order Statuses.
	 * @param   int   $maximum_order_age  Max. order age (days).
	 * @param   int   $limit              Limit.
	 * @return  bool|array
	 */
	private function get_order_ids( $order_statuses = array(), $maximum_order_age = 90, $limit = 10 ) {

		// Get Orders.
		$query = new WC_Order_Query(
			array(
				// Return posts of type `shop_order`.
				'type'         => 'shop_order',

				// Limit the orders and date range.
				'limit'        => (int) $limit,
				'date_created' => '>' . ( time() - ( (int) $maximum_order_age * DAY_IN_SECONDS ) ),

				// Only include Orders that match the supplied statuses.
				'status'       => $order_statuses,

				// Only return Order IDs.
				'return'       => 'ids',
			)
		);

		// If no Orders exist, return false.
		if ( empty( $query->get_orders() ) ) {
			return false;
		}

		// Return the array of Order IDs.
		return $query->get_orders();

	}

}
