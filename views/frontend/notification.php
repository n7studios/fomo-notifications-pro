<?php
/**
 * Notification template for frontend output
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

?>
<div id="fomo-notification">
	<div id="fomo-notification-inner">
		<div id="fomo-notification-image">
			<img src="" alt="" />
		</div>
		<div id="fomo-notification-content">
			<p id="fomo-notification-text">
				<strong id="fomo-notification-name"></strong> 
				<?php esc_html_e( 'from', 'fomo-notifications' ); ?> <span id="fomo-notification-location"></span> 
				<span id="fomo-notification-action"></span> <a href="#" id="fomo-notification-title"></a>
			</p>
			<p id="fomo-notification-date"></p>
		</div>
	</div>
</div>
