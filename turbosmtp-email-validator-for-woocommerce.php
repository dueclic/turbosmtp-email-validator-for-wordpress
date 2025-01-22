<?php
/*
Plugin Name: turboSMTP Email Validator for WooCommerce
Description: Validates email address during WooCommerce registration using turboSMTP API.
Version: 1.0
Author: debba
Author URI: https://www.debbaweb.it
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once( dirname( __FILE__ ) . "/includes/class-validated-emails-table.php" );

register_activation_hook( __FILE__, 'create_email_validation_table' );

function create_email_validation_table() {
	global $wpdb;
	$table_name      = $wpdb->prefix . 'validated_emails';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
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

add_action( 'woocommerce_register_post', 'validate_email_with_turboSMTP', 10, 3 );

add_action( 'woocommerce_after_checkout_validation', 'validate_email_on_checkout', 10, 2 );

function validate_email_with_turboSMTP( $username, $email, $validation_errors ) {
	$validation_result = validate_email( $email );
	if ( is_wp_error( $validation_result ) && $validation_result->get_error_code() === 'email_validation_error' ) {
		$validation_errors->add( 'email_validation_error', $validation_result->get_error_message() );
	}
}

function validate_email_on_checkout( $data, $errors ) {
	$email = isset( $data['billing_email'] ) ? sanitize_email( $data['billing_email'] ) : '';
	if ( empty( $email ) ) {
		$errors->add( 'billing_email', __( 'Please provide a valid email', 'turbosmtp-email-validator-for-woocommerce' ) );

		return;
	}

	$validation_result = validate_email( $email );
	if ( is_wp_error( $validation_result ) && $validation_result->get_error_code() === 'email_validation_error' ) {
		$errors->add( 'billing_email', $validation_result->get_error_message() );
	}
}

function get_emailvalidator_subscription( $refresh = false ) {
	// Nome del transient
	$transient_name = 'turbosmtp_email_validator_subscription';

	// Controlla se il transient esiste e non è scaduto
	if ( ! $refresh && false !== ( $subscription = get_transient( $transient_name ) ) ) {
		return $subscription;
	}

	$consumer_key    = get_option( 'email_validation_consumer_key', '' );
	$consumer_secret = get_option( 'email_validation_consumer_secret', '' );

	$api_url = 'https://pro.api.serversmtp.com/api/v2/emailvalidation/subscription';

	$response = wp_remote_get( $api_url, array(
		'headers' => array(
			'accept'         => 'application/json',
			'Content-Type'   => 'application/json',
			'consumerKey'    => $consumer_key,
			'consumerSecret' => $consumer_secret,
		),
	) );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$body         = wp_remote_retrieve_body( $response );
	$subscription = json_decode( $body, true );

	set_transient( $transient_name, $subscription, 12 * HOUR_IN_SECONDS );

	return $subscription;
}

function validate_email( $email ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'validated_emails';
	$enabled    = get_option( 'email_validation_enabled', 'no' );

	if ( $enabled !== 'yes' ) {
		return true;
	}

	$consumer_key    = get_option( 'email_validation_consumer_key', '' );
	$consumer_secret = get_option( 'email_validation_consumer_secret', '' );

	$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE email = %s", $email ) );

	if ( $result ) {
		$validated_at = strtotime( $result->validated_at );
		$current_time = time();
		$six_months   = 6 * 30 * 24 * 60 * 60;

		if ( ( $current_time - $validated_at ) < $six_months ) {
			if ( $result->status === 'valid' ) {
				return true;
			} else {
				return new WP_Error( 'email_validation_error', __( "The email entered is not valid. Please enter a valid email.", 'turbosmtp-email-validator-for-woocommerce' ) );
			}
		} else {
			// L'email è scaduta, eliminata dalla tabella
			$wpdb->delete( $table_name, array( 'email' => $email ) );
		}
	}

	$api_url = 'https://pro.api.serversmtp.com/api/v2/emailvalidation/validateEmail';

	$response = wp_remote_post( $api_url, array(
		'headers' => array(
			'accept'         => 'application/json',
			'Content-Type'   => 'application/json',
			'consumerKey'    => $consumer_key,
			'consumerSecret' => $consumer_secret,
		),
		'body'    => json_encode( array( 'email' => $email ) ),
	) );

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'email_validation_error_api_error', __( 'Errore nella validazione dell\'email. Riprova più tardi.', 'turbosmtp-email-validator-for-woocommerce' ), $response->get_error_message() );
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( isset( $data['status'] ) ) {
		$wpdb->insert( $table_name, array(
			'email'        => $email,
			'status'       => $data['status'],
			'validated_at' => current_time( 'mysql' ),
			'raw_data'     => $body,
		) );

		if ( $data['status'] === 'valid' ) {
			return true;
		} else {
			return new WP_Error( 'email_validation_error', __( "The email entered is not valid. Please enter a valid email.", 'turbosmtp-email-validator-for-woocommerce' ) );
		}
	} else {
		return new WP_Error( 'email_validation_error_invalid_authorization', __( "Error in email validation. Please try again later.", 'turbosmtp-email-validator-for-woocommerce' ), $data );
	}
}

add_filter( 'woocommerce_registration_errors', 'customize_registration_errors', 10, 3 );

function customize_registration_errors( $errors, $username, $email ) {
	if ( isset( $errors->errors['email_validation_error'] ) ) {
		wc_add_notice( __( "The email entered is not valid. Please enter a valid email.", 'turbosmtp-email-validator-for-woocommerce' ), 'error' );
	}

	return $errors;
}

// Aggiungi opzioni nella pagina di amministrazione
add_action( 'admin_menu', 'email_validation_settings_menu' );

function email_validation_settings_menu() {
	add_options_page(
		'Email Validation Settings',
		'Email Validation',
		'manage_options',
		'email-validation-settings',
		'email_validation_settings_page'
	);
}

function email_validation_settings_page() {
	$subcription = get_emailvalidator_subscription(isset($_REQUEST['refresh']));
	?>
    <div class="wrap">
        <h1><?php _e( "Email Validation Settings", "turbosmtp-email-validator-for-woocommerce" ); ?></h1>
        <form method="post" action="options.php">
			<?php
			settings_fields( 'email_validation_settings_group' );
			do_settings_sections( 'email-validation-settings' );
			submit_button();
			?>
        </form>
        <form method="get" action="">
            <input type="hidden" name="refresh" value="1">
            <input type="hidden" name="page" value="<?php echo sanitize_text_field($_REQUEST['page']) ;?>">
            <h2><?php _e( "Current Subscription", "turbosmtp-email-validator-for-woocommerce" ); ?></h2>
            <p>
                <strong><?php _e( "Remaining Paid Credits", "turbosmtp-email-validator-for-woocommerce" ); ?></strong>: <?php echo $subcription['paid_credits']; ?>
            </p>
            <p>
                <strong><?php _e( "Remaining Free Credits", "turbosmtp-email-validator-for-woocommerce" ); ?></strong>: <?php echo $subcription['remaining_free_credit']; ?>
            </p>
	        <?php
	        submit_button(
		        __( "Refresh subscription", "turbosmtp-email-validator-for-woocommerce" )
	        );
	        ?>
        </form>
        <h2>Test Validator</h2>
        <form method="post" action="">
            <input type="email" name="test_email" value="" required>
			<?php
			submit_button(
				__( "Verify now", "turbosmtp-email-validator-for-woocommerce" )
			);
			?>
        </form>
		<?php
		if ( isset( $_POST['test_email'] ) ) {
			$test_email        = sanitize_email( $_POST['test_email'] );
			$validation_result = validate_email( $test_email );
			if ( is_wp_error( $validation_result ) ) {
				echo '<div style="color: red;">' . esc_html( $validation_result->get_error_message() ) . '</div>';
			} else {
				echo '<div style="color: green;">L\'email è valida!</div>';
			}
		}
		?>
        <h2>Validated Emails</h2>
		<?php display_validated_emails_table(); ?>
    </div>
	<?php
}

add_action( 'admin_init', 'email_validation_settings_init' );

function email_validation_settings_init() {
	register_setting( 'email_validation_settings_group', 'email_validation_enabled' );
	register_setting( 'email_validation_settings_group', 'email_validation_consumer_key' );
	register_setting( 'email_validation_settings_group', 'email_validation_consumer_secret' );

	add_settings_section(
		'email_validation_settings_section',
		__( 'Email Validation Settings', "turbosmtp-email-validator-for-woocommerce" ),
		'email_validation_settings_section_callback',
		'email-validation-settings'
	);

	add_settings_field(
		'email_validation_enabled',
		__( 'Enable Email Validation', "turbosmtp-email-validator-for-woocommerce" ),
		'email_validation_enabled_callback',
		'email-validation-settings',
		'email_validation_settings_section'
	);

	add_settings_field(
		'email_validation_consumer_key',
		__( 'Consumer Key', "turbosmtp-email-validator-for-woocommerce" ),
		'email_validation_consumer_key_callback',
		'email-validation-settings',
		'email_validation_settings_section'
	);

	add_settings_field(
		'email_validation_consumer_secret',
		__( 'Consumer Secret', "turbosmtp-email-validator-for-woocommerce" ),
		'email_validation_consumer_secret_callback',
		'email-validation-settings',
		'email_validation_settings_section'
	);
}

function email_validation_settings_section_callback() {
	echo 'Configure the settings for email validation.';
}

function email_validation_enabled_callback() {
	$enabled = get_option( 'email_validation_enabled', 'no' );
	echo '<input type="checkbox" name="email_validation_enabled" value="yes"' . checked( $enabled, 'yes', false ) . '> Enable Email Validation';
}

function email_validation_consumer_key_callback() {
	$consumer_key = get_option( 'email_validation_consumer_key', '' );
	echo '<input type="text" name="email_validation_consumer_key" value="' . esc_attr( $consumer_key ) . '" class="regular-text">';
}

function email_validation_consumer_secret_callback() {
	$consumer_secret = get_option( 'email_validation_consumer_secret', '' );
	echo '<input type="text" name="email_validation_consumer_secret" value="' . esc_attr( $consumer_secret ) . '" class="regular-text">';
}

function display_validated_emails_table() {
	$validated_emails_table = new Validated_Emails_Table();
	$validated_emails_table->prepare_items();
	?>
    <form method="post">
		<?php $validated_emails_table->display(); ?>
    </form>
	<?php
}
