<?php
/**
 * View to output a setting field row.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

// Output Field.
if ( $field['type'] === 'repeater' ) {
	include 'repeater.php';
} else {
	?>
	<div class="wpzinc-option <?php echo ( isset( $field['source'] ) ? esc_attr( $field['source'] ) : '' ); ?>">
		<div class="left">
			<label for="<?php echo esc_attr( $field_name ); ?>">
				<?php echo esc_html( $field['label'] ); ?>
			</label>
		</div>
		<div class="right">
			<?php
			include 'field.php';
			?>
		</div>
	</div>
	<?php
}
