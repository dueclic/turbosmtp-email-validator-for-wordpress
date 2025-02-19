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
                <a href="<?php echo admin_url( 'options-general.php?page=turbosmtp-email-validator&subpage=settings' ); ?>"
                   class="nav-tab <?php echo( $subpage !== 'validator' && $subpage !== 'history' ? 'nav-tab-active' : '' ); ?>">
					<?php esc_html_e( "Settings", "turbosmtp-email-validator" ); ?>
                </a>
                <a href="<?php echo admin_url( 'options-general.php?page=turbosmtp-email-validator&subpage=validator' ); ?>"
                   class="nav-tab <?php echo( $subpage === 'validator' ? 'nav-tab-active' : '' ); ?>">Validator</a>
                <a href="<?php echo admin_url( 'options-general.php?page=turbosmtp-email-validator&subpage=history' ); ?>"
                   class="nav-tab <?php echo( $subpage === 'history' ? 'nav-tab-active' : '' ); ?>">History</a>
            </nav>

            <div id="tsev-tabs">

				<?php
				switch ( $subpage ):
					case "validator":
						include_once plugin_dir_path( TURBOSMTP_EMAIL_VALIDATOR_PATH ) . 'admin/partials/validator.php';
						break;
					case "history":
						include_once plugin_dir_path( TURBOSMTP_EMAIL_VALIDATOR_PATH ) . "includes/class-turbosmtp-validated-emails-table.php";
						$validated_emails_table = new Turbosmtp_Validated_Emails_Table();
						include_once plugin_dir_path( TURBOSMTP_EMAIL_VALIDATOR_PATH ) . 'admin/partials/history.php';
						break;
					default:
						include_once plugin_dir_path( TURBOSMTP_EMAIL_VALIDATOR_PATH ) . 'admin/partials/settings.php';
						break;
				endswitch;
				?>

            </div>

        </div>


    </div>


</div>
