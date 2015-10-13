<?php
/**
 * Plugin Name: CG Cookie Segment.io Tracking
 * Description: Sends CGC data to segment.io
 * Author: Jonathan Williamson
 * Author URI: http://cgcookie.com
 * Version: 1.3.2
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
		}

		$user = get_userdata( $user_id );
		$registered = ( $user->user_registered . "\n" );

		# Check for traits
		if( empty( $traits ) && is_user_logged_in() ) {

			$traits = array(
				"firstName" => $user->first_name,
				"lastName"  => $user->last_name,
				"email"     => $user->user_email,
				"username"  => $user->user_login,
				'createdAt' => date( "n/j/Y", strtotime( $registered ) )
				);
		}

		// Global traits for EDU
		if( function_exists('rcp_is_active') ) {	
			$traits['type']       = rcp_is_active( $user_id ) ? 'Citizen' : 'Basic';
			$traits['status']     = ucwords( rcp_get_status( $user_id ) );
			$traits['level']      = rcp_get_subscription( $user_id );
			$traits['expiration'] = rcp_get_expiration_date( $user_id );
		}

		if( class_exists( 'cgcUserAPI') ) {
			$traits['betaUser']   = cgcUserAPI::is_user_beta_user( $user_id );
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

	public static function identify_group( $user_id = '', $traits= array() ) {


		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		// Group membership stuff
		$is_group_member = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->is_group_member( $user_id ) : false;
		$group_id        = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->get_group_id( $user_id ) : 'null';
		$group_name      = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->get_group_name( $user_id ) : false;
		$group_role      = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->get_role( $user_id ) : 'member';

		$traits['name']      = $group_name;
		$traits['groupRole'] = $group_role;

		Analytics::group(array(
			"userId"     => $user_id,
			"groupId"    => $group_id,
			"traits"     => $traits,
			)
		);
	}

	public static function track( $event = '', $user_id = '', $properties = array(), $traits = array() ) {

		# If no event name is passed, return
		if( empty( $event ) ) {
			return false;
		}

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		// Global properties for EDU
		if( function_exists( 'rcp_is_active' ) ) {
			$properties['type']       = rcp_is_active( $user_id ) ? 'Citizen' : 'Basic';
			$properties['status']     = ucwords( rcp_get_status( $user_id ) );
			$properties['level']      = rcp_get_subscription( $user_id );
			$properties['expiration'] = rcp_get_expiration_date( $user_id );
		}

		if( class_exists( 'cgcUserAPI') ) {
			$properties['betaUser']   = cgcUserAPI::is_user_beta_user( $user_id );
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
		$subjects = get_user_meta( $user_id, 'learning_interests', true );
		$topics   = get_user_meta( $user_id, 'learning_interests_secondary', true );

		$local_vars['subjects'] = !empty( $subjects ) ? implode( ', ', $subjects ) : '';
		$local_vars['topics']   = !empty( $topics ) ? implode( ', ', $topics ) : '';
		$local_vars['betaUser'] = cgcUserAPI::is_user_beta_user( $user_id );

	}

	if( function_exists( 'rcp_get_subscription' ) ) {
		$subscription = rcp_get_subscription( $user_id );

		$local_vars['type']       = rcp_is_active( $user_id ) ? 'Citizen' : 'Basic';
		$local_vars['status']     = ucwords( rcp_get_status( $user_id ) );
		$local_vars['level']      = $subscription;
		$local_vars['expiration'] = rcp_get_expiration_date( $user_id );
	}

	// Group membership stuff
	$is_group_member = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->is_group_member( $user_id ) : false;
	$group_id        = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->get_group_id( $user_id ) : 'null';
	$group_name      = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->get_group_name( $user_id ) : false;
	$group_role      = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->get_role( $user_id ) : 'member';

	if( $is_group_member ) {

		$local_vars['groupId']   = $group_id;
		$local_vars['groupName'] = $group_name;
		$local_vars['groupRole'] = $group_role;
	}


	wp_enqueue_script( 'cgc_analytics', plugin_dir_url( __FILE__ ) . 'includes/cgc_analytics.js', array(), '3.0.1', true );
	wp_localize_script( 'cgc_analytics', 'cgc_analytics_vars', $local_vars );
}
add_action( 'wp_enqueue_scripts', 'cgc_segment_load_scripts' );

