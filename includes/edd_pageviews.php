<?php

/*
	Cart Functions
*/
function cgc_track_edd_pages( $download_id ){

	global $edd_options;
	$checkout = $edd_options['purchase_page'];
	$purchase_history_page = $edd_options['purchase_history_page'];

	$properties = array();
	/* Ecommerce pages */
	if( is_page( $checkout ) && ! empty( $edd_options['purchase_page'] ) ) {
		cgcSegment::page( 'Checkout', $properties );
	}
	if( is_page( $purchase_history_page ) && ! empty( $edd_options['purchase_history_page'] ) ) {
		cgcSegment::page( 'Purchase History', $properties );
	}
}
add_action( 'wp_head', 'cgc_track_edd_pages' );
