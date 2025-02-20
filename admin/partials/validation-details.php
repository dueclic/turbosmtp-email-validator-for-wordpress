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

<div class="turbosmtp-validation-details">
	<h2><?php esc_html_e('Email Validation Details', 'turbosmtp-email-validator'); ?></h2>

    <?php
        foreach ($validation_details as $validation_kind => $validation_value):
            ?>
            <p><strong><?php echo $validation_kind; ?></strong> <?php echo esc_html($validation_value); ?></p>

        <?php
    endforeach;
    ?>

    <?php
        if ($is_modal):
    ?>
	<button class="turbosmtp-modal-close button"><?php esc_html_e('Close', 'turbosmtp-email-validator'); ?></button>
    <?php
    endif;
    ?>
</div>
