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
<div id="tsev-log">
	<h1><?php esc_html_e("Validated Emails", "turbosmtp-email-validator"); ?></h1>
    <p><?php esc_html_e("Welcome to your validation history overview. Below, you'll find a detailed record of all email validations performed, displaying outcomes, timestamps, status and API response details to help you track and manage your email verification processes effectively.", "turbosmtp-email-validator"); ?></p>
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
<div id="turbosmtp-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000;">
    <div class="turbosmtp-modal-content" style="background:#fff; padding:20px; max-width:600px; margin:50px auto; border-radius:5px;">
        <button class="turbosmtp-modal-close" style="float:right;">&times;</button>
        <div class="turbosmtp-modal-body"></div>
    </div>
</div>
