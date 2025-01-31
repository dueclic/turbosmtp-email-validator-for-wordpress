<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.dueclic.com
 * @since      1.0.0
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <form method="post" action="options.php">
		<?php
		settings_fields( 'email_validation_settings_group' );
		do_settings_sections( 'email-validation-settings' );
		submit_button();
		?>
    </form>

    <?php
        if ($has_api_keys):
    ?>

    <form method="get" action="">
        <input type="hidden" name="page" value="<?php echo sanitize_text_field( $_REQUEST['page'] ); ?>">
        <input type="hidden" name="refresh" value="1">
        <h2><?php _e( "Current Subscription", "turbosmtp-email-validator" ); ?></h2>
        <p>
            <strong><?php _e( "Remaining Paid Credits", "turbosmtp-email-validator" ); ?></strong>: <?php echo $subscription['paid_credits']; ?> <?php echo $subscription['currency']; ?>
        </p>
        <p>
            <strong><?php _e( "Remaining Free Credits", "turbosmtp-email-validator" ); ?></strong>: <?php echo $subscription['remaining_free_credit']; ?>
        </p>
		<?php
		submit_button(
			__( "Refresh subscription", "turbosmtp-email-validator" )
		);
		?>
    </form>
    <h2><?php _e("Test Validator", "turbosmtp-email-validator"); ?></h2>
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
		$validation_result = ts_emailvalidator_validate_email( $test_email );
		if ( is_wp_error( $validation_result ) ) {
			echo '<div style="color: red;">' . esc_html( $validation_result->get_error_message() ) . '</div>';
		} else {
			echo '<div style="color: green;">'.__('Email is valid', 'turbosmtp-email-validator').'</div>';
		}
	}
	?>
    <h2><?php _e("Validated Emails", "turbosmtp-email-validator"); ?></h2>
	<?php

	$validated_emails_table = new Validated_Emails_Table();
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

    <?php
    endif;
    ?>

</div>
