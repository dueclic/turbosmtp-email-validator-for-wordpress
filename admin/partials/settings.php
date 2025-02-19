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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <!-- User connected START -->

    <div class="tsev-main-container">
        <div class="tsev-main-account">
			<?php
			include_once( plugin_dir_path( TURBOSMTP_EMAIL_VALIDATOR_PATH ) . 'admin/partials/account.php' );
			?>
        </div>
        <div class="tsev-main-forms">
            <nav class="nav-tab-wrapper">
                <a href="#tsev-settings" class="nav-tab">Settings</a>
                <a href="#tsev-validator" class="nav-tab">Validator</a>
                <a href="#tsev-log" class="nav-tab">History</a>
            </nav>

            <div id="tsev-tabs">
                <div id="tsev-settings">
                    <form method="post" action="options.php">
		                <?php
		                settings_fields( 'turbosmtp_email_validator_general_settings' );
		                do_settings_sections( 'email-validation-settings' );
		                submit_button();
		                ?>
                    </form>
                </div>
            </div>

        </div>


    </div>


</div>
