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

	use Fomo_Notifications_Source_Trait;

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Bail if WooCommerce isn't active.
		if ( ! function_exists( 'wc_get_order_statuses' ) ) {
			return;
		}

		// Define source name, label, description and fields.
		$this->name        = 'woocommerce';
		$this->label       = __( 'WooCommerce', 'fomo-notifications' );
		$this->description = __( 'Define criteria for including WooCommerce orders as notifications.', 'fomo-notifications' );
		$this->defaults    = array(
			$this->name . '_limit'        => 10,
			$this->name . '_order_age'    => 90,
			$this->name . '_order_status' => array( 'wc-completed' ),
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
				$this->name . '_limit'        => array(
					'label'       => __( 'Number of Notifications', 'fomo-notifications' ),
					'source'      => 'woocommerce',
					'type'        => 'number',
					'value'       => $settings->get_by_key( $this->name . '_limit' ),
					'min'         => 1,
					'max'         => 50,
					'step'        => 1,
					'description' => esc_html__( 'The maximum number of WooCommerce Order notifications to display.', 'fomo-notifications' ),
				),
				$this->name . '_order_age'    => array(
					'label'       => __( 'Maximum Order Age', 'fomo-notifications' ),
					'source'      => 'woocommerce',
					'type'        => 'number',
					'value'       => $settings->get_by_key( $this->name . '_order_age' ),
					'min'         => 1,
					'max'         => 9999,
					'step'        => 1,
					'unit'        => __( 'days', 'fomo-notifications' ),
					'description' => esc_html__( 'The maximum age of WooCommerce Orders to include.', 'fomo-notifications' ),
				),
				$this->name . '_order_status' => array(
					'label'       => __( 'Order Status', 'fomo-notifications' ),
					'source'      => 'woocommerce',
					'type'        => 'select_multiple',
					'value'       => $settings->get_by_key( $this->name . '_order_status' ),
					'options'     => wc_get_order_statuses(),
					'description' => esc_html__( 'Only include WooCommerce Orders with the defined statuses. Ctrl / Cmd + click statuses to include/exclude.', 'fomo-notifications' ),
				),

				// @TODO by product(s) etc.
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

		$product_purchased = array();

		// Get first product.
		foreach ( $order->get_items() as $item_key => $item ) {
			$product           = $item->get_product(); // @phpstan-ignore-line
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
