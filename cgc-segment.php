<?php
/**
 * Plugin Name: CG Cookie Segment.io Tracking
 * Description: Sends CGC data to segment.io
 * Author: Jonathan Williamson
 * Author URI: http://cgcookie.com
 * Version: 1.2.1
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
			require_once dirname( __FILE__ ) . "/includes/edd_events.php";
			require_once dirname( __FILE__ ) . "/includes/edd_pageviews.php";
		}
		if( function_exists( 'rcp_is_active' ) ) {
			require_once dirname( __FILE__ ) . "/includes/rcp.php";
		}
		require_once dirname( __FILE__ ) . "/includes/pageviews.php";
		require_once dirname( __FILE__ ) . "/includes/user_actions.php";
		require_once dirname( __FILE__ ) . "/includes/user_login.php";
		require_once dirname( __FILE__ ) . "/includes/user_registration.php";
		require_once dirname( __FILE__ ) . "/includes/optimizely_js.php";

		# Setup our Segment tracking and
		# alias to Analytics for convenience
		class_alias('Segment', 'Analytics');
		Analytics::init( $WRITE_KEY );

	}

	public static function identify_user( $user_id = '', $traits = array() ) {

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
			$user    = get_userdata( $user_id );
		}

		# Check for traits
		if( empty( $traits ) && is_user_logged_in() ) {

			$traits = array(
				"firstName" => $user->first_name,
				"lastName"  => $user->last_name,
				"email"     => $user->user_email,
				"username"  => $user->user_login
				);
		}

		$context = array(
			'ip' => $_SERVER['REMOTE_ADDR']
			);

		# User data to be passed
		$args = array(
			"userId"  => $user_id,
			"traits"  => $traits,
			"context" => $context
		);

		Analytics::identify( $args );
		return $args;
	}


	public static function track( $event = '', $properties = array(), $traits = array(), $user_id = '' ) {

		# If no event name is passed, return
		if( empty( $event ) ) {
			return false;
		}

		if ( empty( $user_id ) ) {
			$user_id = session_id();
		}

		Analytics::track(array(
				"userId"     => $user_id,
				"event"      => $event,
				"traits"     => $traits,
				"properties" => $properties
			)
		);
	}


	public static function page( $pagename = '', $properties = array(), $traits = array() ) {

		Analytics::page(array(
			"userId"     => is_user_logged_in() ? get_current_user_id() : session_id(),
			"name"       => $pagename,
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

function cgc_segment_load_scripts() {

	$options = get_option( 'cgc_segment' );
	$user_id = get_current_user_id();
	$user    = get_userdata( $user_id );

	$local_vars = array(
		'write_key' => $options['cgc_segment_write_key'],
		);

	if( is_user_logged_in() ) {
		$registered = ($user->user_registered . "\n");

		$local_vars['userId']    = $user_id;
		$local_vars["firstName"] = $user->first_name;
		$local_vars["lastName"]  = $user->last_name;
		$local_vars["email"]     = $user->user_email;
		$local_vars["username"]  = $user->user_login;
		$local_vars["createdAt"] = date("n/j/Y", strtotime($registered));
		$local_vars["userRoles"] = implode( ', ', $user->roles);
	}

		# Get user's interests
	if( class_exists( 'CGC_Core' ) ) {
		$subjects = get_user_meta( $user_id, 'learning_interests' );
		$topics   = get_user_meta( $user_id, 'learning_interests_secondary' );

		$local_vars['subjects']  = implode( ', ', $subjects[0] );
		$local_vars['topics']    = implode( ', ', $topics[0] );
	}


	if( function_exists( 'rcp_get_subscription' ) ) {
		$subscription = rcp_get_subscription( $user_id );

		$local_vars['type']       = rcp_is_active( $user_id ) ? 'Citizen' : 'Basic';
		$local_vars['status']     = ucwords( rcp_get_status( $user_id ) );
		$local_vars['level']      = $subscription;
		$local_vars['expiration'] = rcp_get_expiration_date( $user_id );
	}

	wp_enqueue_script( 'cgc_analytics', plugin_dir_url( __FILE__ ) . 'includes/cgc_analytics.js', array(), '3.0.1', true );
	wp_localize_script( 'cgc_analytics', 'cgc_analytics_vars', $local_vars );
}
add_action( 'wp_enqueue_scripts', 'cgc_segment_load_scripts' );

