<div id="tsev-validator">
	<h1><?php esc_html_e("Test Validator", "turbosmtp-email-validator"); ?></h1>
	<p>Description</p>
    <div class="tsev-form tsev-form-validator card accordion-container tsev-text-center">
        <form method="post" action="">
            <p>Insert the email you want to validate:</p>
            <div class="tsev-validator-input">
                <input type="email" name="test_email" class="tsev-validator-email" value="" required>
                <?php
                submit_button(
                    __( "Verify now", "turbosmtp-email-validator" )
                );
                ?>
            </div>
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
</div>
