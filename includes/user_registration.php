<?php
/*
	User Account Functions
 */
function cgc_track_user_registration( $user_id ) {

	$user_data  = get_userdata( $user_id );
	$registered = ($user_data->user_registered . "\n");

	if( function_exists( 'rcp_is_active' ) ) {
		$status = ucwords( rcp_get_status( $user_id ) );
	}

	$traits = array(
		'firstName' => $user_data->first_name,
		'lastName'  => $user_data->last_name,
		'email'     => $user_data->user_email,
		'username'  => $user_data->user_login,
		'createdAt' => date("n/j/Y", strtotime($registered))
		);

	$properties = array(
		'createdAt'   => date("n/j/Y", strtotime($registered))
		);

	if( function_exists( 'rcp_is_active' ) ) {
		$traits['status'] = $status;
		$properties['status'] = $status;
	}

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'Account Created', $properties, $traits, $user_id );
}
add_action( 'user_register', 'cgc_track_user_registration', 10, 1);
