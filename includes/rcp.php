<?php

function cgc_rcp_track_account_created( $user_id, $newsletters ) {

	$user_data = get_userdata( $user_id );
	$registered = ($user_data->user_registered . "\n");

	$traits = array(
		'firstName' => $user_data->first_name,
		'lastName' => $user_data->last_name,
		'email' => $user_data->user_email,
		'username' => $user_data->user_login,
		'type' => 'Basic',
		'status' => 'Free',
		'createdAt' => date("n/j/Y", strtotime($registered))
		);

	$properties = array(
		'type' => 'Basic',
		'status' => 'Free',
		'newsletters' => implode( ',', $newsletters ),
		'createdAt' => date("n/j/Y", strtotime($registered))
		);

	cgcSegment::track( 'Account Created', $properties, $traits, $user_id );
}
add_action( 'cgc_rcp_account_created', 'cgc_rcp_track_account_created', 9999, 2 );


# Stripe upgrades. This should become universal and pay payment details instead.
function cgc_rcp_account_upgrade_stripe( $payment_id) {

	$user_id = get_current_user_id();

	$subscription = rcp_get_subscription( $user_id );
	$expiration = rcp_get_expiration_date( $user_id );
	$recurring = rcp_is_recurring( $user_id ) ? 'Yes' : 'No';
	$rcp_payments = new RCP_Payments;
	$new_user = $rcp_payments->last_payment_of_user( $user_id );
	$user_time = strtotime( $user->user_registered, current_time( 'timestamp' ) );
	$renewal = ! empty( $new_user );
	$upgrade = $user_time < $ten_min_ago && ! $renewal ? true : false;
	$discount = '';

	$traits = array(
		'firstName' => '',
		'lastName' => '',
		'email' => '',
		'username' => '',
		'type' => 'Citizen',
		'status' => 'Active',
		'level' => $subscription,
		'recurring' => $recurring,
		'expiration' => $expiration
		);

	if( ! empty( $_REQUEST['rcp_discount'] ) ) {
		$discount = sanitize_text_field( $_REQUEST['rcp_discount'] );
	}

	$properties = array(
		'type' => 'Citizen',
		'status' => 'Active',
		'level' => $subscription,
		'redeemed gift' => 'No',
		'coupon' => $discount,
		'recurring' => $recurring,
		'expiration' => $expiration,
		'renewal' => $renewal ? 'Yes' : 'No',
		'Time Since Creation' => human_time_diff( $user_time, current_time( 'timestamp' ) ),
		);

	cgcSegment::track( 'Account Upgraded', $properties, $traits, $user_id );

}
add_action( 'rcp_stripe_signup', 'cgc_rcp_account_upgrade_stripe', 10, 2 );

# Paypal upgrades.
function cgc_rcp_account_upgrade_paypal( $payment_id) {

	$user_id = get_current_user_id();

	$subscription = rcp_get_subscription( $user_id );
	$expiration = rcp_get_expiration_date( $user_id );
	$recurring = rcp_is_recurring( $user_id ) ? 'Yes' : 'No';
	$rcp_payments = new RCP_Payments;
	$new_user = $rcp_payments->last_payment_of_user( $user_id );
	$user_time = strtotime( $user->user_registered, current_time( 'timestamp' ) );
	$renewal = ! empty( $new_user );
	$upgrade = $user_time < $ten_min_ago && ! $renewal ? true : false;
	$discount = '';

	$traits = array(
		'firstName' => '',
		'lastName' => '',
		'email' => '',
		'username' => '',
		'type' => 'Citizen',
		'status' => 'Active',
		'level' => $subscription,
		'recurring' => $recurring,
		'expiration' => $expiration
		);

	if( ! empty( $_REQUEST['rcp_discount'] ) ) {
		$discount = sanitize_text_field( $_REQUEST['rcp_discount'] );
	}

	$properties = array(
		'type' => 'Citizen',
		'status' => 'Active',
		'level' => $subscription,
		'redeemed gift' => 'No',
		'coupon' => $discount,
		'recurring' => $recurring,
		'expiration' => $expiration,
		'renewal' => $renewal ? 'Yes' : 'No',
		'Time Since Creation' => human_time_diff( $user_time, current_time( 'timestamp' ) ),
		);

	cgcSegment::track( 'Account Upgraded', $properties, $traits, $user_id );

}
add_action( 'rcp_ipn_subscr_payment', 'cgc_rcp_account_upgrade_paypal', 10, 2 );


function cgc_rcp_track_cancelled_paypal( $user_id ) {

	$user_id = get_current_user_id();
	$user_data = get_userdata( $user_id );

	$subscription = rcp_get_subscription( $user_id );
	$expiration = rcp_get_expiration_date( $user_id );
	$recurring = rcp_is_recurring( $user_id ) ? 'Yes' : 'No';

	$traits = array(
		'firstName' => $user_data->first_name,
		'lastName' => $user_data->last_name,
		'email' => $user_data->user_email,
		'username' => $user_data->user_login,
		'type' => 'Citizen',
		'status' => 'Cancelled',
		'level' => $subscription,
		'recurring' => $recurring,
		'expiration' => $expiration
		);

	$properties = array(
		'type' => 'Citizen',
		'status' => 'Cancelled',
		'level' => $subscription,
		'recurring' => $recurring,
		'expiration' => $expiration,
		);

	cgcSegment::track( 'Membership Termination', $properties, $traits, $user_id );
}
add_action( 'rcp_ipn_subscr_cancel', 'cgc_rcp_track_cancelled_paypal' );


function cgc_rcp_track_cancelled_stripe( $invoice ) {

	$user_id = rcp_stripe_get_user_id( $invoice->customer );

	$user_data = get_userdata( $user_id );

	$subscription = rcp_get_subscription( $user_id );
	$expiration = rcp_get_expiration_date( $user_id );
	$recurring = rcp_is_recurring( $user_id ) ? 'Yes' : 'No';

	$traits = array(
		'firstName' => $user_data->first_name,
		'lastName' => $user_data->last_name,
		'email' => $user_data->user_email,
		'username' => $user_data->user_login,
		'type' => 'Citizen',
		'status' => 'Cancelled',
		'level' => $subscription,
		'recurring' => $recurring,
		'expiration' => $expiration
		);

	$properties = array(
		'type' => 'Citizen',
		'status' => 'Cancelled',
		'level' => $subscription,
		'recurring' => $recurring,
		'expiration' => $expiration,
		);

	cgcSegment::track( 'Membership Termination', $properties, $traits, $user_id );
}
add_action( 'rcp_stripe_customer.subscription.deleted', 'cgc_rcp_track_cancelled_stripe' );

# function cgc_rcp_payment

# function cgc_rcp_account_status