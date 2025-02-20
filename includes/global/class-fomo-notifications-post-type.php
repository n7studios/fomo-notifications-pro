<?php
/**
 * FOMO Notifications post type class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * FOMO Notifications post type class.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
class Fomo_Notifications_Post_Type {

	/**
	 * Holds the Post Type Name.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	public $post_type_name = 'fomo-notification';

	/**
	 * Constructor
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Register post types.
		add_action( 'init', array( $this, 'register_post_type' ), 9 );

	}

	/**
	 * Registers Post Type
	 *
	 * @since    1.0.0
	 */
	public function register_post_type() {

		// Define Post Type arguments.
		$args = array(
			'labels'              => array(
				'name'               => __( 'Notifications', 'fomo-notifications' ),
				'singular_name'      => __( 'Notification', 'fomo-notifications' ),
				'menu_name'          => __( 'Notifications', 'fomo-notifications' ),
				'add_new'            => __( 'Add New', 'fomo-notifications' ),
				'add_new_item'       => __( 'Add New Notification', 'fomo-notifications' ),
				'edit_item'          => __( 'Edit Notification', 'fomo-notifications' ),
				'new_item'           => __( 'New Notification', 'fomo-notifications' ),
				'view_item'          => __( 'View Notification', 'fomo-notifications' ),
				'search_items'       => __( 'Search Notifications', 'fomo-notifications' ),
				'not_found'          => __( 'No Notifications found', 'fomo-notifications' ),
				'not_found_in_trash' => __( 'No Notifications found in Trash', 'fomo-notifications' ),
				'parent_item_colon'  => '',
			),
			/* translators: Plugin Name */
			'description'         => __( 'FOMO Notifications', 'fomo-notifications' ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'menu_position'       => 9999,
			'menu_icon'           => 'dashicons-admin-network',
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'revisions' ),
			'has_archive'         => false,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false,
		);

		// Register Post Type.
		register_post_type( $this->post_type_name, $args );

	}

}
