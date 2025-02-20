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
    <p>Description</p>
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
