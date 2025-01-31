<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.dueclic.com
 * @since             1.0.0
 * @package           Turbosmtp_Email_Validator
 *
 * @wordpress-plugin
 * Plugin Name:       turboSTMP Email Validator
 * Plugin URI:        https://www.serversmtp.com
 * Description:       Email validation tool in WordPress registration using turboSMTP API.
 * Version:           1.0.0
 * Author:            dueclic
 * Author URI:        https://www.dueclic.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       turbosmtp-email-validator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TURBOSMTP_EMAIL_VALIDATOR_VERSION', '1.0.0' );
define( 'TURBOSMTP_EMAIL_VALIDATOR_PATH', __FILE__);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-turbosmtp-email-validator-activator.php
 */
function activate_turbosmtp_email_validator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-turbosmtp-email-validator-activator.php';
	Turbosmtp_Email_Validator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-turbosmtp-email-validator-deactivator.php
 */
function deactivate_turbosmtp_email_validator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-turbosmtp-email-validator-deactivator.php';
	Turbosmtp_Email_Validator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_turbosmtp_email_validator' );
register_deactivation_hook( __FILE__, 'deactivate_turbosmtp_email_validator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-turbosmtp-email-validator.php';
require plugin_dir_path( __FILE__ ) . 'common-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_turbosmtp_email_validator() {

	$plugin = new Turbosmtp_Email_Validator();
	$plugin->run();

}
run_turbosmtp_email_validator();
