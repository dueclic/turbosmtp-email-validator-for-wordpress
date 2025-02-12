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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <form method="post" action="options.php">
		<?php
		settings_fields( 'turbosmtp_email_validator_general_settings' );
		do_settings_sections( 'email-validation-settings' );
		submit_button();
		?>
    </form>

    <?php
        if ($has_api_keys):
    ?>

    <form method="get" action="">
        <input type="hidden" name="page" value="<?php echo esc_attr(sanitize_text_field( $_REQUEST['page'] )); ?>">
        <input type="hidden" name="refresh" value="1">
        <h2><?php esc_html_e( "Current Subscription", "turbosmtp-email-validator" ); ?></h2>
        <p>
            <strong><?php esc_html_e( "Remaining Paid Credits", "turbosmtp-email-validator" ); ?></strong>: <?php echo esc_html( $subscription['paid_credits'] ?? 0 ); ?> <?php echo esc_html($subscription['currency'] ?? ""); ?>
        </p>
        <p>
            <strong><?php esc_html_e( "Remaining Free Credits", "turbosmtp-email-validator" ); ?></strong>: <?php echo esc_html($subscription['remaining_free_credit'] ?? 0 ); ?>
        </p>
		<?php
		submit_button(
			__( "Refresh subscription", "turbosmtp-email-validator" )
		);
		?>
    </form>
    <h2><?php esc_html_e("Test Validator", "turbosmtp-email-validator"); ?></h2>
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

    <?php
    endif;
    ?>

</div>
