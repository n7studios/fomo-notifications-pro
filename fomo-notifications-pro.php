<?php
/**
 * FOMO Notifications Pro WordPress Plugin
 *
 * @package FOMO_Notifications Pro
 * @author WP Zinc
 *
 * @wordpress-plugin
 * Plugin Name: FOMO Notifications Pro
 * Plugin URI: http://www.wpzinc.com/plugins/fomo-notifications-pro
 * Version: 1.0.0
 * Author: WP Zinc
 * Author URI: http://www.wpzinc.com
 * Description: Display recent sales notifications to create FOMO (Fear of Missing Out)
 * Text Domain: fomo-notifications
 */

// Bail if Plugin is already loaded.
if ( class_exists( 'Fomo_Notifications_Pro' ) ) {
	return;
}
if ( defined( 'FOMO_NOTIFICATIONS_PLUGIN_VERSION' ) ) {
	return;
}

// Define Plugin version and build date.
define( 'FOMO_NOTIFICATIONS_PLUGIN_VERSION', '1.0.0' );
define( 'FOMO_NOTIFICATIONS_PLUGIN_BUILD_DATE', '2025-02-06 18:00:00' );

// Define Plugin paths.
define( 'FOMO_NOTIFICATIONS_PLUGIN_FILE', plugin_basename( __FILE__ ) );
define( 'FOMO_NOTIFICATIONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FOMO_NOTIFICATIONS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Traits.
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/traits/trait-fomo-notifications-admin-section.php';
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/traits/trait-fomo-notifications-admin-section-fields.php';
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/traits/trait-fomo-notifications-settings.php';
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/traits/trait-fomo-notifications-source.php';

// Admin.
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/admin/class-fomo-notifications-admin-notification-ui.php';
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/admin/class-fomo-notifications-admin-section-general.php';
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/admin/class-fomo-notifications-admin-settings.php';

// Global.
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/global/class-fomo-notifications-notification-settings.php';
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/global/class-fomo-notifications-output.php';
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/global/class-fomo-notifications-post-type.php';
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/global/class-fomo-notifications-plugin-settings.php';

// Sources.
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'includes/sources/class-fomo-notifications-source-woocommerce.php';

// Pro.
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'pro/sources/class-fomo-notifications-source-lum.php';
require_once FOMO_NOTIFICATIONS_PLUGIN_PATH . 'pro/class-fomo-notifications-pro.php';

/**
 * Main function to return Plugin instance.
 *
 * @since   1.0.0
 */
function fomo_notifications_pro() {

	return Fomo_Notifications_Pro::get_instance();

}

// Finally, initialize the Plugin.
fomo_notifications_pro();
