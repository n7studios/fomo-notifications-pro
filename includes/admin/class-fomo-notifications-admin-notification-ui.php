<?php
/**
 * FOMO Notifications Post Type UI
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * Handles Notification Post Type's UI for creating
 * and editing Notifications.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Admin_Notification_UI {

	/**
	 * Holds the post type to store notifications in.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	private $post_type = 'fomo-notification';

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Scripts.
		add_filter( 'wpzinc_admin_body_class', array( $this, 'admin_body_class' ) ); // WordPress Admin.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Meta Boxes.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Save Group.
		add_action( 'save_post', array( $this, 'save_post' ) );

	}

	/**
	 * Registers screen names that should add the wpzinc class to the <body> tag
	 *
	 * @since   1.0.0
	 *
	 * @param   array $screens    Screen Names.
	 * @return  array               Screen Names
	 */
	public function admin_body_class( $screens ) {

		// Add Post Types.
		$screens[] = 'fomo-notification';

		// Return.
		return $screens;

	}

	/**
	 * Enqueues scripts for the notification add/edit UI.
	 *
	 * @since   1.0.0
	 */
	public function admin_scripts() {

		wp_enqueue_script( 'wpzinc-admin-tabs' );

	}

	/**
	 * Registers meta boxes
	 *
	 * @since   1.0.0
	 */
	public function add_meta_boxes() {

		add_meta_box(
			$this->post_type,
			__( 'Notification', 'fomo-notifications' ),
			array( $this, 'output_meta_box' ),
			$this->post_type,
			'normal',
			'high'
		);

	}

	/**
	 * Outputs the meta box.
	 *
	 * @since   1.0.0
	 *
	 * @param   WP_Post $post   Notification Post.
	 */
	public function output_meta_box( $post ) {

		// Load settings for this notification.
		$settings = new Fomo_Notifications_Notification_Settings( $post->ID );

		/**
		 * Define the available notification sources.
		 *
		 * @since   1.0.0
		 *
		 * @param   array   $sources    Sources.
		 */
		$sources = apply_filters( 'fomo_notifications_admin_notification_ui_get_sources', array() );

		// Define the source fields that must always display.
		$source_fields = array(
			'source' => array(
				'label'       => __( 'Source', 'fomo-notifications' ),
				'section'     => 'source',

				'type'        => 'select',
				'value'       => $settings->get_by_key( 'source' ),
				'options'     => $sources,
				'description' => esc_html__( 'The source to use for notifications data.', 'fomo-notifications' ),

			),
		);

		// Define the display fields that must always display.
		$display_fields = array();

		/**
		 * Define the available settings fields for the display section
		 *
		 * @since   1.0.0
		 *
		 * @param   array                                       $display_fields     Fields.
		 * @param   Fomo_Notifications_Notification_Settings    $settings           Settings instance for this notification.
		 * @param   int                                         $post_id            Notification ID.
		 */
		$display_fields = apply_filters( 'fomo_notifications_admin_notification_ui_get_display_fields', $display_fields, $settings, $post->ID );

		// Define the conditions fields that must always display.
		// @TODO Shared conditions i.e. logged in, logged out etc etc.
		$conditions_fields = array();

		/**
		 * Define the available settings fields for the conditions section
		 *
		 * @since   1.0.0
		 *
		 * @param   array                                       $conditions_fields      Fields.
		 * @param   Fomo_Notifications_Notification_Settings    $settings               Settings instance for this notification.
		 * @param   int                                         $post_id                Notification ID.
		 */
		$conditions_fields = apply_filters( 'fomo_notifications_admin_notification_ui_get_conditions_fields', $conditions_fields, $settings, $post->ID );

		// Load view.
		include FOMO_NOTIFICATIONS_PLUGIN_PATH . 'views/backend/meta-box.php';

		// Output nonce.
		wp_nonce_field( 'save_notification', $this->post_type . '_nonce' );

	}

	/**
	 * Called when a Notification is saved.
	 *
	 * @since   1.0.0
	 *
	 * @param   int $post_id    Post ID.
	 */
	public function save_post( $post_id ) {

		// Bail if this isn't a Notification that's being saved.
		if ( get_post_type( $post_id ) !== $this->post_type ) {
			return;
		}

		// Run security checks.
		// Missing nonce.
		if ( ! isset( $_POST[ $this->post_type . '_nonce' ] ) ) {
			return;
		}

		// Invalid nonce.
		if ( ! wp_verify_nonce( sanitize_key( $_POST[ $this->post_type . '_nonce' ] ), 'save_notification' ) ) {
			return;
		}

		// Load settings for this notification.
		$settings = new Fomo_Notifications_Notification_Settings( $post_id );

		// Bail if no settings posted.
		if ( ! isset( $_POST[ $settings::SETTINGS_NAME ] ) ) {
			return;
		}

		// Save settings.
		$settings->save( array_map( 'sanitize_text_field', wp_unslash( $_POST[ $settings::SETTINGS_NAME ] ) ) );

	}

}
