<?php
/**
 * Outputs the licensing screen.
 *
 * @package LicensingUpdateManager
 * @author WP Zinc
 */

?>
<header>
	<h1>
		<?php esc_html_e( 'Licensing', $this->plugin->name ); // phpcs:ignore WordPress.WP.I18n ?>
	</h1>
</header>

<hr class="wp-header-end" />

<div class="wrap">
	<?php
	// Notices.
	if ( isset( $this->message ) ) {
		?>
		<div class="updated notice"><p><?php echo $this->message; // phpcs:ignore WordPress.Security.EscapeOutput ?></p></div>  
		<?php
	}
	if ( isset( $this->errorMessage ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		?>
		<div class="error notice"><p><?php echo $this->errorMessage; //phpcs:ignore WordPress.Security.EscapeOutput,WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase ?></p></div>  
		<?php
	}
	?>

	<div class="wrap-inner">
		<div id="poststuff">
			<?php require_once 'licensing-inline.php'; ?>
		</div>
	</div><!-- /.wrap-inner -->
</div><!-- /.wrap -->
