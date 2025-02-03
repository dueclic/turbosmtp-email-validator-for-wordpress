<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.dueclic.com
 * @since      1.0.0
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/includes
 * @author     dueclic <info@dueclic.com>
 */
class Turbosmtp_Email_Validator_Activator {

	public static function setup_db_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'validated_emails';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        source varchar(255) NOT NULL,
        form_id VARCHAR(255) NOT NULL,
        email varchar(255) NOT NULL,
        status varchar(50) NOT NULL,
        validated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        raw_data text NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::setup_db_table();
		$validation_forms = [
			'contact_form_7',
			'wpforms',
			'woocommerce',
			'wordpress_comments',
			'wordpress_registration',
			'mc4wp_mailchimp',
			'gravity_forms',
			'elementor_forms'
		];

		update_option( 'ts_email_validator_settings_validation_forms', $validation_forms );

		$validation_pass = [
			'valid'     => 'valid',
			'catch-all' => 'catch-all',
			'unknown'   => 'unknown',
		];

		update_option( 'ts_email_validator_settings_validation_pass', $validation_pass );


	}

}
