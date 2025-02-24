<?php
/**
 * Outputs the export options at Import & Export > Export
 *
 * @package Page_Generator_Pro
 * @author WP Zinc
 */

?>
<div class="wpzinc-option">
	<div class="left">
		<label for="settings"><?php esc_html_e( 'Settings', 'fomo-notifications' ); ?></label>
	</div>
	<div class="right">
		<input type="checkbox" name="settings" id="settings" value="1" checked />
	</div>
</div>

<?php
// Notifications.
if ( isset( $notifications ) && is_array( $notifications ) ) {
	?>
	<div class="wpzinc-option">
		<div class="left">
			<label><?php esc_html_e( 'Notifications', 'fomo-notifications' ); ?></label><br />
			<a href="#" class="wpzinc-checkbox-toggle" data-target="content-group"><?php esc_html_e( 'Select / Deselect All', 'fomo-notifications' ); ?></a>
		</div>
		<div class="right">
			<div class="tax-selection">
				<div class="tabs-panel">
					<ul class="categorychecklist form-no-clear">				                    			
						<?php
						foreach ( $notifications as $notification_id => $notification ) {
							?>
							<li>
								<label class="selectit">
									<input type="checkbox" name="notifications[<?php echo esc_attr( $notification_id ); ?>]" value="1" class="notification" checked />
									<?php echo esc_html( $notification['title'] ); ?>      
								</label>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php
}
