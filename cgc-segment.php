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
		add_action( 'edd_post_add_to_cart', array($this,'track_add_product_to_cart'), 10, 2 );
		add_action( 'edd_remove', array($this, 'track_remove_product_from_cart'), 10, 2);

		add_action( 'edd_complete_purchase', array($this, 'track_purchase'), 10, 3 );
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

	function track_add_product_to_cart( $download_id, $options ) {
		$user_id = self::identify_user();

		Analytics::track(array(
			"userId" => $user_id,
			"event" => "Added Product to Cart",
			"properties" => array(
				"product" => get_the_title( $download_id ),
				"value" => edd_get_cart_item_price( $download_id, $options )
				)
			)
		);
	}

	function track_remove_product_from_cart( $download_id, $options ) {
		$user_id = self::identify_user();

		Analytics::track(array(
			"userId" => $user_id,
			"event" => "Removed Product from Cart",
			"properties" => array(
				"product" => get_the_title( $download_id ),
				"value" => edd_get_cart_item_price( $download_id, $options )
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
		$purchase_date = edd_get_payment_completed_date( $payment_id );

		$products = array();
		foreach( $downloads as $download ) {
			$products[] = get_the_title( $download_id['id'] );
		}

		Analytics::track(array(
			"userId" => $user_id,
			"event" => "Purchased Product(s)",
			"properties" => array(
				"cart quantity" => edd_get_cart_quantity(),
				"products" => implode( ', ', $products ),
				"value" => $amount,
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