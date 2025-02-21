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
<div class="tsev-forms-logo">
    <img src="<?php echo plugins_url( '/admin/img/ts-logo.svg', TURBOSMTP_EMAIL_VALIDATOR_PATH ); ?>" alt="">
    <div class="tsev-account-status">
        <div><?php _e( "Account connected", "turbosmtp-email-validator" ); ?></div>
        <div class="tsev-account-connected"></div>
    </div>
</div>
<div class="tsev-account-info">
                <span class="flex-grow-1 truncate"
                      title="<?php echo esc_html( $user_info['email'] ); ?>"><strong><?php echo esc_html( $user_info['email'] ); ?></strong>
                </span>
    <span>
                    <a id="turbosmtp-disconnect" class="tsev-account-disconnect"
                       title="<?php esc_html_e( "Disconnect account", "turbosmtp-email-validator" ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path
                                    d="M280 24c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 240c0 13.3 10.7 24 24 24s24-10.7 24-24l0-240zM134.2 107.3c10.7-7.9 12.9-22.9 5.1-33.6s-22.9-12.9-33.6-5.1C46.5 112.3 8 182.7 8 262C8 394.6 115.5 502 248 502s240-107.5 240-240c0-79.3-38.5-149.7-97.8-193.3c-10.7-7.9-25.7-5.6-33.6 5.1s-5.6 25.7 5.1 33.6c47.5 35 78.2 91.2 78.2 154.7c0 106-86 192-192 192S56 368 56 262c0-63.4 30.7-119.7 78.2-154.7z"></path></svg>
                    </a>
                </span>
</div>
<!--hr class="tsev-hr-separator"-->
<?php
$has_credits = $subscription['remaining_free_credit'] || $subscription['paid_credits'];
?>
<form method="get" action="" class="card <?php echo $has_credits ?: "tsev-account-no-credits"; ?>">
    <input type="hidden" name="page" value="<?php echo esc_attr(sanitize_text_field( $_REQUEST['page'] )); ?>">
    <input type="hidden" name="refresh" value="1">
    <!--h3 class="tsev-text-center"><?php esc_html_e( "Current Subscription", "turbosmtp-email-validator" ); ?></h3-->
    <div class="tsev-credits-row">
        <strong><?php esc_html_e( "Paid Credits", "turbosmtp-email-validator" ); ?></strong> <span><?php echo esc_html($subscription['currency'] ?? ""); ?> <?php echo esc_html( $subscription['paid_credits'] ?? 0 ); ?></span>
    </div>
    <div class="tsev-credits-row">
        <strong><?php esc_html_e( "Free Credits", "turbosmtp-email-validator" ); ?></strong> <span><?php echo esc_html($subscription['remaining_free_credit'] ?? 0 ); ?></span>
    </div>
    <?php if (!$has_credits) { ?>
    <p class="tsev-validator-invalid tsev-text-center"><?php esc_html_e( "Validation is bypassed; all emails accepted due to zero credit balance.", "turbosmtp-email-validator" ); ?></p>
    <?php } ?>
    <div class="tsev-text-center submit">
        <button type="submit"  id="submit" class="button button-small button-secondary">
			<?php _e( "Refresh", "turbosmtp-email-validator" ); ?>
        </button>
        <a class="button button-small button-primary" href="https://dashboard.serversmtp.com/addons/emailvalidator" target="_blank">Buy credits</a>
    </div>
	<?php
	/*
	submit_button(
		__( "Refresh subscription", "turbosmtp-email-validator" )
	);
	*/
	?>
</form>
