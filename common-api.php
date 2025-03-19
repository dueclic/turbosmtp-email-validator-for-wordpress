<?php
function turbosmtp_email_validator_status_ok($status, $validationPass) {
	if (in_array($status, $validationPass)) {
		return true;
	}

	return apply_filters('turbosmtp_email_validator_status_ok', false, $status);
}

function turbosmtp_email_validator_get_status(
	$status,
	$validationPass
){
	if (turbosmtp_email_validator_status_ok($status, $validationPass)) {
		return 'valid';
	}
	return $status;
}

function turbosmtp_email_validator_validation_forms(
	$compact = false
){

	$validation_forms = apply_filters('turbosmtp_email_validator_validation_forms', [
		'contact_form_7' => __('Contact Form 7', 'turbosmtp-email-validator'),
		'wpforms' => __('WPForms', 'turbosmtp-email-validator'),
		'woocommerce' => __('WooCommerce', 'turbosmtp-email-validator'),
		'wordpress_comments' => __('WordPress Comments', 'turbosmtp-email-validator'),
		'wordpress_registration' => __('WordPress Registration', 'turbosmtp-email-validator'),
		'mc4wp_mailchimp' => __('MC4WP: Mailchimp for WordPress', 'turbosmtp-email-validator'),
		'gravity_forms' => __('Gravity Forms', 'turbosmtp-email-validator'),
		'elementor_forms' => __('Elementor Forms', 'turbosmtp-email-validator')
	]);

	if ($compact){
		return array_keys($validation_forms);
	}
	return $validation_forms;
}
function turbosmtp_email_validator_validation_statuses(
	$compact = false
){
	$validation_statuses = apply_filters('turbosmtp_email_validator_validation_statuses', [
		'valid' => __('Valid', 'turbosmtp-email-validator'),
		'invalid' => __('Invalid', 'turbosmtp-email-validator'),
		'catch-all' => __('Catch-All', 'turbosmtp-email-validator'),
		'unknown' => __('Unknown', 'turbosmtp-email-validator'),
		'spamtrap' => __('Spamtrap', 'turbosmtp-email-validator'),
		'abuse' => __('Abuse', 'turbosmtp-email-validator'),
		'do_not_mail' => __('Do Not Mail', 'turbosmtp-email-validator'),
	]);

	if ($compact){
		return array_keys($validation_statuses);
	}
	return $validation_statuses;

}
/**
 * @param mixed $values
 * @param array $array_data
 *
 * @return array
 */

function turbosmtp_email_validator_sanitize_array( $values, $array_data ){

	if ( ! is_array( $values ) ) {
		return [];
	}


	$sanitized_input = [];
	foreach ( $values as $value ) {
		if ( in_array( $value, $array_data, true ) ) { // Strict comparison
			$sanitized_input[] = $value;
		}
	}

	return $sanitized_input;
}

/**
 * @return mixed|null
 */
function turbosmtp_email_validator_get_threshold(){
	return apply_filters( 'turbosmtp_email_validator_threshold', 3 * 60 );
}

function turbosmtp_email_validator_get_error_message(){
	$error_message = get_option( 'turbosmtp_email_validator_error_message');

	if ( function_exists( 'pll__' ) ) {
		$error_message = pll__( $error_message );
	} elseif ( function_exists( 'apply_filters' ) ) {
		$error_message = apply_filters( 'wpml_translate_single_string', $error_message, 'turbosmtp-email-validator', 'Email Validation Error Message' );
	}

	return $error_message;
}

function turbosmtp_email_validator_email_in_whitelist($email) {

	$skip_validation = apply_filters( 'turbosmtp_email_validator_skip_validation', false, $email );

	if ( $skip_validation ) {
		return true;
	}

	$whitelist = get_option('turbosmtp_email_validator_whitelist', "");
	$whitelist = explode("\n", $whitelist);

	if (in_array($email, $whitelist, true)) {
		return true;
	}

	$domain = '@' . substr(strrchr($email, "@"), 1);

	if (in_array($domain, $whitelist, true)) {
		return true;
	}

	return false;
}
