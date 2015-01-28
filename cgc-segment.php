<?php
/**
 * Plugin Name: CG Cookie Segment.io Tracking
 * Description: Sends CGC data to segment.io
 * Author: Jonathan Williamson
 * Author URI: http://cgcookie.com
 * Version: 1.0.0
 */
# Setup our Segment tracking and
# alias to Analytics for convenience

class cgcSegment {

	function __construct(){

		require_once dirname( __FILE__ ) . "/analytics-php/lib/Segment.php";

		class_alias('Segment', 'Analytics');
		Analytics::init("jOMIQl4Nqe4zzkUNITBHlyKKVixnTpTl");
		add_action( 'edd_post_add_to_cart', array($this,'track_add_product_to_cart'), 1, 2 );
		add_action( 'edd_remove', array($this, 'track_remove_product_from_cart'), 1, 2);

		add_action( 'edd_complete_download_purchase', array($this, 'track_purchase'), 9999, 3 );
	}

	function identify_user() {
		$user_id = get_current_user_id();
		$user = get_userdata( $user_id );

		Analytics::identify(array(
			"userId" => $user_id,
			"traits" => array(
				"firstName" => $user->first_name,
				"lastName" => $user->last_name,
				"email" => $user->user_email,
				)
			)
		);
		return $user_id;
	}

	/*
		Cart Functions
	*/
	function cgc_get_product_category( $download_id ){
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

	function track_add_product_to_cart( $download_id, $options ) {
		$user_id = self::identify_user();

		Analytics::track(array(
			"userId" => $user_id,
			"event" => "Added Product",
			"properties" => array(
				"id" => $download_id,
				"name" => get_the_title( $download_id ),
				"price" => edd_get_cart_item_price( $download_id, $options ),
				"category" => self::cgc_get_product_category( $download_id )
				)
			)
		);
	}

	function track_remove_product_from_cart( $download_id, $options ) {
		$user_id = self::identify_user();

		Analytics::track(array(
			"userId" => $user_id,
			"event" => "Removed Product",
			"properties" => array(
				"id" =>  $download_id,
				"name" => get_the_title( $download_id ),
				"price" => edd_get_cart_item_price( $download_id, $options ),
				"category" => self::cgc_get_product_category( $download_id )
				)
			)
		);
	}

	// function track_save_cart()


	/*
		Purchase Functions
	*/
	function track_purchase( $download_id, $payment_id, $download_type ) {
		$user_id = self::identify_user();

		$downloads = edd_get_payment_meta_cart_details( $payment_id );
		$amount = edd_get_payment_amount( $payment_id );
		$discounts = edd_get_cart_discounts();
		$purchase_date = edd_get_payment_completed_date( $payment_id );

		// Not using for now; not working correctly so will track single products instead.
		// $products = array();
		// foreach( $downloads as $download ) {
		// 	$products[] = get_the_title( $download_id['id'] );
		// }

		// "cart quantity" => edd_get_cart_quantity(),
		Analytics::track(array(
			"userId" => $user_id,
			"event" => "Completed Order",
			"properties" => array(
				"orderId" => $payment_id,
				"total" => $amount,
				"revenue" => $amount,
				"currency" => "USD",
				"discounts" => 0,
				"coupon" => $discounts,
				"repeat" => '',
				"product" => get_the_title( $download_id ),
				"purchase date" => $purchase_date
				)
			)
		);
	}



	/*
		Product Download Functions
	*/

	// function track_product_downloaded()


}

new cgcSegment;