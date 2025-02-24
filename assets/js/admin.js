/**
 * Admin JS
 *
 * @since   1.0.0
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

jQuery( document ).ready(
	function ( $ ) {
		/**
		 * Conditional options
		 */
		$( 'select[name="_fomo_notification_settings[source]"]' ).on(
			'change.fomo-notifications',
			function () {

				var source = $( this ).val();

				// Hide all conditional options.
				$( '.wpzinc-option.conditional' ).hide();

				// Show options for this source.
				$( '.wpzinc-option.conditional.' + source ).show();

			}
		);
		$( 'select[name="_fomo_notification_settings[source]"]' ).trigger( 'change.fomo-notifications' );

	}
);