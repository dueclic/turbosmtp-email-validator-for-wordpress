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

register_activation_hook( __FILE__, 'create_email_validation_table' );

function create_email_validation_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'validated_emails';
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
    if ( is_wp_error( $validation_result ) ) {
        $validation_errors->add( 'email_validation_error', $validation_result->get_error_message() );
    }
}

function validate_email_on_checkout( $data, $errors ) {
	$email = isset( $data['billing_email'] ) ? sanitize_email( $data['billing_email'] ) : '';
	if ( empty( $email ) ) {
		$errors->add( 'billing_email', __( 'Per favore, inserisci un\'email valida.', 'woocommerce-turbosmtp-email-validator' ) );
		return;
	}

	$validation_result = validate_email( $email );
	if ( is_wp_error( $validation_result ) ) {
		$errors->add( 'billing_email', $validation_result->get_error_message() );
	}
}

function validate_email( $email ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'validated_emails';
    $enabled = get_option( 'email_validation_enabled', 'no' );

    if ( $enabled !== 'yes' ) {
        return true;
    }

    $consumer_key = get_option( 'email_validation_consumer_key', '' );
    $consumer_secret = get_option( 'email_validation_consumer_secret', '' );

    $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE email = %s", $email ) );

    if ( $result ) {
        $validated_at = strtotime( $result->validated_at );
        $current_time = time();
        $six_months = 6 * 30 * 24 * 60 * 60;

        if ( ( $current_time - $validated_at ) < $six_months ) {
            if ( $result->status === 'valid' ) {
                return true; // L'email è valida e non è scaduta
            } else {
                return new WP_Error( 'email_validation_error', __( 'L\'email inserita non è valida. Per favore, inserisci un\'email valida.', 'woocommerce-turbosmtp-email-validator' ) );
            }
        } else {
            // L'email è scaduta, eliminata dalla tabella
            $wpdb->delete( $table_name, array( 'email' => $email ) );
        }
    }

    $api_url = 'https://pro.api.serversmtp.com/api/v2/emailvalidation/validateEmail';

    $response = wp_remote_post( $api_url, array(
        'headers' => array(
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'consumerKey' => $consumer_key,
            'consumerSecret' => $consumer_secret,
        ),
        'body' => json_encode( array( 'email' => $email ) ),
    ));

    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'email_validation_error', __( 'Errore nella validazione dell\'email. Riprova più tardi.', 'woocommerce-turbosmtp-email-validator' ) );
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( isset( $data['status'] ) ) {
        $wpdb->insert( $table_name, array(
            'email' => $email,
            'status' => $data['status'],
            'validated_at' => current_time( 'mysql' ),
            'raw_data' => $body,
        ));

        if ( $data['status'] === 'valid' ) {
            return true;
        } else {
            return new WP_Error( 'email_validation_error', __( 'L\'email inserita non è valida. Per favore, inserisci un\'email valida.', 'woocommerce-turbosmtp-email-validator' ) );
        }
    } else {
        return new WP_Error( 'email_validation_error', __( 'Errore nella validazione dell\'email. Riprova più tardi.', 'woocommerce-turbosmtp-email-validator' ) );
    }
}

add_filter( 'woocommerce_registration_errors', 'customize_registration_errors', 10, 3 );

function customize_registration_errors( $errors, $username, $email ) {
    if ( isset( $errors->errors['email_validation_error'] ) ) {
        wc_add_notice( __( 'L\'email inserita non è valida. Per favore, inserisci un\'email valida.', 'woocommerce-turbosmtp-email-validator' ), 'error' );
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
    ?>
    <div class="wrap">
        <h1>Email Validation Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'email_validation_settings_group' );
            do_settings_sections( 'email-validation-settings' );
            submit_button();
            ?>
        </form>
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
        'Email Validation Settings',
        'email_validation_settings_section_callback',
        'email-validation-settings'
    );

    add_settings_field(
        'email_validation_enabled',
        'Enable Email Validation',
        'email_validation_enabled_callback',
        'email-validation-settings',
        'email_validation_settings_section'
    );

    add_settings_field(
        'email_validation_consumer_key',
        'Consumer Key',
        'email_validation_consumer_key_callback',
        'email-validation-settings',
        'email_validation_settings_section'
    );

    add_settings_field(
        'email_validation_consumer_secret',
        'Consumer Secret',
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
    global $wpdb;
    $table_name = $wpdb->prefix . 'validated_emails';
    $results = $wpdb->get_results( "SELECT * FROM $table_name" );

    if ( $results ) {
        echo '<table class="widefat fixed" cellspacing="0">';
        echo '<thead><tr><th>Email</th><th>Status</th><th>Validated At</th><th>Raw Data</th></tr></thead>';
        echo '<tbody>';
        foreach ( $results as $row ) {
            echo '<tr>';
            echo '<td>' . esc_html( $row->email ) . '</td>';
            echo '<td>' . esc_html( $row->status ) . '</td>';
            echo '<td>' . esc_html( $row->validated_at ) . '</td>';
            echo '<td><textarea readonly rows="3" cols="50">' . esc_textarea( $row->raw_data ) . '</textarea></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No validated emails found.</p>';
    }
}
