<div id="tsev-settings">
	<form method="post" action="<?php echo admin_url( "options.php" ); ?>">
		<?php
		settings_fields( 'turbosmtp_email_validator_general_settings' );
		do_settings_sections( 'email-validation-settings' );
		submit_button();
		?>
	</form>
</div>
