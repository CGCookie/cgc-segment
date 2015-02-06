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
	
	$properties = array(
		"id" => $download_id,
		"name" => get_the_title( $download_id ),
		"price" => edd_get_cart_item_price( $download_id, $options ),
		"category" => cgc_edd_get_product_category( $download_id )
	);

	cgcSegment::track( 'Added Product', $properties );

	// $user_id = self::identify_user();

	// Analytics::track(array(
	// 	"userId" => $user_id,
	// 	"event" => "Added Product",
	// 	"properties" => array(
	// 		"id" => $download_id,
	// 		"name" => get_the_title( $download_id ),
	// 		"price" => edd_get_cart_item_price( $download_id, $options ),
	// 		"category" => self::cgc_get_product_category( $download_id )
	// 		)
	// 	)
	// );
}
add_action( 'edd_post_add_to_cart', 'cgc_edd_track_add_product_to_cart', 1, 2 );


function cgc_edd_track_remove_product_from_cart( $cart_key ) {

	$contents    = edd_get_cart_contents();
	$cart_item   = $contents[ $cart_key ];
	$download_id = $cart_item['id'];
	$options     = $cart_item['options'];

	$properties = array(
		"id" => $download_id,
		"name" => get_the_title( $download_id ),
		"price" => edd_get_cart_item_price( $download_id, $options )
		);

	cgcSegment::track( 'Removed Product', $properties );

	// Analytics::track(array(
	// 	"userId" => $user_id,
	// 	"event" => "Removed Product",
	// 	"properties" => array(
	// 		"id" => $download_id,
	// 		"name" => get_the_title( $download_id ),
	// 		"price" => edd_get_cart_item_price( $download_id, $options ),
	// 		)
	// 	)
	// );
}
add_action( 'edd_pre_remove_from_cart', 'cgc_edd_track_remove_product_from_cart' );

// function track_save_cart()


/*
	Purchase Functions
*/
function cgc_edd_track_purchase( $payment_id ) {
	$user_id = get_current_user_id();

	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		return false;
	}
	$subtotal = edd_get_payment_subtotal( $payment_id );
	$total = edd_get_payment_amount( $payment_id );

	$tax = edd_get_payment_tax();
	$discounts = edd_get_cart_discounts();

	if ( edd_get_users_purchases( $user_id ) == false ) {
		$repeat = false;
	} else {
		$repeat = true;
	}

	$downloads = edd_get_payment_meta_cart_details( $payment_id );
	$products = array();

	foreach( $downloads as $download ) {
		$products[] = get_the_title( $download['id'] );
	}

	$properties = array(
		"orderId" => $payment_id,
		"total" => $subtotal,
		"revenue" => $total,
		"currency" => "USD",
		"tax" => $tax,
		"discount" => $subtotal - $total, // total - coupon amount
		"coupon" => $discounts,
		"repeat" => $repeat,
		"products" => $products
		);

	cgcSegment::track( 'Completed Order', $properties );


	// Analytics::track(array(
	// 	"userId" => $user_id,
	// 	"event" => "Completed Order",
	// 	"properties" => array(
	// 		"orderId" => $payment_id,
	// 		"total" => $subtotal,
	// 		"revenue" => $total,
	// 		"currency" => "USD",
	// 		"tax" => $tax,
	// 		"discount" => $subtotal - $total, // total - coupon amount
	// 		"coupon" => $discounts,
	// 		"repeat" => $repeat,
	// 		"products" => $products
	// 		)
	// 	)
	// );
}
add_action( 'edd_complete_purchase', 'cgc_edd_track_purchase', 9999, 1 );

/*
	Product Download Functions
*/

// function track_product_downloaded()

