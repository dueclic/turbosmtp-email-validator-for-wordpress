<div id="tsev-validator">
	<h1><?php esc_html_e("Test Validator", "turbosmtp-email-validator"); ?></h1>
	<p>Description</p>
    <div class="tsev-form tsev-form-validator card accordion-container tsev-text-center">

        <?php
        $test_email = "";
        if ( isset( $_POST['test_email'] ) ) {
            $test_email        = sanitize_email( $_POST['test_email'] );

            $validation_result = "";

            $validation_result = apply_filters( 'turbosmtp_email_validator_checkemail', $validation_result, $test_email );

            if ( is_wp_error( $validation_result ) ) {
                ?>

                <h1 class="tsev-validator-invalid"><?php echo $test_email; ?></h1>
                <h2 class="tsev-validator-invalid"><?php echo esc_html( $validation_result->get_error_message() ); ?></h2>

            <?php } else { ?>

                <h2 class="tsev-validator-valid"><?php echo $test_email; ?></h2>
                <h2 class="tsev-validator-valid"><?php echo esc_html__('Email is valid', 'turbosmtp-email-validator'); ?></h2>

            <?php } ?>
            <hr class="tsev-hr-separator">
        <?php
        }
        ?>

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
    </div>
</div>
