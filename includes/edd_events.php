<?php

/*
	Cart Functions
*/
function cgc_edd_get_product_category( $download_id ){
	$terms = get_the_terms( $download_id, 'download_category' );

	if ( $terms && ! is_wp_error( $terms ) ):
		$category_names = array();

		foreach( $terms as $term ) {
			$category_names[] = $term->name;
		}
	$categories = join( ", ", $category_names );
	return $categories;
	endif;
}

function cgc_edd_track_add_product_to_cart( $download_id, $options ) {

	$user_id = is_user_logged_in() ? get_current_user_id() : session_id();

	$traits = array(
		"userId" => $user_id
		);

	$properties = array(
		"id"       => $download_id,
		"name"     => get_the_title( $download_id ),
		"price"    => intval( edd_get_cart_item_price( $download_id, $options ) ),
		"category" => cgc_edd_get_product_category( $download_id )
	);

	cgcSegment::track( 'Added Product', $user_id, $properties, $traits );
}
add_action( 'edd_post_add_to_cart', 'cgc_edd_track_add_product_to_cart', 1, 2 );


function cgc_edd_track_remove_product_from_cart( $cart_key ) {

	$user_id = is_user_logged_in() ? get_current_user_id() : session_id();

	$traits = array(
		"userId" => $user_id
		);

	$contents    = edd_get_cart_contents();
	$cart_item   = $contents[ $cart_key ];
	$download_id = $cart_item['id'];
	$options     = $cart_item['options'];

	$properties = array(
		"id"    => $download_id,
		"name"  => get_the_title( $download_id ),
		"price" => intval( edd_get_cart_item_price( $download_id, $options ) )
		);

	cgcSegment::track( 'Removed Product', $user_id, $properties, $traits );

}
add_action( 'edd_pre_remove_from_cart', 'cgc_edd_track_remove_product_from_cart' );

// function track_save_cart()


/*
	Purchase Functions
*/
function cgc_edd_track_purchase( $payment_id ) {

	$userInfo = edd_get_payment_meta_user_info( $payment_id);
	$user_id  = get_current_user_id();

	$traits = array(
		"userId"    => $user_id,
		"firstName" => $userInfo[ 'first_name' ],
		"lastName"  => $userInfo[ 'last_name' ],
		"email"     => $userInfo[ 'email' ],
		);

	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		return false;
	}
	$subtotal  = edd_get_payment_subtotal( $payment_id );
	$total     = edd_get_payment_amount( $payment_id );

	$tax       = edd_get_payment_tax();
	$discounts = edd_get_cart_discounts();

	if ( edd_get_users_purchases( $user_id ) == false ) {
		$repeat = false;
	} else {
		$repeat = true;
	}

	$downloads = edd_get_payment_meta_cart_details( $payment_id );
	$products  = array();

	foreach( $downloads as $download ) {
		$products[] = get_the_title( $download['id'] );
	}

	$properties = array(
		"orderId"  => $payment_id,
		"total"    => intval( $subtotal ),
		"revenue"  => intval( $total ),
		"currency" => "USD",
		"tax"      => intval( $tax ),
		"discount" => intval( $subtotal - $total ), // total - coupon amount
		"coupon"   => $discounts,
		"repeat"   => $repeat,
		"products" => $products
		);

	cgcSegment::track( 'Completed Order', $user_id, $properties, $traits);
}
add_action( 'edd_complete_purchase', 'cgc_edd_track_purchase', 9999, 1 );

/*
	Product Download Functions
*/

function cgc_edd_track_product_downloaded( $download_id, $email ) {

	$user_id = get_current_user_id();

	$traits = array(
		"userId" => $user_id,
		);

	$product = get_the_title( $download_id );

	$properties = array(
		"product" => $product,
		);
	cgcSegment::track( 'Product Download', $user_id, $properties, $traits );
}
add_action( 'edd_process_verified_download', 'cgc_edd_track_product_downloaded', 10, 2 );

