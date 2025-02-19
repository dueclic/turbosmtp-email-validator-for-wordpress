<div id="tsev-settings">
    <h1><?php _e( 'Email Validation Settings', "turbosmtp-email-validator" ); ?></h1>
    <p>Description</p>
    <form method="post" action="<?php echo admin_url( "options.php" ); ?>">
		<?php
		settings_fields( 'turbosmtp_email_validator_general_settings' );
		?>
        <div class="tsev-form card accordion-container">
			<?php
			do_settings_sections( 'email-general-settings' );
			?>
        </div>
        <div class="tsev-form card accordion-container">
			<?php
			do_settings_sections( 'email-validation-settings' );
			?>
        </div>
		<?php

		submit_button();
		?>
    </form>
</div>
