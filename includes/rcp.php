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
		cgcSegment::track( 'Account Upgraded', $user_id, $properties, $traits );

	} elseif ( 'expired' == $new_status ) {
		// do cancelled event
		cgcSegment::track( 'Account Expired', $user_id, $properties, $traits );

	} elseif ( 'active' == $old_status && 'cancelled' == $new_status ) {
		// do cancelled event
		cgcSegment::track( 'Account Cancelled', $user_id, $properties, $traits );

	}


}
add_action( 'rcp_set_status', 'cgc_track_account_status_change', 999, 3 );

# Track new RCP subscription payments
function cgc_track_subscription_payment( $payment_id, $args ) {

	$user_id = $args['user_id'];

	$traits = array(
		'userId'             => $user_id,
		);

	$properties = array(
			'level'          => $args['subscription'],
			'date'           => date("n/j/Y", strtotime( $args['date'] )),
			'revenue'        => intval( $args['amount'] ),
			'user_id'        => $user_id,
			'payment_type'   => $args['payment_type'],
			'transaction_id' => $args['transaction_id'],
			'payment_status' => $args['status'],
			);

	cgcSegment::track( 'Subscription Payment', $user_id, $properties, $traits );

}
add_action( 'rcp_insert_payment', 'cgc_track_subscription_payment', 10, 2 );

# Track Stripe Checkout signups
function cgc_track_stripe_checkout( $user_id, $subscription ) {

	$properties = array(
		'userID' => $user_id,
		'level'  => $subscription->subscription_name,
	);

	$traits = array(
		'userID' => $user_id,
	);

	if ( !empty( $_POST['source'] ) && 'stripe-checkout' == $_POST['source'] ){

		cgcSegment::track( 'Stripe Checkout Signup', $user_id, $properties, $traits );

	}

}
add_action( 'rcp_stripe_signup', 'cgc_track_stripe_checkout', 20, 2 );

