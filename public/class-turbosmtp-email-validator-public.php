<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.dueclic.com
 * @since      1.0.0
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/public
 * @author     dueclic <info@dueclic.com>
 */
class Turbosmtp_Email_Validator_Public {

	/**
	 * The turboSMTP API class used for validation
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Turbosmtp_Email_Validator_API $api
	 */
	private $api;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param Turbosmtp_Email_Validator_API $api New instance for Turbosmtp_Email_Validator_API class
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $api, $plugin_name, $version ) {

		$this->api         = $api;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	function validate_email_on_check( $result, $email ): ?WP_Error {
		$checkEmailForm = new Turbosmtp_Email_Validator_Form_Public( 'testemail', '' );
		$validationInfo = $checkEmailForm->prep_validation_info( $email );

		$message = $checkEmailForm->set_error_message();

		return $checkEmailForm->setup_form_validation(
			$validationInfo,
			function () {
				$args = func_get_args();

				return new WP_Error(
					'ts_email_validator_error',
					$args[0]['message']
				);
			}, [ "message" => $message ]
		);

	}

	function woocommerce_registration_validator( $username, $email, $validation_errors ) {

		$woocommerceForm = new Turbosmtp_Email_Validator_Form_Public('woocommerceregistration', '');
		$validationInfo = $woocommerceForm->prep_validation_info($email);

		$message = $woocommerceForm->set_error_message();
		$woocommerceForm->setup_form_validation($validationInfo, function () {
			$args = func_get_args();
			$message = $args[0]['message'];
			$validation_errors = &$args[0]['validation_errors'];
			$validation_errors->add( 'ts_email_validator_error', $message );
		}, ['message' => $message, 'validation_errors' => &$validation_errors]);

	}

	/**
	 * Woocommerce Checkout Form Validator Hook - shortcode blocks
	 * @param $fields
	 * @param $errors
	 * @return void
	 */
	public function woocommerce_validator($fields, $errors)
	{
		$woocommerceForm = new Turbosmtp_Email_Validator_Form_Public('woocommercecheckout', '');
		$validationInfo = null;

		if (!empty($fields['billing_email'])) {
			$validationInfo = $woocommerceForm->prep_validation_info($fields['billing_email']);
		}

		if (!empty($fields['shipping_email'])) {
			$validationInfo = $woocommerceForm->prep_validation_info($fields['shipping_email']);
		}

		$message = $woocommerceForm->set_error_message();
		$woocommerceForm->setup_form_validation($validationInfo, function () {
			$args = func_get_args();
			$message = $args[0]['message'];
			$errors = &$args[0]['errors'];
			$errors->add('validation', esc_html__($message));
		}, ['message' => $message, 'errors' => &$errors]);
	}


	/**
	 * WordPress Registration Form Validator Hook
	 * @param $errors
	 * @param $sanitized_user_login
	 * @param $email
	 * @return mixed
	 */
	public function wordpress_registration_validator($errors, $sanitized_user_login, $email)
	{
		if (email_exists($email)) {
			return $errors;
		}

		$wprForm = new Turbosmtp_Email_Validator_Form_Public('wordpressregister', '');
		$validationInfo = $wprForm->prep_validation_info($email);
		$message = $wprForm->set_error_message();
		$wprForm->setup_form_validation($validationInfo, function () {
			$args = func_get_args();
			$message = $args[0]['message'];
			$errors = &$args[0]['errors'];
			$errors->add('invalid_email', esc_html__($message));
		}, ['message' => $message, 'errors' => &$errors]);

		return $errors;
	}

	/**
	 * WordPress Multisite Registration Form Validator Hook
	 * @param $result
	 * @return mixed
	 */
	public function wordpress_multisite_registration_validator($result)
	{
		$email = $result['user_email'];
		if (!strlen($email) || strlen($email) < 3) {
			return $result;
		}

		$wprForm = new Turbosmtp_Email_Validator_Form_Public('wordpressmultisiteregister', '');
		$validationInfo = $wprForm->prep_validation_info($email);
		$message = $wprForm->set_error_message();
		$wprForm->setup_form_validation($validationInfo, function () {
			$args = func_get_args();
			$message = $args[0]['message'];
			$result = &$args[0]['result'];
			$result['errors']->add('user_email', esc_html__($message));
		}, ['message' => $message, 'result' => &$result]);

		return $result;
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Turbosmtp_Email_Validator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Turbosmtp_Email_Validator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/turbosmtp-email-validator-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Turbosmtp_Email_Validator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Turbosmtp_Email_Validator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/turbosmtp-email-validator-public.js', array( 'jquery' ), $this->version, false );

	}

}
