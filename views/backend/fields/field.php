<?php
/**
 * Outputs a setting field.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

// Build a string of data- attributes.
$data_attributes                   = '';
$data_attributes_shortcode_defined = false;
if ( isset( $field['data'] ) ) {
	foreach ( $field['data'] as $data_attribute => $data_attribute_value ) {
		$data_attributes .= ' data-' . $data_attribute . '="' . $data_attribute_value . '"';

		if ( $data_attribute === 'shortcode' ) {
			$data_attributes_shortcode_defined = true;
		}
	}
}
if ( ! $data_attributes_shortcode_defined ) {
	$data_attributes .= ' data-shortcode="' . $field_name . '"';
}

switch ( $field['type'] ) {
	/**
	 * Text
	 */
	case 'text':
		?>
		<input type="text" 
				id="<?php echo esc_attr( $field_name ); ?>"
				name="<?php echo esc_attr( $settings::SETTINGS_NAME ); ?>[<?php echo esc_attr( $field_name ); ?>]"
				value="<?php echo esc_attr( isset( $field['value'] ) ? $field['value'] : '' ); ?>" 
				<?php echo $data_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				placeholder="<?php echo esc_attr( ( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ) ); ?>"
				class="widefat <?php echo esc_attr( ( isset( $field['class'] ) ? $field['class'] : '' ) ); ?>" />
		<?php
		break;

	/**
	 * Textarea
	 */
	case 'textarea':
		?>
		<textarea 
				id="<?php echo esc_attr( $field_name ); ?>"
				name="<?php echo esc_attr( $settings::SETTINGS_NAME ); ?>[<?php echo esc_attr( $field_name ); ?>]"
				<?php echo $data_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				placeholder="<?php echo esc_attr( ( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ) ); ?>"
				class="widefat <?php echo esc_attr( ( isset( $field['class'] ) ? $field['class'] : '' ) ); ?>"><?php echo esc_attr( isset( $field['value'] ) ? $field['value'] : '' ); ?></textarea>
		<?php
		break;

	/**
	 * Number
	 */
	case 'number':
		?>
		<input type="number" 
				id="<?php echo esc_attr( $field_name ); ?>"
				name="<?php echo esc_attr( $settings::SETTINGS_NAME ); ?>[<?php echo esc_attr( $field_name ); ?>]" 
				value="<?php echo esc_attr( isset( $field['value'] ) ? $field['value'] : '' ); ?>" 
				<?php echo $data_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				min="<?php echo esc_attr( $field['min'] ); ?>" 
				max="<?php echo esc_attr( $field['max'] ); ?>" 
				step="<?php echo esc_attr( $field['step'] ); ?>"
				class="widefat <?php echo esc_attr( ( isset( $field['class'] ) ? $field['class'] : '' ) ); ?>" />
		<?php
		break;

	/**
	 * Select
	 */
	case 'select':
		?>
		<select name="<?php echo esc_attr( $settings::SETTINGS_NAME ); ?>[<?php echo esc_attr( $field_name ); ?>]"
				id="<?php echo esc_attr( $field_name ); ?>"
				<?php echo $data_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				size="1"
				class="widefat <?php echo esc_attr( ( isset( $field['class'] ) ? $field['class'] : '' ) ); ?>">
			<?php
			$field['value'] = ( isset( $field['value'] ) ? $field['value'] : '' );
			foreach ( $field['options'] as $value => $label ) {
				?>
				<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $field['value'], $value ); ?>>
					<?php echo esc_attr( $label ); ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
		break;

	/**
	 * Multiple Select
	 */
	case 'select_multiple':
		?>
		<select name="<?php echo esc_attr( $settings::SETTINGS_NAME ); ?>[<?php echo esc_attr( $field_name ); ?>][]"
				id="<?php echo esc_attr( $field_name ); ?>"
				<?php echo $data_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				size="10"
				multiple="multiple"
				class="widefat <?php echo esc_attr( ( isset( $field['class'] ) ? $field['class'] : '' ) ); ?>">
			<?php
			$field['value'] = ( isset( $field['value'] ) ? $field['value'] : '' );
			if ( isset( $field['options'] ) && is_array( $field['options'] ) && count( $field['options'] ) > 0 ) {
				foreach ( $field['options'] as $value => $label ) {
					?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php echo esc_attr( in_array( $value, (array) $field['value'], true ) ? ' selected' : '' ); ?>>
						<?php echo esc_attr( $label ); ?>
					</option>
					<?php
				}
			}
			?>
		</select>
		<?php
		break;

	/**
	 * Toggle
	 */
	case 'toggle':
		?>
		<select name="<?php echo esc_attr( $settings::SETTINGS_NAME ); ?>[<?php echo esc_attr( $field_name ); ?>]"
				id="<?php echo esc_attr( $field_name ); ?>"
				<?php echo $data_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				size="1"
				class="widefat <?php echo esc_attr( ( isset( $field['class'] ) ? $field['class'] : '' ) ); ?>">
			<?php
			$field['value'] = ( isset( $field['value'] ) ? $field['value'] : '' );
			?>
			<option value="0"<?php selected( $field['value'], 0 ); ?>><?php esc_html_e( 'No', 'fomo-notifications' ); ?></option>
			<option value="1"<?php selected( $field['value'], 1 ); ?>><?php esc_html_e( 'Yes', 'fomo-notifications' ); ?></option>
		</select>
		<?php
		break;
}

if ( isset( $field['description'] ) ) {
	?>
	<p class="description">
		<?php echo $field['description']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</p>
	<?php
}
