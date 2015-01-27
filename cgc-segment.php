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
		add_action( 'edd_post_add_to_cart', array($this,'to_cart'), 10, 2 );
	}

	function to_cart( $download_id, $options ) {

		$user_id = get_current_user_id();
		$user = get_userdata( $user_id );

		Analytics::identify(array(
		  "userId" => $user_id,
		  "traits" => array(
		    "firstName" => $user->first_name,
		    "lastName" => $user->last_name,
		    "email" => $user->user_email,
		  )
		));

		Analytics::track(array(
		  "userId" => $user_id,
		  "event" => "Added Product to Cart",
		  "properties" => array(
		    "product" => get_the_title( $download_id ),
		    "value" => edd_get_cart_item_price( $download_id, $options )
		  )
		));

		}

}
new cgcSegment;