<form method="get" action="">
	<input type="hidden" name="page" value="<?php echo esc_attr(sanitize_text_field( $_REQUEST['page'] )); ?>">
	<input type="hidden" name="refresh" value="1">
	<h3><?php esc_html_e( "Current Subscription", "turbosmtp-email-validator" ); ?></h3>
	<div class="tsev-credits-row">
		<strong><?php esc_html_e( "Remaining Paid Credits", "turbosmtp-email-validator" ); ?></strong> <span><?php echo esc_html($subscription['currency'] ?? ""); ?> <?php echo esc_html( $subscription['paid_credits'] ?? 0 ); ?></span>
	</div>
	<div class="tsev-credits-row">
		<strong><?php esc_html_e( "Remaining Free Credits", "turbosmtp-email-validator" ); ?></strong> <span><?php echo esc_html($subscription['remaining_free_credit'] ?? 0 ); ?></span>
	</div>
	<div class="tsev-text-center submit">
		<button type="submit" name="submit" id="submit" class="button button-small button-secondary">
			<?php _e( "Refresh subscription", "turbosmtp-email-validator" ); ?>
		</button>
	</div>
	<?php
	/*
	submit_button(
		__( "Refresh subscription", "turbosmtp-email-validator" )
	);
	*/
	?>
</form>
<div id="tsev-settings">
	<h1><?php _e( 'Email Validation Settings', "turbosmtp-email-validator" ); ?></h1>
	<p>Description</p>
	<form method="post" action="options.php">
		<div class="tsev-form card accordion-container">
			<h2>General settings</h2>
			<p>General settings info</p>
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row" class="titledesc">Enalble Email verification</th>
					<td class="forminp forminp-checkbox ">
						<fieldset>
							<!--legend class="screen-reader-text">
								<span>Check to enable real time email verification on forms submit</span>
							</legend-->
							<label for="turbosmtp_email_validator_enabled">
								<input type="checkbox" name="turbosmtp_email_validator_enabled" value="yes" checked="checked">
								Check to enable real time email verification on forms submit
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc">API timeout</th>
					<td class="forminp forminp-input">
						<div style="display: flex; align-items: center; gap: 10px;">
							<input type="number" style="max-width: 100px" min="1" name="wc_emailchef_cron_end_interval_value" id="wc_emailchef_cron_end_interval_value" value="1">
							hours (?)
						</div>
						<p>Description</p>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="tsev-form card accordion-container">
			<h2>Validation settings</h2>
			<p>Define on which forms would you like to apply email validation, and email statuses that should pass validation.</p>
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row">Forms to be validated</th>
					<td>
						<fieldset><label for="turbosmtp_email_validator_validation_forms_1"><input
									id="turbosmtp_email_validator_validation_forms_1"
									name="turbosmtp_email_validator_validation_forms[]"
									type="checkbox" value="contact_form_7" checked="checked">
								Contact Form 7</label><br><label
								for="turbosmtp_email_validator_validation_forms_2"><input
									id="turbosmtp_email_validator_validation_forms_2"
									name="turbosmtp_email_validator_validation_forms[]"
									type="checkbox" value="wpforms" checked="checked">
								WPForms</label><br><label
								for="turbosmtp_email_validator_validation_forms_3"><input
									id="turbosmtp_email_validator_validation_forms_3"
									name="turbosmtp_email_validator_validation_forms[]"
									type="checkbox" value="woocommerce" checked="checked">
								WooCommerce</label><br><label
								for="turbosmtp_email_validator_validation_forms_4"><input
									id="turbosmtp_email_validator_validation_forms_4"
									name="turbosmtp_email_validator_validation_forms[]"
									type="checkbox" value="wordpress_comments" checked="checked">
								WordPress Comments</label><br><label
								for="turbosmtp_email_validator_validation_forms_5"><input
									id="turbosmtp_email_validator_validation_forms_5"
									name="turbosmtp_email_validator_validation_forms[]"
									type="checkbox" value="wordpress_registration"
									checked="checked"> WordPress Registration</label><br><label
								for="turbosmtp_email_validator_validation_forms_6"><input
									id="turbosmtp_email_validator_validation_forms_6"
									name="turbosmtp_email_validator_validation_forms[]"
									type="checkbox" value="mc4wp_mailchimp" checked="checked">
								MC4WP: Mailchimp for WordPress</label><br><label
								for="turbosmtp_email_validator_validation_forms_7"><input
									id="turbosmtp_email_validator_validation_forms_7"
									name="turbosmtp_email_validator_validation_forms[]"
									type="checkbox" value="gravity_forms" checked="checked"> Gravity
								Forms</label><br><label
								for="turbosmtp_email_validator_validation_forms_8"><input
									id="turbosmtp_email_validator_validation_forms_8"
									name="turbosmtp_email_validator_validation_forms[]"
									type="checkbox" value="elementor_forms" checked="checked">
								Elementor Forms</label><br></fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row">Validation pass</th>
					<td>
						<p style="margin-bottom: 1rem;">Define which email statuses should be considered valid.
							<a href="https://serversmtp.com/email-validation-tool/" target="_blank">More info about statuses here</a>.
						</p>
						<fieldset><label for="turbosmtp_email_validator_validation_pass_1"><input
									id="turbosmtp_email_validator_validation_pass_1"
									name="turbosmtp_email_validator_validation_pass[]"
									type="checkbox" value="valid" checked="checked">
								Valid</label><br><label
								for="turbosmtp_email_validator_validation_pass_2"><input
									id="turbosmtp_email_validator_validation_pass_2"
									name="turbosmtp_email_validator_validation_pass[]"
									type="checkbox" value="invalid"> Invalid</label><br><label
								for="turbosmtp_email_validator_validation_pass_3"><input
									id="turbosmtp_email_validator_validation_pass_3"
									name="turbosmtp_email_validator_validation_pass[]"
									type="checkbox" value="catch-all" checked="checked">
								Catch-All</label><br><label
								for="turbosmtp_email_validator_validation_pass_4"><input
									id="turbosmtp_email_validator_validation_pass_4"
									name="turbosmtp_email_validator_validation_pass[]"
									type="checkbox" value="unknown" checked="checked">
								Unknown</label><br><label
								for="turbosmtp_email_validator_validation_pass_5"><input
									id="turbosmtp_email_validator_validation_pass_5"
									name="turbosmtp_email_validator_validation_pass[]"
									type="checkbox" value="spamtrap"> Spamtrap</label><br><label
								for="turbosmtp_email_validator_validation_pass_6"><input
									id="turbosmtp_email_validator_validation_pass_6"
									name="turbosmtp_email_validator_validation_pass[]"
									type="checkbox" value="abuse"> Abuse</label><br><label
								for="turbosmtp_email_validator_validation_pass_7"><input
									id="turbosmtp_email_validator_validation_pass_7"
									name="turbosmtp_email_validator_validation_pass[]"
									type="checkbox" value="do_not_mail"> Do Not Mail</label><br>
						</fieldset>
					</td>
				</tr>
				</tbody>
			</table>
		</div>


	</form>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'turbosmtp_email_validator_general_settings' );
		do_settings_sections( 'email-validation-settings' );
		submit_button();
		?>
	</form>

</div>
<div id="tsev-validator">
	<h2><?php esc_html_e("Test Validator", "turbosmtp-email-validator"); ?></h2>
	<p>Description</p>
	<form method="post" action="">
		<input type="email" name="test_email" value="" required>
		<?php
		submit_button(
			__( "Verify now", "turbosmtp-email-validator" )
		);
		?>
	</form>
	<?php
	if ( isset( $_POST['test_email'] ) ) {
		$test_email        = sanitize_email( $_POST['test_email'] );

		$validation_result = "";

		$validation_result = apply_filters( 'turbosmtp_email_validator_checkemail', $validation_result, $test_email );
		if ( is_wp_error( $validation_result ) ) {
			echo '<div style="color: red;">' . esc_html( $validation_result->get_error_message() ) . '</div>';
		} else {
			echo '<div style="color: green;">'.esc_html__('Email is valid', 'turbosmtp-email-validator').'</div>';
		}
	}
	?>
</div>
<div id="tsev-log">
	<h2><?php esc_html_e("Validated Emails", "turbosmtp-email-validator"); ?></h2>
	<?php

	$validated_emails_table->views();
	?>
	<form method="post">
		<?php
		$validated_emails_table->prepare_items();
		$validated_emails_table->search_box(
			__( 'Search', 'turbosmtp-email-validator' ),
			'search_id'
		);
		?>
		<?php $validated_emails_table->display(); ?>
	</form>
</div>
