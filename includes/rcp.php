<?php

// # Stripe upgrades. This should become universal and pay payment details instead.
// function cgc_rcp_account_upgrade( $payment_id) {

// 	$user_id = get_current_user_id();

// 	$subscription = rcp_get_subscription( $user_id );
// 	$expiration = rcp_get_expiration_date( $user_id );
// 	$recurring = rcp_is_recurring( $user_id ) ? 'Yes' : 'No';
// 	$rcp_payments = new RCP_Payments;
// 	$new_user = $rcp_payments->last_payment_of_user( $user_id );
// 	$user_time = strtotime( $user->user_registered, current_time( 'timestamp' ) );
// 	$renewal = ! empty( $new_user );
// 	$upgrade = $user_time < $ten_min_ago && ! $renewal ? true : false;
// 	$discount = '';

// 	// $traits = array(
// 	// 	'firstName' = '',
// 	// 	'lastName' = '',
// 	// 	'email' = '',
// 	// 	'username' = '',
// 	// 	'account type' = 'Citizen',
// 	// 	'account status' = 'Active',
// 	// 	'account level' = $subscription,
// 	// 	'recurring' = $recurring,
// 	// 	'expiration' = $expiration
// 	// 	);

// 	if( ! empty( $_REQUEST['rcp_discount'] ) ) { 
// 		$discount = sanitize_text_field( $_REQUEST['rcp_discount'] );
// 	}

// 	// $properties = array(
// 	// 	'account type' = 'Citizen',
// 	// 	'account status' = 'Active',
// 	// 	'account level' = $subscription,
// 	// 	'redeemed gift' = 'No',
// 	// 	'coupon' = $discount,
// 	// 	'recurring' = $recurring,
// 	// 	'expiration' = $expiration,
// 	// 	'renewal' = $renewal ? 'Yes' : 'No',
// 	// 	'Time Since Creation' = human_time_diff( $user_time, current_time( 'timestamp' ) ),
// 	// 	);

// 	cgcSegment::track( 'Account Upgraded', $properties );

// }
// add_action( 'rcp_stripe_signup', 'cgc_rcp_account_upgrade', 10, 2 );


# function cgc_rcp_account_cancelled

# function cgc_rcp_payment

# function cgc_rcp_account_status