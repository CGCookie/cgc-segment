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

		if( class_exists( 'Easy_Digital_Downloads' ) ) {
			require_once dirname( __FILE__ ) . "/includes/edd.php";
		}
		if( function_exists( 'rcp_is_active' ) ) {
			require_once dirname( __FILE__ ) . "/includes/rcp.php";
		}

		class_alias('Segment', 'Analytics');
		Analytics::init("jOMIQl4Nqe4zzkUNITBHlyKKVixnTpTl");

	}

	public static function identify_user() {
		$user_id = get_current_user_id();
		$user = get_userdata( $user_id );

		Analytics::identify(array(
			"userId" => $user_id,
			"traits" => array(
				"firstName" => $user->first_name,
				"lastName" => $user->last_name,
				"email" => $user->user_email,
				"username" => $user->user_login,
				)
			)
		);
		return $user_id;
	}


	public static function track( $event = '', $properties = array() ) {

		if( empty( $event ) || empty( $properties ) ) {
			return false;
		}

		Analytics::track(array(
				"userId" => self::identify_user(),
				"event" => $event,
				"properties" => $properties
			)
		);
	}
}

function cgc_segment_load() {
	new cgcSegment;
}
add_action( 'plugins_loaded', 'cgc_segment_load' );
