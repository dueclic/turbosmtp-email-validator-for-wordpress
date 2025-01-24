<?php


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Validated_Emails_Table extends WP_List_Table {
	function __construct() {
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
			return '<span style="font-weight:bold;color: ' . ( $value === 'valid' ? 'green' : 'red' ) . ';">' . strtoupper( $value ) . '</span>';
		}

		return $value;
	}

	function get_columns() {
		return array(
			'email'        => __( 'Email', 'turbosmtp-email-validator-for-woocommerce' ),
			'status'       => __( 'Status', 'turbosmtp-email-validator-for-woocommerce' ),
			'validated_at' => __( 'Validated At', 'turbosmtp-email-validator-for-woocommerce' ),
			'raw_data'     => __( 'Raw Data', 'turbosmtp-email-validator-for-woocommerce' ),
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
				[ ['status' => 'all', 'total' =>  $wpdb->get_var( "SELECT COUNT(id) FROM $table_name") ]],
				$wpdb->get_results( "SELECT status, COUNT(*) AS total FROM $table_name GROUP BY status", ARRAY_A )
			);

		$status_links = [];

		foreach ($statuses as $status) {
			$status_links[$status['status']] = sprintf(__('<a href="%s">%s (%d)</a>', 'turbosmtp-email-validator-for-woocommerce'), admin_url( 'options-general.php?page=email-validation-settings&status='.$status['status'] ), ucfirst($status['status']), $status['total']);
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
		$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'validated_at';
		$order   = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array(
				'asc',
				'desc'
			) ) ) ? $_REQUEST['order'] : 'desc';
		$search  = ( isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '' );

		$whereStatus = "";
		if (isset($_GET['status']) && $_GET['status'] != 'all') {
			$whereStatus = "AND status LIKE '" . $wpdb->esc_like( $_REQUEST['status'] ) . "%'";
		}

		$total_items = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $table_name WHERE email LIKE %s".$whereStatus, '%' . $search . '%' ) );
		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE email LIKE %s ".$whereStatus." ORDER BY $orderby $order LIMIT %d OFFSET %d", '%' . $search . '%', $per_page, $paged ), ARRAY_A );

		$this->set_pagination_args( array(
			'total_items' => $total_items, // total items defined above
			'per_page'    => $per_page, // per page constant defined at top of method
			'total_pages' => ceil( $total_items / $per_page ) // calculate pages count
		) );


	}
}
