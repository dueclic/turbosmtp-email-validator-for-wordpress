<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.dueclic.com
 * @since      1.0.0
 *
 * @package    Turbosmtp_Email_Validator
 * @subpackage Turbosmtp_Email_Validator/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <!-- User NOT connected START -->

    <form action="" method="post" class="tsev-login-form" id="tsev-login-form">
        <div class="tsev-text-center">
            <img src="<?php echo plugins_url('/admin/img/ts-logo.svg', TURBOSMTP_EMAIL_VALIDATOR_PATH); ?>" class="tsev-login-logo" alt="">
        </div>
        <input type="hidden" name="option_page" value="pluginPage"><input type="hidden" name="action" value="update"><input type="hidden" id="_wpnonce" name="_wpnonce" value="2c3bcd0d5b"><input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=emailchef">
        <p class="tsev-text-center tsev-login-form-signup">
            Not a member? <a target="_blank" href="https://serversmtp.com/signup/">Sign up for free</a>.
        </p>
        <fieldset>
            <div class="tsev-login-form-control-group">
                <label for="consumer_key" class="tsev-login-form-get-api">
                    Consumer Key:
                    <a href="https://dashboard.serversmtp.com/settings/integrations" target="_blank" class="tsev-get-api">Get API Key</a>
                </label>
                <input class="tsev-input" type="text" value="" id="consumer_key" name="emailchef_settings[consumer_key]">
            </div>
            <div class="tsev-login-form-control-group tsev-login-form-password-field">
                <a id="showPassword" title="Show Consumer Secret">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                        <path d="M288 80c-65.2 0-118.8 29.6-159.9 67.7C89.6 183.5 63 226 49.4 256c13.6 30 40.2 72.5 78.6 108.3C169.2 402.4 222.8 432 288 432s118.8-29.6 159.9-67.7C486.4 328.5 513 286 526.6 256c-13.6-30-40.2-72.5-78.6-108.3C406.8 109.6 353.2 80 288 80zM95.4 112.6C142.5 68.8 207.2 32 288 32s145.5 36.8 192.6 80.6c46.8 43.5 78.1 95.4 93 131.1c3.3 7.9 3.3 16.7 0 24.6c-14.9 35.7-46.2 87.7-93 131.1C433.5 443.2 368.8 480 288 480s-145.5-36.8-192.6-80.6C48.6 356 17.3 304 2.5 268.3c-3.3-7.9-3.3-16.7 0-24.6C17.3 208 48.6 156 95.4 112.6zM288 336c44.2 0 80-35.8 80-80s-35.8-80-80-80c-.7 0-1.3 0-2 0c1.3 5.1 2 10.5 2 16c0 35.3-28.7 64-64 64c-5.5 0-10.9-.7-16-2c0 .7 0 1.3 0 2c0 44.2 35.8 80 80 80zm0-208a128 128 0 1 1 0 256 128 128 0 1 1 0-256z"></path>
                    </svg>
                </a>
                <a id="hidePassword" style="display: none" title="Hide Consumer Secret">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                        <path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zm151 118.3C226 97.7 269.5 80 320 80c65.2 0 118.8 29.6 159.9 67.7C518.4 183.5 545 226 558.6 256c-12.6 28-36.6 66.8-70.9 100.9l-53.8-42.2c9.1-17.6 14.2-37.5 14.2-58.7c0-70.7-57.3-128-128-128c-32.2 0-61.7 11.9-84.2 31.5l-46.1-36.1zM394.9 284.2l-81.5-63.9c4.2-8.5 6.6-18.2 6.6-28.3c0-5.5-.7-10.9-2-16c.7 0 1.3 0 2 0c44.2 0 80 35.8 80 80c0 9.9-1.8 19.4-5.1 28.2zm9.4 130.3C378.8 425.4 350.7 432 320 432c-65.2 0-118.8-29.6-159.9-67.7C121.6 328.5 95 286 81.4 256c8.3-18.4 21.5-41.5 39.4-64.8L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5l-41.9-33zM192 256c0 70.7 57.3 128 128 128c13.3 0 26.1-2 38.2-5.8L302 334c-23.5-5.4-43.1-21.2-53.7-42.3l-56.1-44.2c-.2 2.8-.3 5.6-.3 8.5z"></path>
                    </svg>
                </a>
                <label for="consumer_secret">Consumer Secret:</label>
                <input class="tsev-input" type="password" id="consumer_secret" value="" name="emailchef_settings[consumer_secret]">
            </div>
            <div class="tsev-text-center">
                <input type="button" id="tsev-login-submit" class="button button-primary" value="Login">
            </div>
        </fieldset>
        <div class="tsev-check-login-result notice notice-alt"></div>
    </form>

    <!-- User NOT connected END -->
    <!-- User connected START -->

    <?php
    if ($has_api_keys):
        ?>
    <div class="tsev-main-container">
        <div class="tsev-main-account">
            <div class="tsev-forms-logo">
                <img src="<?php echo plugins_url('/admin/img/ts-logo.svg', TURBOSMTP_EMAIL_VALIDATOR_PATH); ?>" alt="">
                <div class="tsev-account-status">
                    <div><?php _e( "Account connected", "turbosmtp-email-validator" ); ?></div>
                    <div class="tsev-account-connected"></div>
                </div>
            </div>
            <div class="tsev-account-info">
                <span class="flex-grow-1 truncate" title="[[alessandro@serversmtp.com]]"><strong>[[alessandro@serversmtp.com]]</strong>
                </span>
                    <span>
                    <a id="turbosmtp-disconnect" class="tsev-account-disconnect" title="<?php _e( "Disconnect account", "turbosmtp-email-validator" ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M280 24c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 240c0 13.3 10.7 24 24 24s24-10.7 24-24l0-240zM134.2 107.3c10.7-7.9 12.9-22.9 5.1-33.6s-22.9-12.9-33.6-5.1C46.5 112.3 8 182.7 8 262C8 394.6 115.5 502 248 502s240-107.5 240-240c0-79.3-38.5-149.7-97.8-193.3c-10.7-7.9-25.7-5.6-33.6 5.1s-5.6 25.7 5.1 33.6c47.5 35 78.2 91.2 78.2 154.7c0 106-86 192-192 192S56 368 56 262c0-63.4 30.7-119.7 78.2-154.7z"></path></svg>
                    </a>
                </span>
            </div>
            <hr class="tsev-hr-separator">
            <form method="get" action="">
                <input type="hidden" name="page" value="<?php echo esc_attr(sanitize_text_field( $_REQUEST['page'] )); ?>">
                <input type="hidden" name="refresh" value="1">
                <h3><?php esc_html_e( "Current Subscription", "turbosmtp-email-validator" ); ?></h3>
                <div class="tsev-credits-row">
                    <strong><?php esc_html_e( "Remaining Paid Credits", "turbosmtp-email-validator" ); ?></strong> <span><?php echo esc_html($subscription['currency'] ?? ""); ?> <?php echo esc_html( $subscription['paid_credits'] ?? 0 ); ?></span>
                </div>
                <div class="tsev-credits-row">
                    <strong><?php esc_html_e( "Remaining Free Credits", "turbosmtp-email-validator" ); ?></strong> <span><?php echo esc_html($subscription['remaining_free_credit'] ?? 0 ); ?></span>
                </div>
                <div class="tsev-text-center submit">
                    <button type="submit" name="submit" id="submit" class="button button-small button-secondary">
                        <?php _e( "Refresh subscription", "turbosmtp-email-validator" ); ?>
                    </button>
                </div>
                <?php
                /*
                submit_button(
                    __( "Refresh subscription", "turbosmtp-email-validator" )
                );
                */
                ?>
            </form>
        </div>
        <div class="tsev-main-forms">
            <nav class="nav-tab-wrapper">
                <a href="#tsev-settings" class="nav-tab">Settings</a>
                <a href="#tsev-validator" class="nav-tab">Validator</a>
                <a href="#tsev-log" class="nav-tab">History</a>
            </nav>

            <div id="tsev-tabs">
                <div id="tsev-settings">
                    <h1><?php _e( 'Email Validation Settings', "turbosmtp-email-validator" ); ?></h1>
                    <p>Description</p>
                    <form method="post" action="options.php">
                        <div class="tsev-form card accordion-container">
                            <h2>General settings</h2>
                            <p>General settings info</p>
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <th scope="row" class="titledesc">Enalble Email verification</th>
                                    <td class="forminp forminp-checkbox ">
                                        <fieldset>
                                            <!--legend class="screen-reader-text">
                                                <span>Check to enable real time email verification on forms submit</span>
                                            </legend-->
                                            <label for="turbosmtp_email_validator_enabled">
                                                <input type="checkbox" name="turbosmtp_email_validator_enabled" value="yes" checked="checked">
                                                Check to enable real time email verification on forms submit
                                            </label>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="titledesc">API timeout</th>
                                    <td class="forminp forminp-input">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <input type="number" style="max-width: 100px" min="1" name="wc_emailchef_cron_end_interval_value" id="wc_emailchef_cron_end_interval_value" value="1">
                                            hours (?)
                                        </div>
                                        <p>Description</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tsev-form card accordion-container">
                            <h2>Validation settings</h2>
                            <p>Define on which forms would you like to apply email validation, and email statuses that should pass validation.</p>
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <th scope="row">Forms to be validated</th>
                                    <td>
                                        <fieldset><label for="turbosmtp_email_validator_validation_forms_1"><input
                                                        id="turbosmtp_email_validator_validation_forms_1"
                                                        name="turbosmtp_email_validator_validation_forms[]"
                                                        type="checkbox" value="contact_form_7" checked="checked">
                                                Contact Form 7</label><br><label
                                                    for="turbosmtp_email_validator_validation_forms_2"><input
                                                        id="turbosmtp_email_validator_validation_forms_2"
                                                        name="turbosmtp_email_validator_validation_forms[]"
                                                        type="checkbox" value="wpforms" checked="checked">
                                                WPForms</label><br><label
                                                    for="turbosmtp_email_validator_validation_forms_3"><input
                                                        id="turbosmtp_email_validator_validation_forms_3"
                                                        name="turbosmtp_email_validator_validation_forms[]"
                                                        type="checkbox" value="woocommerce" checked="checked">
                                                WooCommerce</label><br><label
                                                    for="turbosmtp_email_validator_validation_forms_4"><input
                                                        id="turbosmtp_email_validator_validation_forms_4"
                                                        name="turbosmtp_email_validator_validation_forms[]"
                                                        type="checkbox" value="wordpress_comments" checked="checked">
                                                WordPress Comments</label><br><label
                                                    for="turbosmtp_email_validator_validation_forms_5"><input
                                                        id="turbosmtp_email_validator_validation_forms_5"
                                                        name="turbosmtp_email_validator_validation_forms[]"
                                                        type="checkbox" value="wordpress_registration"
                                                        checked="checked"> WordPress Registration</label><br><label
                                                    for="turbosmtp_email_validator_validation_forms_6"><input
                                                        id="turbosmtp_email_validator_validation_forms_6"
                                                        name="turbosmtp_email_validator_validation_forms[]"
                                                        type="checkbox" value="mc4wp_mailchimp" checked="checked">
                                                MC4WP: Mailchimp for WordPress</label><br><label
                                                    for="turbosmtp_email_validator_validation_forms_7"><input
                                                        id="turbosmtp_email_validator_validation_forms_7"
                                                        name="turbosmtp_email_validator_validation_forms[]"
                                                        type="checkbox" value="gravity_forms" checked="checked"> Gravity
                                                Forms</label><br><label
                                                    for="turbosmtp_email_validator_validation_forms_8"><input
                                                        id="turbosmtp_email_validator_validation_forms_8"
                                                        name="turbosmtp_email_validator_validation_forms[]"
                                                        type="checkbox" value="elementor_forms" checked="checked">
                                                Elementor Forms</label><br></fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Validation pass</th>
                                    <td>
                                        <p style="margin-bottom: 1rem;">Define which email statuses should be considered valid.
                                            <a href="https://serversmtp.com/email-validation-tool/" target="_blank">More info about statuses here</a>.
                                        </p>
                                        <fieldset><label for="turbosmtp_email_validator_validation_pass_1"><input
                                                        id="turbosmtp_email_validator_validation_pass_1"
                                                        name="turbosmtp_email_validator_validation_pass[]"
                                                        type="checkbox" value="valid" checked="checked">
                                                Valid</label><br><label
                                                    for="turbosmtp_email_validator_validation_pass_2"><input
                                                        id="turbosmtp_email_validator_validation_pass_2"
                                                        name="turbosmtp_email_validator_validation_pass[]"
                                                        type="checkbox" value="invalid"> Invalid</label><br><label
                                                    for="turbosmtp_email_validator_validation_pass_3"><input
                                                        id="turbosmtp_email_validator_validation_pass_3"
                                                        name="turbosmtp_email_validator_validation_pass[]"
                                                        type="checkbox" value="catch-all" checked="checked">
                                                Catch-All</label><br><label
                                                    for="turbosmtp_email_validator_validation_pass_4"><input
                                                        id="turbosmtp_email_validator_validation_pass_4"
                                                        name="turbosmtp_email_validator_validation_pass[]"
                                                        type="checkbox" value="unknown" checked="checked">
                                                Unknown</label><br><label
                                                    for="turbosmtp_email_validator_validation_pass_5"><input
                                                        id="turbosmtp_email_validator_validation_pass_5"
                                                        name="turbosmtp_email_validator_validation_pass[]"
                                                        type="checkbox" value="spamtrap"> Spamtrap</label><br><label
                                                    for="turbosmtp_email_validator_validation_pass_6"><input
                                                        id="turbosmtp_email_validator_validation_pass_6"
                                                        name="turbosmtp_email_validator_validation_pass[]"
                                                        type="checkbox" value="abuse"> Abuse</label><br><label
                                                    for="turbosmtp_email_validator_validation_pass_7"><input
                                                        id="turbosmtp_email_validator_validation_pass_7"
                                                        name="turbosmtp_email_validator_validation_pass[]"
                                                        type="checkbox" value="do_not_mail"> Do Not Mail</label><br>
                                        </fieldset>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>


                    </form>

                    <form method="post" action="options.php">
                        <?php
                        settings_fields( 'turbosmtp_email_validator_general_settings' );
                        do_settings_sections( 'email-validation-settings' );
                        submit_button();
                        ?>
                    </form>

                </div>
                <div id="tsev-validator">
                    <h2><?php esc_html_e("Test Validator", "turbosmtp-email-validator"); ?></h2>
                    <p>Description</p>
                    <form method="post" action="">
                        <input type="email" name="test_email" value="" required>
                        <?php
                        submit_button(
                            __( "Verify now", "turbosmtp-email-validator" )
                        );
                        ?>
                    </form>
                    <?php
                    if ( isset( $_POST['test_email'] ) ) {
                        $test_email        = sanitize_email( $_POST['test_email'] );

                        $validation_result = "";

                        $validation_result = apply_filters( 'turbosmtp_email_validator_checkemail', $validation_result, $test_email );
                        if ( is_wp_error( $validation_result ) ) {
                            echo '<div style="color: red;">' . esc_html( $validation_result->get_error_message() ) . '</div>';
                        } else {
                            echo '<div style="color: green;">'.esc_html__('Email is valid', 'turbosmtp-email-validator').'</div>';
                        }
                    }
                    ?>
                </div>
                <div id="tsev-log">
                    <h2><?php esc_html_e("Validated Emails", "turbosmtp-email-validator"); ?></h2>
                    <?php

                    $validated_emails_table->views();
                    ?>
                    <form method="post">
                        <?php
                        $validated_emails_table->prepare_items();
                        $validated_emails_table->search_box(
                            __( 'Search', 'turbosmtp-email-validator' ),
                            'search_id'
                        );
                        ?>
                        <?php $validated_emails_table->display(); ?>
                    </form>
                </div>
            </div>
        </div>







    </div>



    <?php
    endif;
    ?>

</div>
