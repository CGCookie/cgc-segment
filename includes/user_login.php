<?php

function cgc_track_login( $user_login, $user ) {

	$user_id    = $user->ID;
	$user_data  = get_userdata( $user_id );
	$registered = ($user_data->user_registered . "\n");


	$traits = array(
		'userId'    => $user_id,
		'firstName' => $user_data->first_name,
		'lastName'  => $user_data->last_name,
		'username'  => $user_login,
		'email'     => $user_data->user_email,
		'createdAt' => date("n/j/Y", strtotime($registered))
		);

	$properties = array(
		'userId'    => $user_id,
		'firstName' => $user_data->first_name,
		'lastName'  => $user_data->last_name,
		'username'  => $user_login,
		'email'     => $user_data->user_email
		);

	if( function_exists( 'rcp_get_subscription' ) ) {
		$subscription = rcp_get_subscription( $user_id );

		$traits['type']       = rcp_is_active( $user_id ) ? 'Citizen' : 'Basic';
		$traits['status']     = ucwords( rcp_get_status( $user_id ) );
		$traits['level']      = $subscription;
		$traits['expiration'] = rcp_get_expiration_date( $user_id );

		$properties['type']   = rcp_is_active( $user_id ) ? 'Citizen' : 'Basic';
		$properties['status'] = ucwords( rcp_get_status( $user_id ) );
		$properties['level']  = $subscription;
	}

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'User Login', $properties, $traits, $user_id);

}
add_action('wp_login', 'cgc_track_login', 11, 2);