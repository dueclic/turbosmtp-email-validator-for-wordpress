<?php
function ts_emailvalidator_status_ok(
	$status
) {
	return $status === 'valid' || apply_filters( 'ts_email_validator_status_ok', false, $status );
}

function get_validation_forms(
	$compat = false
){

	$validation_forms = apply_filters('ts_email_validator_validation_forms', [
		'contact_form_7' => __('Contact Form 7', 'turbosmtp-email-validator'),
		'wpforms' => __('WPForms', 'turbosmtp-email-validator'),
		'woocommerce' => __('WooCommerce', 'turbosmtp-email-validator'),
		'wordpress_comments' => __('WordPress Post Comments', 'turbosmtp-email-validator'),
		'wordpress_registration' => __('WordPress Registration', 'turbosmtp-email-validator'),
		'mc4wp_mailchimp' => __('MC4WP: Mailchimp for WordPress', 'turbosmtp-email-validator'),
		'gravity_forms' => __('Gravity Forms', 'turbosmtp-email-validator'),
		'elementor_forms' => __('Elementor Forms', 'turbosmtp-email-validator')
	]);

	if ($compat){
		return array_keys($validation_forms);
	}
	return $validation_forms;
}
