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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
<div id="tsev-validator">
    <h1><?php esc_html_e( "Test Validator", "turbosmtp-email-validator" ); ?></h1>
    <p><?php esc_html_e( "Instantly verify any email address with our manual validation feature. Simply type an email into the input field to check its authenticity on the spot.", "turbosmtp-email-validator" ); ?></p>
    <p><i><?php esc_html_e( "Keep in mind that the final validation result is determined not only by the API response but also by your configured settings. These settings can influence the interpretation of the validation outcome.", "turbosmtp-email-validator" ); ?></i></p>
    <div class="tsev-form tsev-form-validator card accordion-container tsev-text-center">

		<?php
		$test_email = "";
		if ( isset( $_POST['test_email'] ) ) {
			$test_email = sanitize_email( $_POST['test_email'] );

			$validation_result = apply_filters( 'turbosmtp_email_validator_checkemail', null, $test_email );

			if ( is_null( $validation_result ) ) {
				echo '<p class="tsev-validator-invalid">' . esc_html__( "Something was wrong.", "turbosmtp-email-validator" ) . '</p>';
                echo '<p>'.sprintf(__("In case you already validated this email, please wait %d seconds."), turbosmtp_email_validator_get_threshold()).'</p>';
			} else {
				if ( is_wp_error( $validation_result ) ) {
					?>

                    <h1 class="tsev-validator-invalid"><?php echo $test_email; ?></h1>
                    <h2 class="tsev-validator-invalid"><?php echo esc_html( $validation_result->get_error_message() ); ?></h2>

				<?php } else { ?>

                    <h2 class="tsev-validator-valid"><?php echo $test_email; ?></h2>
                    <h2 class="tsev-validator-valid"><?php echo esc_html__( 'Email is valid', 'turbosmtp-email-validator' ); ?></h2>

				<?php } ?>

                    <div class="tsev-validation-response-container">
				<?php
				$validation_details = is_wp_error( $validation_result ) ?
                    $validation_result->get_error_data() :
                    $validation_result;

				include_once plugin_dir_path( TURBOSMTP_EMAIL_VALIDATOR_PATH ) . 'admin/partials/validation-details.php';

            ?>
                </div>
                <hr class="tsev-hr-separator">
        <?php
                }
		}
		?>

        <form method="post" action="">
            <p>Insert the email you want to validate:</p>
            <div class="tsev-validator-input">
                <input type="email" name="test_email" class="tsev-validator-email" value="" required>
				<?php
				submit_button(
					__( "Verify now", "turbosmtp-email-validator" )
				);
				?>
            </div>
            <p class="tsev-text-left"><i><?php esc_html_e( "Keep in mind that the final validation result is determined not only by the API response but also by your configured settings. These settings can influence the interpretation of the validation outcome.", "turbosmtp-email-validator" ); ?></i></p>
        </form>
    </div>
</div>
