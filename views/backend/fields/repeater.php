<?php
/**
 * Outputs a repeater field.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

?>
<div class="wpzinc-option">
	<div class="full">
		<table class="widefat">
			<thead>
				<tr>
					<?php
					foreach ( $field['sub_fields'] as $sub_field_name => $sub_field ) {
						?>
						<th><?php echo esc_html( $sub_field['label'] ); ?></th>
						<?php
					}
					?>
					<th><?php esc_html_e( 'Actions', 'fomo-notifications' ); ?></th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<td colspan="<?php echo esc_attr( count( $field['sub_fields'] ) + 1 ); ?>">
						<button class="wpzinc-add-table-row button" data-table-row-selector="repeater-row">
							<?php esc_html_e( 'Add', 'fomo-notifications' ); ?>
						</button>
					</td>
				</tr>
			</tfoot>

			<tbody id="<?php echo esc_attr( $shortcode['name'] ); ?>-<?php echo esc_attr( $field_name ); ?>">
				<tr id="<?php echo esc_attr( $shortcode['name'] ); ?>-<?php echo esc_attr( $field_name ); ?>-row" class="repeater-row hidden">
					<?php
					$sub_fields = $field['sub_fields'];
					foreach ( $sub_fields as $field_name => $field ) {
						?>
						<td>
							<?php include 'field.php'; ?>
						</td>
						<?php
					}
					?>
					<td>
						<a href="#" class="wpzinc-delete-table-row">
							<?php esc_html_e( 'Delete', 'fomo-notifications' ); ?>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
