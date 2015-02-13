<?php

function cgc_track_login( $user_login, $user ) {

	$user_id = $user->ID;

	$traits = array(
		'userId' => $user->ID,
		);

	if( function_exists( 'rcp_get_subscription' ) ) {
		$subscription = rcp_get_subscription( $user_id );

		$traits['account type'] = rcp_is_active( $user_id ) ? 'Citizen' : 'Basic';
		$traits['account status'] = ucwords( rcp_get_status( $user_id ) );
		$traits['account level'] = $subscription;
	}

	$properties = array();

	cgcSegment::identify_user( $traits );
	cgcSegment::track( 'User Login', $properties, $traits );

}
add_action('wp_login', 'cgc_track_login', 9999, 2);