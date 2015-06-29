<?php

## Track account status changes

function cgc_track_account_status_change( $new_status, $user_id, $old_status ) {

	$subscription = rcp_get_subscription( $user_id );
	$expiration   = rcp_get_expiration_date( $user_id );
	$recurring    = rcp_is_recurring( $user_id ) ? 'Yes' : 'No';

	$traits = array(
		'userId'     => $user_id,
		'status'     => ucwords( $new_status ),
		'level'      => $subscription,
		'recurring'  => $recurring,
		'expiration' => $expiration,
		);
	$properties = array(
		'status'     => ucwords( $new_status ),
		'level'      => $subscription,
		'recurring'  => $recurring,
		'expiration' => $expiration,
		);

	if( 'active' == $new_status ) {
		// do upgrade event
		cgcSegment::track( 'Account Upgraded', $properties, $traits, $user_id );

	} elseif ( 'expired' == $new_status ) {
		// do cancelled event
		cgcSegment::track( 'Account Expired', $properties, $traits, $user_id );

	} elseif ( 'active' == $old_status && 'cancelled' == $new_status ) {
		// do cancelled event
		cgcSegment::track( 'Account Cancelled', $properties, $traits, $user_id );

	}


}
add_action( 'rcp_set_status', 'cgc_track_account_status_change', 999, 3 );

