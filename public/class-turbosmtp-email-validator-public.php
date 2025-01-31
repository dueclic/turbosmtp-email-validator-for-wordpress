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
					'email_validation_error',
					$args[0]['message']
				);
			}, [ "message" => $message ]
		);

	}

	function validate_email_on_woocommerce_registration( $username, $email, $validation_errors ) {
		$validation_result = ts_emailvalidator_validate_email( $email );
		if ( is_wp_error( $validation_result ) && $validation_result->get_error_code() === 'email_validation_error' ) {
			$validation_errors->add( 'email_validation_error', $validation_result->get_error_message() );
		}
	}

	function customize_woocommerce_registration_errors( $errors, $username, $email ) {
		if ( isset( $errors->errors['email_validation_error'] ) ) {
			wc_add_notice( __( "The email entered is not valid. Please enter a valid email.", 'turbosmtp-email-validator' ), 'error' );
		}

		return $errors;
	}

	function validate_email_on_woocommerce_checkout( $data, $errors ) {
		$email = isset( $data['billing_email'] ) ? sanitize_email( $data['billing_email'] ) : '';
		if ( empty( $email ) ) {
			$errors->add( 'billing_email', __( 'Please provide a valid email', 'turbosmtp-email-validator' ) );

			return;
		}

		$validation_result = ts_emailvalidator_validate_email( $email );
		if ( is_wp_error( $validation_result ) && $validation_result->get_error_code() === 'email_validation_error' ) {
			$errors->add( 'billing_email', $validation_result->get_error_message() );
		}
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
