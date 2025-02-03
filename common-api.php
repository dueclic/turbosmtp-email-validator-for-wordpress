<?php
function ts_emailvalidator_status_ok($status, $validationPass) {
	if (in_array($status, $validationPass)) {
		return true;
	}

	return apply_filters('ts_email_validator_status_ok', false, $status);
}

function get_validation_forms(
	$compact = false
){

	$validation_forms = apply_filters('ts_email_validator_validation_forms', [
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
function get_validation_statuses(
	$compact = false
){
	$validation_statuses = apply_filters('ts_email_validator_validation_statuses', [
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
