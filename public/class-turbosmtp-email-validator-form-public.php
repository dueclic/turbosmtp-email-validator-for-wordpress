<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/public
 * @author     dueclic <info@dueclic.com>
 */
class Turbosmtp_Email_Validator_Form_Public {
	/**
	 * The turboSMTP API class used for validation
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Turbosmtp_Email_Validator_API
	 */
	private $api;

	/**
	 * @var string
	 */
	private $source;

	/**
	 * @var string
	 */
	private $formId;

	/**
	 * @var array
	 */
	private $validationPass;

	/**
	 * @param $source
	 * @param $formId
	 *
	 * @return void
	 */
	public function __construct( $source, $formId ) {
		$this->source         = $source;
		$this->formId         = $formId;
		$this->validationPass = get_option( 'turbosmtp_email_validator_validation_pass' );
		$this->api            = new Turbosmtp_Email_Validator_API(
			get_option( 'turbosmtp_email_validator_consumer_key' ),
			get_option( 'turbosmtp_email_validator_consumer_secret' ),
			get_option( 'turbosmtp_email_validator_api_timeout' )
		);
	}

	/**
	 * @param string $raw_email
	 */
	private function validate( string $raw_email ) {
		global $wpdb;
		$email      = sanitize_email( $raw_email );
		$table_name = $wpdb->prefix . 'validated_emails';

		$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE email = %s", $email ), ARRAY_A );

		if ( $result ) {
			$validated_at    = strtotime( $result['validated_at'] );
			$current_time    = time();
			$expire_interval = 6 * 30 * 24 * 60 * 60;

			if ( ( $current_time - $validated_at ) < apply_filters( 'turbosmtp_email_validator_expire_interval', $expire_interval ) ) {
				return json_decode($result['raw_data'], true);
			} else {
				$wpdb->delete( $table_name, array( 'email' => $email ) );
			}
		}

		return $this->api->validateEmail( $email );

	}

	/**
	 * @return string
	 */
	public function set_error_message() {
		$custom_error = get_option( 'turbosmtp_email_validator_error_message' );

		$error_message = __( 'We cannot accept this email address.', 'turbosmtp-email-validator' );
		if ( isset( $custom_error ) && $custom_error ) {
			$error_message = $custom_error;
		}

		return $error_message;
	}

	/**
	 * @param $email
	 *
	 * @return array|null
	 */
	public function prep_validation_info( $email ): ?array {
		global $wpdb;

		$skip_validation = apply_filters( 'turbosmtp_email_validator_skip_validation', false, $email );

		if ( $skip_validation ) {
			return null;
		}

		$validationInfo = $this->validate( $email );

		if ( $validationInfo != null ) {
			$table_name = $wpdb->prefix . 'validated_emails';

			$wpdb->insert(
				$table_name, array(
					'email'        => $email,
					'source'       => $this->source,
					'ip_address'   => ( $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER['REMOTE_ADDR'] ),
					'form_id'      => $this->formId,
					'status'       => turbosmtp_email_validator_get_status($validationInfo['status'], $this->validationPass),
					'sub_status'   => $validationInfo['sub_status'],
					'original_status'   => $validationInfo['status'],
					'validated_at' => current_time( 'mysql' ),
					'raw_data'     => json_encode( $validationInfo ),
				)
			);
		}

		return $validationInfo;
	}

	/**
	 * @param ?array $validationInfo
	 * @param callable $callback
	 * @param $args
	 *
	 * @return WP_Error|null
	 */
	public function setup_form_validation( ?array $validationInfo, callable $callback, $args ) {
		if ( ! is_null( $validationInfo ) ) {
			if ( ! turbosmtp_email_validator_status_ok( $validationInfo['status'], $this->validationPass ) ) {
				return call_user_func( $callback, $args );
			}
		}

		return null;
	}
}
