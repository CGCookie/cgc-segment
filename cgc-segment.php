<?php
/**
 * Plugin Name: CG Cookie Segment.io Tracking
 * Description: Sends CGC data to segment.io
 * Author: Jonathan Williamson
 * Author URI: http://cgcookie.com
 * Version: 1.0.0
 */

$plugin_url = WP_PLUGIN_URL . '/cgc-segment';
$options = array();

class cgcSegment {

	function __construct(){

		$options = get_option( 'cgc_segment' );

		if( $options != '' ) {

			$WRITE_KEY = $options['cgc_segment_write_key'];

		}

		require_once dirname( __FILE__ ) . "/analytics-php/lib/Segment.php";

		if( class_exists( 'Easy_Digital_Downloads' ) ) {
			require_once dirname( __FILE__ ) . "/includes/edd.php";
		}
		if( function_exists( 'rcp_is_active' ) ) {
			require_once dirname( __FILE__ ) . "/includes/rcp.php";
		}
		require_once dirname( __FILE__ ) . "/includes/pageviews.php";
		require_once dirname( __FILE__ ) . "/includes/user_login.php";

		# Setup our Segment tracking and
		# alias to Analytics for convenience
		class_alias('Segment', 'Analytics');
		Analytics::init( $WRITE_KEY );

	}

	public static function identify_user( $user_id = '', $traits = array() ) {

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );
		}

		# Check for traits
		if( empty( $traits ) && is_user_logged_in() ) {

			$traits = array(
				"firstName" => $user->first_name,
				"lastName" => $user->last_name,
				"email" => $user->user_email,
				"username" => $user->user_login
				);
		}

		# User data to be passed
		$args = array(
			"userId" => $user_id,
			"traits" => $traits
		);
		Analytics::identify( $args );
		return $args;
	}


	public static function track( $event = '', $properties = array(), $traits = array() ) {

		# If no event name is passed, return
		if( empty( $event ) ) {
			return false;
		}

		Analytics::track(array(
				"userId" => is_user_logged_in() ? get_current_user_id() : $_SERVER['REMOTE_ADDR'],
				"event" => $event,
				"traits" => $traits,
				"properties" => $properties
			)
		);
	}


	public static function page( $pagename = '', $properties = array(), $traits = array() ) {

		Analytics::page(array(
			"userId" => is_user_logged_in() ? get_current_user_id() : $_SERVER['REMOTE_ADDR'],
			"name" => $pagename,
			"properties" => $properties
			)
		);
	}


}

function cgc_segment_menu() {
	add_options_page(
		'CG Cookie Segment Event Tracking',
		'CGC Segment',
		'manage_options',
		'cgc-segment-options',
		'cgc_segment_options_page'
		);
}
add_action( 'admin_menu', 'cgc_segment_menu');

function cgc_segment_options_page() {
	if( !current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	global $plugin_url;
	global $options;

	if( isset( $_POST['cgc_segment_write_key_form_submitted'] ) ) {

		$hidden_field = esc_html( $_POST['cgc_segment_write_key_form_submitted'] );

		if( $hidden_field == 'Y' ) {

			$cgc_segment_write_key = esc_html( $_POST['cgc_segment_write_key'] );

			$options['cgc_segment_write_key'] = $cgc_segment_write_key;

			update_option( 'cgc_segment', $options );

		}

	}

	require ( 'includes/options_page_wrapper.php');

}

function cgc_segment_load() {

	$options = get_option( 'cgc_segment' );
	$key = $options['cgc_segment_write_key'];

	if( !empty( $key ) ) {
		new cgcSegment;
	}
}
add_action( 'plugins_loaded', 'cgc_segment_load' );
