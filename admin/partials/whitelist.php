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
<div id="tsev-validator">
    <h1><?php esc_html_e( "Whitelist", "turbosmtp-email-validator" ); ?></h1>
    <form method="post" action="<?php echo admin_url( "options.php" ); ?>">

    <?php
	settings_fields( 'turbosmtp_email_validator_whitelist_settings' );
	?>
    <div class="tsev-form card accordion-container">
		<?php
		do_settings_sections( 'email-validation-whitelist' );
		?>
    </div>
	<?php

	submit_button();
	?>
    </form>
</div>
