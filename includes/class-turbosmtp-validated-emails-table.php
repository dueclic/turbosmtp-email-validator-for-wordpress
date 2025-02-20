<?php


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Turbosmtp_Validated_Emails_Table extends WP_List_Table {

	private $validationPass;
	function __construct() {

		$this->validationPass =  get_option( 'turbosmtp_email_validator_validation_pass' );

		parent::__construct( array(
			'singular' => 'validated_email',
			'plural'   => 'validated_emails',
			'ajax'     => false
		) );
	}

	function column_default( $item, $column_name ) {
		$value = $item[ $column_name ];
		if ( $column_name === 'raw_data' ) {
			return '<textarea style="width:100%; height:80px">' . $value . '</textarea>';
		} else if ( $column_name === 'status' ) {

			return '<p><span style="font-weight:bold;color: ' . ( $value === 'valid' ? 'green' : 'red' ) . ';">' . strtoupper( $value ) . '</span>' .
			       ( turbosmtp_email_validator_status_ok( $value, $this->validationPass ) ? '<span class="tooltip dashicons dashicons-info"><span class="tooltip-text">'.__('It should be considered as Valid, due Validation Pass', 'turbosmtp-email-validator').'</span></span></p>' : '');
		} else if ( $column_name === 'source' ) {
			switch ( $value ) {
				case "wordpressisemail":
					$value = __( "WordPress Comments", "turbosmtp-email-validator" );
					break;
				case "testemail":
					$value = __( "Test Email", "turbosmtp-email-validator" );
					break;
				case "woocommerceregistration":
					$value = __( "WooCommerce Registration", "turbosmtp-email-validator" );
					break;
				case "woocommercecheckout":
					$value = __( "WooCommerce Checkout", "turbosmtp-email-validator" );
					break;
				case "wordpressregister":
					$value = __( "WordPress Registration", "turbosmtp-email-validator" );
					break;
				case "wpforms":
					$value = __( "WPForms", "turbosmtp-email-validator" );
					break;
				case "cf7forms":
					$value = __( "Contact Form 7", "turbosmtp-email-validator" );
					break;
				case "wordpressmultisiteregister":
					$value = __( "WordPress Multi Site Registration", "turbosmtp-email-validator" );
					break;
				case "mc4wp_mailchimp":
					$value = __( "MC4WP: Mailchimp for WordPress", "turbosmtp-email-validator" );
					break;
				case "gravity_forms":
					$value = __( "Gravity Forms", "turbosmtp-email-validator" );
					break;
				case "elementor_forms":
					$value = __( "Elementor Forms", "turbosmtp-email-validator" );
					break;
				default:
					$value = __( "Unknown", "turbosmtp-email-validator" );
					break;
			}
		}

		return $value;
	}

	function get_columns() {
		return array(
			'email'        => __( 'Email', 'turbosmtp-email-validator' ),
			'source'       => __( 'Source', 'turbosmtp-email-validator' ),
			'status'       => __( 'Status', 'turbosmtp-email-validator' ),
			'validated_at' => __( 'Validated At', 'turbosmtp-email-validator' ),
			'raw_data'     => __( 'Raw Data', 'turbosmtp-email-validator' ),
		);
	}

	protected function get_sortable_columns() {
		return array(
			'email'        => 'email',
			'status'       => 'status',
			'validated_at' => array( 'validated_at', true )
		);
	}

	protected function get_views() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'validated_emails';

		$statuses =
			array_merge(
				[ [ 'status' => 'all', 'total' => $wpdb->get_var( "SELECT COUNT(id) FROM $table_name" ) ] ],
				$wpdb->get_results( "SELECT status, COUNT(*) AS total FROM $table_name GROUP BY status", ARRAY_A )
			);

		$status_links = [];

		foreach ( $statuses as $status ) {
			$is_status_selected                = ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] === $status['status'] );
			$status_links[ $status['status'] ] = sprintf( '<a style="' . ( $is_status_selected ? 'font-weight:bold' : '' ) . '" href="%s">%s (%d)</a>', admin_url( 'options-general.php?page=turbosmtp-email-validator&subpage=history&status=' . $status['status'] ), ucfirst( $status['status'] ), $status['total'] );
		}

		return $status_links;

	}

	function prepare_items() {

		global $wpdb;
		$table_name = $wpdb->prefix . 'validated_emails';

		$per_page = 10;

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		// here we configure table headers, defined in our methods
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// will be used in pagination settings

		$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] - 1 ) * $per_page ) : 0;
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? sanitize_key( $_REQUEST['orderby'] ) : 'validated_at';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array(
				'asc',
				'desc'
			) ) ) ? sanitize_key( $_REQUEST['order'] ) : 'desc';
		$search  = ( isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '' );

		$status = isset( $_GET['status'] ) && in_array( $_GET['status'], turbosmtp_email_validator_validation_statuses( true ), true ) ? sanitize_key( $_GET['status'] ) : 'all'; // Sanitize e valida

		$whereStatus = "";
		if ( $status != 'all' ) {
			$whereStatus = $wpdb->prepare( "AND status = %s", $status );
		}

		$total_items = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $table_name WHERE email LIKE %s" . $whereStatus, '%' . $search . '%' ) );
		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE email LIKE %s " . $whereStatus . " ORDER BY $orderby $order LIMIT %d OFFSET %d", '%' . $search . '%', $per_page, $paged ), ARRAY_A );

		$this->set_pagination_args( array(
			'total_items' => $total_items, // total items defined above
			'per_page'    => $per_page, // per page constant defined at top of method
			'total_pages' => ceil( $total_items / $per_page ) // calculate pages count
		) );


	}
}
