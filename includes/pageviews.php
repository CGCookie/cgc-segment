<?php

function cgc_track_pageviews() {

	global $edd_options;
	$checkout = $edd_options['purchase_page'];
	$purchase_history_page =$edd_options['purchase_history_page'];

	$properties = array();

	if( is_page( $checkout ) ) {
		cgcSegment::page( 'Checkout', $properties );
	}
	if( is_page( $purchase_history_page ) && ! empty( $edd_options['purchase_history_page'] ) ) {
		cgcSegment::page( 'Purchase History', $properties );
	}

}

add_action( 'wp_head', 'cgc_track_pageviews' );