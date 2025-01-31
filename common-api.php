<?php
function ts_emailvalidator_status_ok(
	$status
) {
	return $status === 'valid' || apply_filters( 'ts_email_validator_status_ok', false, $status );
}
