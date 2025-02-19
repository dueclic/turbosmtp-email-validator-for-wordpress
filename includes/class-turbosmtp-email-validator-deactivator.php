<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.dueclic.com
 * @since      1.0.0
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/includes
 * @author     dueclic <info@dueclic.com>
 */
class Turbosmtp_Email_Validator_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;


		delete_option("turbosmtp_email_validator_enabled");
		delete_option("turbosmtp_email_validator_consumer_key");
		delete_option("turbosmtp_email_validator_consumer_secret");
		delete_option("turbosmtp_email_validator_api_timeout");
		delete_option("turbosmtp_email_validator_validation_forms");
		delete_option("turbosmtp_email_validator_validation_pass");
		delete_option("turbosmtp_email_validator_general_settings");
		delete_option("turbosmtp_email_validator_error_message");

		$table_name      = $wpdb->prefix . 'validated_emails';

		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		delete_transient( 'turbosmtp_email_validator_subscription' );

	}

}
