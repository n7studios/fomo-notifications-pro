/**
 * Frontend functionality for FOMO notifications
 *
 * @since   1.0.0
 *
 * @package Fomo_Notifications
 * @author WP Zinc
 */

// Initialize variables.
let fomoNotificationCurrentIndex = 0;
let fomoNotificationTimeout;
let fomoNotificationNotifications;

/**
 * Update the notification data with the next data in the array,
 * and show it.
 *
 * @since 	1.0.0
 */
function fomoNotificationsShow() {

	// Get next notification.
	fomoNotificationsPopulate( fomoNotificationNotifications[fomoNotificationCurrentIndex] );

	// Show the notification.
	document.getElementById( 'fomo-notification' ).classList.add( 'show' );

	// Hide the notification after 10 seconds.
	fomoNotificationTimeout = setTimeout(
		function () {
			fomoNotificationsHide();
		},
		10000
	);

}

/**
 * Hide the notification, and increment the index to the next or first
 * notification in the loop.
 *
 * @since 	1.0.0
 */
function fomoNotificationsHide() {
	// Hide the notification.
	document.getElementById( 'fomo-notification' ).classList.remove( 'show' );

	// Wait for transition to complete before showing next.
	setTimeout(
		function () {

			// Update index for next notification, or loop back to the start if we reach the end.
			fomoNotificationCurrentIndex = (fomoNotificationCurrentIndex + 1) % fomoNotificationNotifications.length;

			// Show next notification after a delay.
			setTimeout( fomoNotificationsShow, 2000 );

		},
		300
	);
}

/**
 * Populates the notification element with the next notification.
 *
 * @since 	1.0.0
 */
function fomoNotificationsPopulate(notification) {
	let fomoNotificationElement = document.getElementById( 'fomo-notification' );
	if ( notification.image ) {
		fomoNotificationElement.querySelector( '#fomo-notification-image img' ).src = notification.image;
		fomoNotificationElement.querySelector( '#fomo-notification-image' ).classList.add( 'show' );
	} else {
		fomoNotificationElement.querySelector( '#fomo-notification-image img' ).src = '';
		fomoNotificationElement.querySelector( '#fomo-notification-image' ).classList.remove( 'show' );
	}
	fomoNotificationElement.querySelector( '#fomo-notification-name' ).textContent     = notification.name;
	fomoNotificationElement.querySelector( '#fomo-notification-location' ).textContent = notification.location;
	fomoNotificationElement.querySelector( '#fomo-notification-action' ).textContent   = notification.action;
	fomoNotificationElement.querySelector( '#fomo-notification-title' ).textContent    = notification.title;
	fomoNotificationElement.querySelector( '#fomo-notification-title' ).href           = notification.url ? notification.url : '#';
	fomoNotificationElement.querySelector( '#fomo-notification-date' ).textContent     = notification.date;
}

document.addEventListener(
	'DOMContentLoaded',
	function () {

		// Fetch notifications.
		fetch(
			fomo_notifications.ajax_url,
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: new URLSearchParams({
    				action: fomo_notifications.action,
					nonce: fomo_notifications.nonce
				}).toString(),
			}
		)
		.then(
			function ( response ) {
				return response.json();
			}
		)
		.then(
			function ( result ) {
				fomoNotificationNotifications = result.data;
				setTimeout( fomoNotificationsShow, 1000 );
			}
		)
		.catch(
			function ( error ) {
				console.log( error );
			}
		);

	}
);
