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
