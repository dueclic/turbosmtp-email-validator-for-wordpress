<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.dueclic.com
 * @since      1.0.0
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/includes
 * @author     dueclic <info@dueclic.com>
 */
class Turbosmtp_Email_Validator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Turbosmtp_Email_Validator_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TURBOSMTP_EMAIL_VALIDATOR_VERSION' ) ) {
			$this->version = TURBOSMTP_EMAIL_VALIDATOR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'turbosmtp-email-validator';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Turbosmtp_Email_Validator_Loader. Orchestrates the hooks of the plugin.
	 * - Turbosmtp_Email_Validator_Admin. Defines all hooks for the admin area.
	 * - Turbosmtp_Email_Validator_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-turbosmtp-email-validator-loader.php';

		/**
		 * The class responsible for defining turboSMTP API functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-turbosmtp-email-validator-api.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-turbosmtp-email-validator-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-turbosmtp-email-validator-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-turbosmtp-email-validator-form-public.php';

		$this->loader = new Turbosmtp_Email_Validator_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Turbosmtp_Email_Validator_Admin(
			new Turbosmtp_Email_Validator_API(
				$this->get_consumer_key(), $this->get_consumer_secret(), $this->get_api_timeout()
			), $this->get_plugin_name(), $this->get_version()
		);

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'settings_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_init' );
	}

	/**
	 * Retrieve the Consumer Key for turboSMTP.
	 *
	 * @return    string    The consumer key defined or null.
	 * @since     1.0.0
	 */
	public function get_consumer_key() {
		$consumer_key = get_option( 'turbosmtp_email_validator_consumer_key' );

		return $consumer_key ?: "";
	}

	/**
	 * Retrieve the Consumer Secret for turboSMTP.
	 *
	 * @return    string    The consumer Secret defined or null.
	 * @since     1.0.0
	 */
	public function get_consumer_secret() {
		$consumer_key = get_option( 'turbosmtp_email_validator_consumer_secret' );

		return $consumer_key ?: "";
	}

	/**
	 * Retrieve the API Timeout for turboSMTP.
	 *
	 * @return    int    The api timeout defined or 5 seconds by default.
	 * @since     1.0.0
	 */
	public function get_api_timeout() {
		$api_timeout = get_option( 'turbosmtp_email_validator_api_timeout' );

		return $api_timeout ?: (int) 5;
	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Turbosmtp_Email_Validator_Public(
			new Turbosmtp_Email_Validator_API(
				$this->get_consumer_key(), $this->get_consumer_secret(), $this->get_api_timeout()
			),
			$this->get_plugin_name(), $this->get_version()
		);

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$enabled = get_option( 'turbosmtp_email_validator_enabled', 'no' );
		$validation_forms = get_option('turbosmtp_email_validator_validation_forms');

		if ( $enabled === 'yes' ) {

			$this->loader->add_filter( 'turbosmtp_email_validator_checkemail', $plugin_public, 'validate_email_on_check', 10, 2 );

			// WordPress Registrations

			if (is_array($validation_forms)  && in_array('wordpress_registration', $validation_forms) ){
				$this->loader->add_filter('registration_errors', $plugin_public, 'wordpress_registration_validator', 10, 3);
				$this->loader->add_filter('wpmu_validate_user_signup', $plugin_public, 'wordpress_multisite_registration_validator', 10, 1);
			}

			// WooCommerce

			if (is_array($validation_forms)  && in_array('woocommerce', $validation_forms) ) {
				$this->loader->add_action( 'woocommerce_register_post', $plugin_public, 'woocommerce_registration_validator', 10, 3 );
				$this->loader->add_filter( 'woocommerce_after_checkout_validation', $plugin_public, 'woocommerce_validator', 10, 2 );
			}

			// WordPress Comments

			if (is_array($validation_forms) && in_array('wordpress_comments', $validation_forms)) {
				$this->loader->add_action('pre_comment_on_post', $plugin_public, 'apply_is_email_validator');
				$this->loader->add_action('comment_post', $plugin_public, 'remove_is_email_validator');
			}

			// MailChimp Forms

			if (is_array($validation_forms) && in_array('mc4wp_mailchimp', $validation_forms)) {
				$this->loader->add_filter('mc4wp_form_messages', $plugin_public, 'mc4wp_mailchimp_error_message');
				$this->loader->add_filter('mc4wp_form_errors', $plugin_public, 'mc4wp_mailchimp_validator', 10, 2);
			}

			// Gravity Forms

			if (is_array($validation_forms) && in_array('gravity_forms', $validation_forms)) {
				$this->loader->add_filter('gform_field_validation', $plugin_public, 'gravity_forms_validator', 10, 4);
			}

			// Contact Form 7

			if (is_array($validation_forms) && in_array('contact_form_7', $validation_forms)) {
				$this->loader->add_filter('wpcf7_validate_email', $plugin_public, 'contact_form_7_validator', 10, 2);
				$this->loader->add_filter('wpcf7_validate_email*', $plugin_public, 'contact_form_7_validator', 10, 2);
			}

			// WPForms
			if (is_array($validation_forms) && in_array('wpforms', $validation_forms)) {
				$this->loader->add_filter('wpforms_process_after_filter', $plugin_public, 'wpforms_validator', 10, 3);
			}

			if (is_array($validation_forms) && in_array('elementor_forms', $validation_forms)) {
				$this->loader->add_filter('elementor_pro/forms/validation/email', $plugin_public, 'elementor_validator', 10, 3);
			}


		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Turbosmtp_Email_Validator_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
