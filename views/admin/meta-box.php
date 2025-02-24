<?php
/**
 * Notification meta box output when adding / editing
 * a notification
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

?>

<div class="wpzinc-vertical-tabbed-ui no-border">
	<!-- Tabs -->
	<ul class="wpzinc-nav-tabs wpzinc-js-tabs" data-panels-container="#notification-container" data-panel=".notification-panel" data-active="wpzinc-nav-tab-vertical-active">
		<li class="wpzinc-nav-tab">
			<a href="#notification-source" class="wpzinc-nav-tab-vertical-active">
				<?php esc_html_e( 'Source', 'fomo-notifications' ); ?>
			</a>
		</li>
		<li class="wpzinc-nav-tab">
			<a href="#notification-display">
				<?php esc_html_e( 'Display', 'fomo-notifications' ); ?>
			</a>
		</li>
		<li class="wpzinc-nav-tab">
			<a href="#notification-conditions">
				<?php esc_html_e( 'Conditions', 'fomo-notifications' ); ?>
			</a>
		</li>
	</ul>

	<!-- Sections -->
	<div id="notification-container" class="wpzinc-nav-tabs-content no-padding">
		<div id="notification-source" class="notification-panel">
			<div class="postbox">
				<header>
					<h3><?php esc_html_e( 'Source', 'fomo-notifications' ); ?></h3>
					<p class="description">
						<?php esc_html_e( 'The source to use for notifications data.', 'fomo-notifications' ); ?>
					</p>
				</header>

				<?php
				// Iterate through fields, outputting those that belong to this section.
				foreach ( $source_fields  as $field_name => $field ) {
					include 'fields/row.php';
				}
				?>
			</div>
		</div>

		<div id="notification-display" class="notification-panel">
			<div class="postbox">
				<header>
					<h3><?php esc_html_e( 'Display Settings', 'fomo-notifications' ); ?></h3>
					<p class="description">
						<?php esc_html_e( 'Controls what to display for this notification.', 'fomo-notifications' ); ?>
					</p>
				</header>

				<?php
				// Iterate through fields, outputting those that belong to this section.
				foreach ( $display_fields  as $field_name => $field ) {
					include 'fields/row.php';
				}
				?>
			</div>
		</div>

		<div id="notification-conditions" class="notification-panel">
			<div class="postbox">
				<header>
					<h3><?php esc_html_e( 'Conditions', 'fomo-notifications' ); ?></h3>
					<p class="description">
						<?php esc_html_e( 'Controls where and when the notification should display.', 'fomo-notifications' ); ?>
					</p>
				</header>

				<?php
				// Iterate through fields, outputting those that belong to this section.
				foreach ( $conditions_fields  as $field_name => $field ) {
					include 'fields/row.php';
				}
				?>
			</div>
		</div>
	</div>
</div>