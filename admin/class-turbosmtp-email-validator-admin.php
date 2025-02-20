<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.dueclic.com
 * @since      1.0.0
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/admin
 * @author     dueclic <info@dueclic.com>
 */
class Turbosmtp_Email_Validator_Admin {

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
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/turbosmtp-email-validator-admin.css', array(), $this->version, 'all' );

	}

	public function ajax_get_email_details() {
		global $wpdb;

		if ( ! wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ), 'turbosmtp-email-validator_get_email_details' ) ) {
			wp_send_json_error( [
				'message' => __( 'Invalid request', 'turbosmtp-email-validator' )
			] );
		}

		$table_name = $wpdb->prefix . 'validated_emails';

		$email_id = isset( $_POST['email_id'] ) ? intval( $_POST['email_id'] ) : 0;
		if ( ! $email_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid email ID.', 'turbosmtp-email-validator' ) ] );
		}

		$validation_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $email_id ), ARRAY_A );
		if ( ! $validation_details ) {
			wp_send_json_error( [ 'message' => __( 'Email not found.', 'turbosmtp-email-validator' ) ] );
		}

        $validation_details = json_decode($validation_details['raw_data']);
        $is_modal = true;

		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/validation-details.php';
		$html = ob_get_clean();

		wp_send_json_success( [ 'html' => $html ] );
	}

	public function ajax_disconnect() {

		if ( ! wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ), 'turbosmtp-email-validator-disconnect' ) ) {
			wp_send_json_error( [
				'message' => __( 'Invalid request', 'turbosmtp-email-validator' )
			] );
		}

		deactivate_turbosmtp_email_validator();

		wp_send_json_success( [
			'message' => __( 'turboSMTP account successfully disconnected', 'turbosmtp-email-validator' )
		] );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/turbosmtp-email-validator-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'turbosmtpEmailValidator', [
			'disconnect_account_confirm_message' => esc_html__( "Are you sure you want to disconnect account?", "turbosmtp-email-validator" ),
			'ajax_disconnect_url'                => wp_nonce_url(
				add_query_arg( [
					'action' => 'turbosmtp-email-validator-disconnect'
				],
					admin_url( 'admin-ajax.php' )
				),
				'turbosmtp-email-validator-disconnect'
			),
			'ajax_get_email_details_url'                => wp_nonce_url(
				add_query_arg( [
					'action' => 'turbosmtp-email-validator_get_email_details'
				],
					admin_url( 'admin-ajax.php' )
				),
				'turbosmtp-email-validator_get_email_details'
			)
		] );

	}

	function get_emailvalidator_subscription(
		$refresh = false
	) {

		$transient_name = 'turbosmtp_email_validator_subscription';

		if ( ! $refresh && false !== ( $subscription = get_transient( $transient_name ) ) ) {
			return $subscription;
		}

		$subscription = $this->api->getSubscription();
		set_transient( $transient_name, $subscription, 12 * HOUR_IN_SECONDS );

		return $subscription;

	}

	public function settings_page() {

		if ( ! $this->api->hasApiKeys() || ! $this->api->isValid() ) {
			include_once plugin_dir_path( TURBOSMTP_EMAIL_VALIDATOR_PATH ) . 'admin/partials/logged-out.php';
		} else {

			$user_info = $this->api->getUserInfo();

			$has_api_keys = $this->api->hasApiKeys() && get_option( 'turbosmtp_email_validator_enabled' ) === 'yes';
			$subscription = $this->get_emailvalidator_subscription( isset( $_REQUEST['refresh'] ) );

			$subpage = sanitize_text_field( $_GET['subpage'] );


			include_once plugin_dir_path( TURBOSMTP_EMAIL_VALIDATOR_PATH ) . 'admin/partials/turbosmtp-email-validator-admin-display.php';
		}

	}


	public function turbosmtp_email_validator_general_settings_section_callback() {
		esc_html_e( 'General Settings info', 'turbosmtp-email-validator' );
	}

	public function turbosmtp_email_validator_validation_settings_section_callback() {
		esc_html_e( "Define on which forms would you like to apply email validation, and email statuses that should pass validation." );
	}

	public function turbosmtp_email_validator_enabled_callback() {
		$enabled = get_option( 'turbosmtp_email_validator_enabled', 'no' );

		?>
        <fieldset>
            <label for="turbosmtp_email_validator_enabled">
                <input type="checkbox" id="turbosmtp_email_validator_enabled" name="turbosmtp_email_validator_enabled"
                       value="yes" <?php checked( $enabled, 'yes' ); ?>>
				<?php esc_html_e( "Check to enable real time email verification on forms submit", "turbosmtp-email-validator" ); ?>
            </label>
        </fieldset>
		<?php
	}

	public function turbosmtp_email_validator_api_timeout_callback() {
		$api_timeout = get_option( 'turbosmtp_email_validator_api_timeout', 5 );
		?>
        <div style="display: flex; align-items: center; gap: 10px;">
            <input type="number" style="max-width: 100px" min="1" name="turbosmtp_email_validator_api_timeout"
                   id="turbosmtp_email_validator_api_timeout" value="<?php echo esc_attr( $api_timeout ) ?>">
			<?php esc_html_e( "seconds", "turbosmtp-email-validator" ); ?>
        </div>
        <p>Description</p>
		<?php
	}

	public function turbosmtp_email_validator_error_message_callback(
		$arguments
	) {
		echo '<input type="text" name="turbosmtp_email_validator_error_message" value="' . esc_attr( $arguments['value'] ) . '" class="regular-text">';
	}

	/**
	 * Register the shortcut for the settings area.
	 *
	 * @since    1.0.0
	 */
	public function settings_link( $links, $file ) {
		if ( is_network_admin() ) {
			$settings_url = network_admin_url( 'admin.php?page=turbosmtp-email-validator' );
		} else {
			$settings_url = admin_url( 'admin.php?page=turbosmtp-email-validator' );
		}

		$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . __( 'Settings', 'turbosmtp-email-validator' ) . '</a>';
		array_unshift( $links, $settings_link );

		$credits_link = '<a style="font-weight: bold;" href="https://serversmtp.com/email-validation-tool/" target="_blank">' . __( 'Buy Credits', 'turbosmtp-email-validator' ) . '</a>';
		array_unshift( $links, $credits_link );

		return $links;
	}

	public function login_handler() {

		$consumer_key    = sanitize_text_field( $_POST['consumer_key'] );
		$consumer_secret = sanitize_text_field( $_POST['consumer_secret'] );

		if ( ! isset( $_POST['_tsnonce'] ) || ! wp_verify_nonce( $_POST['_tsnonce'], 'turbosmtp-email-validator-login' ) ) {
			wp_redirect( add_query_arg( 'login_error', 'invalid_nonce', wp_get_referer() ) );
			exit;
		}

		if ( ! $this->api->isValid( $consumer_key, $consumer_secret ) ) {
			wp_redirect( add_query_arg( 'login_error', 'invalid_credentials', wp_get_referer() ) );
			exit;
		}

		update_option( 'turbosmtp_email_validator_consumer_key', $consumer_key );
		update_option( 'turbosmtp_email_validator_consumer_secret', $consumer_secret );
		update_option( 'turbosmtp_email_validator_enabled', 'yes' );

		activate_turbosmtp_email_validator();

		wp_redirect( add_query_arg( 'refresh', 1, remove_query_arg( 'login_error', wp_get_referer() ) ) );

		exit;

	}

	public function turbosmtp_email_validator_forms_callback(
		$arguments
	) {

		if ( empty( $arguments['value'] ) ) {
			$arguments['value'] = [];
		}
		$options_markup = '';
		$iterator       = 0;
		foreach ( $arguments['options'] as $key => $label ) {
			$iterator ++;
			$options_markup .= sprintf(
				'<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>',
				esc_html( $arguments['id'] ),
				'checkbox',
				esc_html( $key ),
				checked( $arguments['value'][ @array_search( $key, $arguments['value'], true ) ] ?? false, $key, false ),
				esc_html( $label ),
				$iterator
			);
		}

		echo $arguments['prepend'];

		printf( '<fieldset>%s</fieldset>', $options_markup );
	}


	public function sanitize_api_timeout( $input ) {
		$int_value = intval( $input );
		if ( $int_value < 1 || $int_value > 60 ) {
			$int_value = 5;
		}

		return $int_value;
	}


	function sanitize_validation_forms( $input ) {
		$allowed_validation_forms = turbosmtp_email_validator_validation_forms( true );

		return turbosmtp_email_validator_sanitize_array( $input, $allowed_validation_forms );
	}

	function sanitize_validation_pass( $input ) {
		$allowed_statuses = turbosmtp_email_validator_validation_statuses( true );

		return turbosmtp_email_validator_sanitize_array( $input, $allowed_statuses );
	}

	function settings_init() {

		// API credentials settings

		register_setting( 'turbosmtp_email_validator_general_settings', 'turbosmtp_email_validator_enabled', [
			'type'              => 'boolean',
			'sanitize_callback' => 'sanitize_text_field',
		] );
		register_setting( 'turbosmtp_email_validator_general_settings', 'turbosmtp_email_validator_api_timeout', [
			'type'              => 'integer',
			'sanitize_callback' => [ $this, 'sanitize_api_timeout' ]
		] );

		add_settings_section(
			'turbosmtp_email_validator_settings_section',
			__( 'General Settings', "turbosmtp-email-validator" ),
			[ $this, 'turbosmtp_email_validator_general_settings_section_callback' ],
			'email-general-settings'
		);

		add_settings_field(
			'turbosmtp_email_validator_enabled',
			__( 'Enable Email Validation', "turbosmtp-email-validator" ),
			[ $this, 'turbosmtp_email_validator_enabled_callback' ],
			'email-general-settings',
			'turbosmtp_email_validator_settings_section'
		);
		add_settings_field(
			'turbosmtp_email_validator_api_timeout',
			__( 'API timeout', "turbosmtp-email-validator" ),
			[ $this, 'turbosmtp_email_validator_api_timeout_callback' ],
			'email-general-settings',
			'turbosmtp_email_validator_settings_section'
		);

		// Validation forms settings

		register_setting( 'turbosmtp_email_validator_general_settings', 'turbosmtp_email_validator_validation_forms', [
			'type'              => 'array',
			'sanitize_callback' => [ $this, 'sanitize_validation_forms' ],
		] );
		register_setting( 'turbosmtp_email_validator_general_settings', 'turbosmtp_email_validator_validation_pass', [
			'type'              => 'array',
			'sanitize_callback' => [ $this, 'sanitize_validation_pass' ],
		] );
		register_setting( 'turbosmtp_email_validator_general_settings', 'turbosmtp_email_validator_error_message', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		] );

		add_settings_section(
			'turbosmtp_email_validator_validation_settings_section',
			__( 'Email Validation Settings', "turbosmtp-email-validator" ),
			[ $this, 'turbosmtp_email_validator_validation_settings_section_callback' ],
			'email-validation-settings'
		);

		add_settings_field(
			'turbosmtp_email_validator_validation_forms',
			__( 'Forms to be validated', 'turbosmtp-email-validator' ),
			[ $this, 'turbosmtp_email_validator_forms_callback' ],
			'email-validation-settings',
			'turbosmtp_email_validator_validation_settings_section',
			[
				'id'      => 'turbosmtp_email_validator_validation_forms',
				'options' => turbosmtp_email_validator_validation_forms(),
				'value'   => get_option( 'turbosmtp_email_validator_validation_forms' )
			]
		);

		add_settings_field(
			'turbosmtp_email_validator_validation_pass',
			__( 'Validation pass', 'turbosmtp-email-validator' ),
			[ $this, 'turbosmtp_email_validator_forms_callback' ],
			'email-validation-settings',
			'turbosmtp_email_validator_validation_settings_section',
			[
				'id'      => 'turbosmtp_email_validator_validation_pass',
				'options' => turbosmtp_email_validator_validation_statuses(),
				'value'   => get_option( 'turbosmtp_email_validator_validation_pass' ),
				'prepend' => '<p style="margin-bottom: 1rem;">' .
				             esc_html__( "Define which email statuses should be considered valid.", "turbosmtp-email-validator" ) .
				             ' <a href="https://serversmtp.com/email-validation-tool/" target="_blank">' .
				             esc_html__( "More info about statuses here", "turbosmtp-email-validator" ) .
				             '</a></p>'
			]
		);

		add_settings_field(
			'turbosmtp_email_validator_error_message',
			__( 'Custom error message', 'turbosmtp-email-validator' ),
			[ $this, 'turbosmtp_email_validator_error_message_callback' ],
			'email-validation-settings',
			'turbosmtp_email_validator_validation_settings_section',
			[
				'id'    => 'turbosmtp_email_validator_error_message',
				'value' => get_option( 'turbosmtp_email_validator_error_message' )
			]
		);

	}


	public function settings_menu() {

		add_options_page(
			__( 'Email Validation Settings', 'turbosmtp-email-validator' ),
			'Email Validation',
			'manage_options',
			'turbosmtp-email-validator',
			[ $this, 'settings_page' ]
		);

	}

}
