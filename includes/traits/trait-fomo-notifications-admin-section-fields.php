<?php
/**
 * FOMO Notifications settings section fields trait.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

/**
 * FOMO Notifications settings section fields trait.
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */
trait Fomo_Notifications_Admin_Section_Fields_Trait {

	/**
	 * Outputs a text field.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $args   Field arguments.
	 */
	public function text_field_callback( $args ) {

		$html = sprintf(
			'<input type="text" class="regular-text" id="%s" name="%s[%s]" value="%s" />',
			esc_attr( $args['name'] ),
			esc_attr( $this->settings_key ),
			esc_attr( $args['name'] ),
			esc_attr( $args['value'] )
		);

		echo $html . $this->get_description( $args['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Outputs a number field.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $args   Field arguments.
	 */
	public function number_field_callback( $args ) {

		$html = sprintf(
			'<input type="number" class="small-text" id="%s" name="%s[%s]" value="%s" min="%s" max="%s" step="%s" />',
			esc_attr( $args['name'] ),
			esc_attr( $this->settings_key ),
			esc_attr( $args['name'] ),
			esc_attr( $args['value'] ),
			$args['min'],
			$args['max'],
			$args['step']
		);

		echo $html . $this->get_description( $args['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Outputs a textarea field.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $args   Field arguments.
	 */
	public function textarea_field_callback( $args ) {

		$html = sprintf(
			'<textarea class="regular-text" id="%s" name="%s[%s]">%s</textarea>',
			esc_attr( $args['name'] ),
			esc_attr( $this->settings_key ),
			esc_attr( $args['name'] ),
			esc_attr( $args['value'] )
		);

		echo $html . $this->get_description( $args['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Outputs a date field.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $args   Field arguments.
	 */
	public function date_field_callback( $args ) {

		$html = sprintf(
			'<input type="date" class="regular-text" id="%s" name="%s[%s]" value="%s" />',
			esc_attr( $args['name'] ),
			esc_attr( $this->settings_key ),
			esc_attr( $args['name'] ),
			esc_attr( $args['value'] )
		);

		echo $html . $this->get_description( $args['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Outputs a select field.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $args   Field arguments.
	 */
	public function select_field_callback( $args ) {

		// Build opening <select> tag.
		$html = sprintf(
			'<select id="%s" name="%s[%s]" size="1">',
			esc_attr( $args['name'] ),
			esc_attr( $this->settings_key ),
			esc_attr( $args['name'] )
		);

		// Build <option> tags.
		foreach ( $args['options'] as $option => $label ) {
			$html .= sprintf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $option ),
				selected( $args['value'], $option, false ),
				esc_attr( $label )
			);
		}

		// Close <select>.
		$html .= '</select>';

		echo $html . $this->get_description( $args['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Outputs a multiple select field.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $args   Field arguments.
	 */
	public function select_multiple_field_callback( $args ) {

		// Build opening <select> tag.
		$html = sprintf(
			'<select id="%s" name="%s[%s][]" size="%s" multiple>',
			esc_attr( $args['name'] ),
			esc_attr( $this->settings_key ),
			esc_attr( $args['name'] ),
			esc_attr( count( $args['options'] ) )
		);

		// Build <option> tags.
		foreach ( $args['options'] as $option => $label ) {
			$html .= sprintf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $option ),
				( in_array( $option, $args['value'], true ) ? ' selected' : '' ),
				esc_attr( $label )
			);
		}

		// Close <select>.
		$html .= '</select>';

		echo $html . $this->get_description( $args['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Outputs a checkbox field.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $args   Field arguments.
	 */
	public function checkbox_field_callback( $args ) {

		$html = sprintf(
			'<input type="checkbox" id="%s" name="%s[%s]" value="1"%s />',
			esc_attr( $args['name'] ),
			esc_attr( $this->settings_key ),
			esc_attr( $args['name'] ),
			esc_attr( $args['value'] ? ' checked' : '' )
		);

		echo $html . $this->get_description( $args['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	/**
	 * Returns the given text wrapped in a paragraph with the description class.
	 *
	 * @since   1.0.0
	 *
	 * @param   bool|string|array $description    Description.
	 * @return  string                            HTML Description
	 */
	public function get_description( $description ) {

		// Return blank string if no description specified.
		if ( ! $description ) {
			return '';
		}

		// Return description in paragraph.
		return '<p class="description">' . esc_html( $description ) . '</p>';

	}

}
