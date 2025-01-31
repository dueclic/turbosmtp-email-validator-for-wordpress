<?php
function ts_emailvalidator_status_ok(
	$status
) {
	return $status === 'valid' || apply_filters( 'ts_email_validator_status_ok', false, $status );
}

function ts_emailvalidator_validate_email( $email ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'validated_emails';
	$enabled    = get_option( 'email_validation_enabled', 'no' );

	if ( $enabled !== 'yes' ) {
		return true;
	}

	$skip_validation = apply_filters( 'ts_email_validator_skip_validation', false, $email );

	if ( $skip_validation ) {
		return true;
	}

	$consumer_key    = get_option( 'email_validation_consumer_key', '' );
	$consumer_secret = get_option( 'email_validation_consumer_secret', '' );

	$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE email = %s", $email ) );

	if ( $result ) {
		$validated_at = strtotime( $result->validated_at );
		$current_time = time();
		$six_months   = 6 * 30 * 24 * 60 * 60;

		if ( ( $current_time - $validated_at ) < $six_months ) {
			if ( ts_emailvalidator_status_ok( $result->status ) ) {
				return true;
			} else {
				return new WP_Error( 'email_validation_error', __( "The email entered is not valid. Please enter a valid email.", 'turbosmtp-email-validator' ) );
			}
		} else {
			// L'email è scaduta, eliminata dalla tabella
			$wpdb->delete( $table_name, array( 'email' => $email ) );
		}
	}

	$api_url = 'https://pro.api.serversmtp.com/api/v2/emailvalidation/validateEmail';

	$response = wp_remote_post( $api_url, array(
		'headers' => array(
			'accept'         => 'application/json',
			'Content-Type'   => 'application/json',
			'consumerKey'    => $consumer_key,
			'consumerSecret' => $consumer_secret,
		),
		'body'    => json_encode( array( 'email' => $email ) ),
	) );

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'email_validation_error_api_error', __( 'Error in email validation. Please try again later.', 'turbosmtp-email-validator' ), $response->get_error_message() );
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( isset( $data['status'] ) ) {
		$wpdb->insert( $table_name, array(
			'email'        => $email,
			'status'       => $data['status'],
			'validated_at' => current_time( 'mysql' ),
			'raw_data'     => $body,
		) );

		if ( ts_emailvalidator_status_ok( $data['status'] ) ) {
			return true;
		} else {
			return new WP_Error( 'email_validation_error', __( "The email entered is not valid. Please enter a valid email.", 'turbosmtp-email-validator' ) );
		}
	} else {
		return new WP_Error( 'email_validation_error_invalid_authorization', __( "Error in email validation. Please try again later.", 'turbosmtp-email-validator' ), $data );
	}
}
