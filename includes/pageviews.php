<?php

function cgc_track_pageviews() {

	global $edd_options;
	$checkout = $edd_options['purchase_page'];
	$purchase_history_page =$edd_options['purchase_history_page'];

	$properties = array();

	/* Ecommerce pages */
	if( is_page( $checkout ) ) {
		cgcSegment::page( 'Checkout', $properties );
	}
	if( is_page( $purchase_history_page ) && ! empty( $edd_options['purchase_history_page'] ) ) {
		cgcSegment::page( 'Purchase History', $properties );
	}

	if( is_page( 'membership' ) ) {
		cgcSegment::page( 'Membership', $properties );
	}
	if( is_page( 'registration' ) ) {
		cgcSegment::page( 'Registration', $properties );
	}
	// Hard coding Blender Baiscs for now, will later replace with admin area.
	if( is_single( 'blender-basics-introduction-for-beginners' ) ) {
		cgcSegment::page( 'Blender Basics', $properties);
	}

}

add_action( 'wp_head', 'cgc_track_pageviews' );