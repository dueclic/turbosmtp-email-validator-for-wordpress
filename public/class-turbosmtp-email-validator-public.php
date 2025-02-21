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

	/**
	 * WordPress Comment Filter - add hook
	 * @return void
	 */
	public function apply_is_email_validator() {
		add_filter( 'is_email', [ $this, 'wordpress_is_email_validator' ], 10, 3 );
	}

	/**
	 * WordPress Comment Filter - remove hook
	 * @return void
	 */
	public function remove_is_email_validator() {
		remove_filter( 'is_email', [ $this, 'wordpress_is_email_validator' ], 10, 3 );
	}

	/**
	 * WordPress Comments Form Validator Hook
	 *
	 * @param $is_email
	 * @param $email
	 * @param $context
	 *
	 * @return false|mixed
	 */
	public function wordpress_is_email_validator( $is_email, $email, $context ) {
		if ( ! strlen( $email ) || strlen( $email ) < 3 ) {
			return false;
		}

		$wpForm         = new Turbosmtp_Email_Validator_Form_Public( 'wordpressisemail', get_the_ID() );
		$validationInfo = $wpForm->prep_validation_info( $email );

		return $wpForm->setup_form_validation( $validationInfo, function () {
			return false;
		}, [ 'is_email' => &$is_email ] );

	}

	/**
	 * @param $result
	 * @param $email
	 *
	 * @return array|WP_Error|null
	 */

	function validate_email_on_check( $result, $email ) {
		$checkEmailForm = new Turbosmtp_Email_Validator_Form_Public( 'testemail', '' );
		$validationInfo = $checkEmailForm->prep_validation_info( $email );

		$message = $checkEmailForm->set_error_message();

		$validation = $checkEmailForm->setup_form_validation(
			$validationInfo,
			function () {
				$args = func_get_args();

				return new WP_Error(
					'turbosmtp_email_validator_error',
					$args[0]['message'],
					$args[0]['validationInfo']
				);
			}, [ "message" => $message, "validationInfo" => $validationInfo ]
		);

		if (is_wp_error($validation)){
			if ($validation->get_error_code() !== 'turbosmtp_email_validator_error'){
				return $validation->get_error_data();
			}
			return $validation;
		}

		return $validationInfo;

	}

	function woocommerce_registration_validator( $username, $email, $validation_errors ) {

		$woocommerceForm = new Turbosmtp_Email_Validator_Form_Public( 'woocommerceregistration', '' );
		$validationInfo  = $woocommerceForm->prep_validation_info( $email );

		$message = $woocommerceForm->set_error_message();
		$woocommerceForm->setup_form_validation( $validationInfo, function () {
			$args              = func_get_args();
			$message           = $args[0]['message'];
			$validation_errors = &$args[0]['validation_errors'];
			$validation_errors->add( 'turbosmtp_email_validator_error', $message );
		}, [ 'message' => $message, 'validation_errors' => &$validation_errors ] );

	}

	/**
	 * Woocommerce Checkout Form Validator Hook - shortcode blocks
	 *
	 * @param $fields
	 * @param $errors
	 *
	 * @return void
	 */
	public function woocommerce_validator( $fields, $errors ) {
		$woocommerceForm = new Turbosmtp_Email_Validator_Form_Public( 'woocommercecheckout', '' );
		$validationInfo  = null;

		if ( ! empty( $fields['billing_email'] ) ) {
			$validationInfo = $woocommerceForm->prep_validation_info( $fields['billing_email'] );
		}

		if ( ! empty( $fields['shipping_email'] ) ) {
			$validationInfo = $woocommerceForm->prep_validation_info( $fields['shipping_email'] );
		}

		$message = $woocommerceForm->set_error_message();
		$woocommerceForm->setup_form_validation( $validationInfo, function () {
			$args    = func_get_args();
			$message = $args[0]['message'];
			$errors  = &$args[0]['errors'];
			$errors->add( 'validation', $message );
		}, [ 'message' => $message, 'errors' => &$errors ] );
	}


	/**
	 * WordPress Registration Form Validator Hook
	 *
	 * @param $errors
	 * @param $sanitized_user_login
	 * @param $email
	 *
	 * @return mixed
	 */
	public function wordpress_registration_validator( $errors, $sanitized_user_login, $email ) {
		if ( email_exists( $email ) ) {
			return $errors;
		}

		$wprForm        = new Turbosmtp_Email_Validator_Form_Public( 'wordpressregister', '' );
		$validationInfo = $wprForm->prep_validation_info( $email );
		$message        = $wprForm->set_error_message();
		$wprForm->setup_form_validation( $validationInfo, function () {
			$args    = func_get_args();
			$message = $args[0]['message'];
			$errors  = &$args[0]['errors'];
			$errors->add( 'invalid_email', $message  );
		}, [ 'message' => $message, 'errors' => &$errors ] );

		return $errors;
	}

	/**
	 * WordPress Multisite Registration Form Validator Hook
	 *
	 * @param $result
	 *
	 * @return mixed
	 */
	public function wordpress_multisite_registration_validator( $result ) {
		$email = $result['user_email'];
		if ( ! strlen( $email ) || strlen( $email ) < 3 ) {
			return $result;
		}

		$wprForm        = new Turbosmtp_Email_Validator_Form_Public( 'wordpressmultisiteregister', '' );
		$validationInfo = $wprForm->prep_validation_info( $email );
		$message        = $wprForm->set_error_message();
		$wprForm->setup_form_validation( $validationInfo, function () {
			$args    = func_get_args();
			$message = $args[0]['message'];
			$result  = &$args[0]['result'];
			$result['errors']->add( 'user_email', $message ) ;
		}, [ 'message' => $message, 'result' => &$result ] );

		return $result;
	}

	/**
	 * Mailchimp Form Validator Hook
	 *
	 * @param $errors
	 * @param MC4WP_Form $form
	 *
	 * @return mixed
	 */
	public function mc4wp_mailchimp_validator( $errors, $form ) {
		$data  = $form->get_data();
		$email = strtolower( $data['EMAIL'] );

		$mc4Form        = new Turbosmtp_Email_Validator_Form_Public( 'mc4wp_mailchimp', $form->ID );
		$validationInfo = $mc4Form->prep_validation_info( $email );
		$mc4Form->setup_form_validation( $validationInfo, function () {
			$args     = func_get_args();
			$errors   = &$args[0]['errors'];
			$errors[] = 'invalid_email';
		}, [ 'errors' => &$errors ] );

		return $errors;
	}

	/**
	 * Contact Forms 7 Validator Hook
	 *
	 * @param $result
	 * @param $tag
	 *
	 * @return mixed
	 */
	public function contact_form_7_validator( $result, $tag ) {
		$tag = new WPCF7_FormTag( $tag );
		if ( 'email' == $tag->type || 'email*' == $tag->type ) {
			$wpcf7Form      = new Turbosmtp_Email_Validator_Form_Public( 'cf7forms', '' );
			$validationInfo = $wpcf7Form->prep_validation_info(
				sanitize_email( $_POST[ $tag->name ] )
			);
			$message        = $wpcf7Form->set_error_message();
			$wpcf7Form->setup_form_validation( $validationInfo, function () {
				$args = func_get_args();
				extract( $args[0] );
				$result->invalidate( $tag, $message );
			}, [ 'message' => $message, 'tag' => $tag, 'result' => &$result ] );
		}

		return $result;
	}

	/**
	 * WpForms Form Validator Hook
	 *
	 * @param $fields
	 * @param $entry
	 * @param $form_data
	 *
	 * @return mixed
	 */
	public function wpforms_validator( $fields, $entry, $form_data ) {
		foreach ( $fields as $field_id => $field ) {
			if ( isset( $field['type'] ) && $field['type'] === 'email' && ! empty( $field['value'] ) ) {
				$wpForm         = new Turbosmtp_Email_Validator_Form_Public( 'wpforms', '' );
				$validationInfo = $wpForm->prep_validation_info( $field['value'] );
				$message        = $wpForm->set_error_message( $validationInfo['did_you_mean'] );
				$wpForm->setup_form_validation( $validationInfo, function () {
					$args                                                       = func_get_args();
					$form_data                                                  = $args[0]['form_data'];
					$message                                                    = $args[0]['message'];
					$field_id                                                   = $args[0]['field_id'];
					wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = $message;
				}, [ 'form_data' => $form_data, 'field_id' => $field_id, 'message' => $message ] );
			}
		}

		return $fields;
	}

	/**
	 * Elementor Form Validator Hook
	 *
	 * @param $field
	 * @param $record
	 * @param $ajax_handler
	 *
	 * @return void
	 */
	public function elementor_validator( $field, $record, $ajax_handler ) {
		if ( empty( $field ) ) {
			return;
		}

		$settings = $record->get( 'form_settings' );

		if ( empty( $settings ) ) {
			return;
		}

		$elementorForm = new Turbosmtp_Email_Validator_Form_Public( 'elementor_forms', get_the_ID() );

		$email = sanitize_email( $field['value'] );

		$validationInfo = $elementorForm->prep_validation_info( $email );
		$message        = $elementorForm->set_error_message();

		$elementorForm->setup_form_validation( $validationInfo, function () {
			$args         = func_get_args();
			$message      = $args[0]['message'];
			$field_id     = $args[0]['field_id'];
			$ajax_handler = $args[0]['ajax_handler'];
			$ajax_handler->add_error( $field_id, $message );
		}, [ 'message' => $message, 'field_id' => $field['id'], 'ajax_handler' => &$ajax_handler ] );

	}

	private function gravity_form_validation( $id, $email, &$result ) {
		$gravityForm    = new Turbosmtp_Email_Validator_Form_Public( 'gravity_forms', $id );
		$validationInfo = $gravityForm->prep_validation_info( $email );
		$message        = $gravityForm->set_error_message();
		$gravityForm->setup_form_validation( $validationInfo, function () {
			$args               = func_get_args();
			$message            = $args[0]['message'];
			$result             = &$args[0]['result'];
			$result['is_valid'] = false;
			$result['message']  = $message;
		}, [ 'message' => $message, 'result' => &$result ] );
	}

	/**
	 * Gravity Forms Validator Hook
	 *
	 * @param $result
	 * @param $value
	 * @param $form
	 * @param $field
	 *
	 * @return mixed
	 */
	public function gravity_forms_validator( $result, $value, $form, $field ) {
		if ( $field->type == 'email' && $field->isRequired == 1 ) {
			if ( is_array( $value ) && count( $value ) !== 0 ) {
				foreach ( $value as $k => $v ) {
					$this->gravity_form_validation( $form['id'], $v, $result );
				}
			} else {
				$this->gravity_form_validation( $form['id'], $value, $result );
			}
		}

		return $result;
	}

	/**
	 * Mailchimp add custom message to email validation - hook
	 *
	 * @param $messages
	 *
	 * @return mixed
	 */
	public function mc4wp_mailchimp_error_message( $messages ) {
		$mc4Form                   = new Turbosmtp_Email_Validator_Form_Public( 'mc4wp_mailchimp', '' );
		$messages['invalid_email'] = $mc4Form->set_error_message();

		return $messages;
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
