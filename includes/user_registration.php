<?php
/*
	User Account Functions
// */
function cgc_user_registration( $entry, $user_id ) {

	$user_id = self::identify_user();

	if ( $entry["4"] == true ) {
		$subscribed = true;
	} else {
		$subscribed = false;
	}

	Analytics::track(array(
		"userId"     => $user_id,
		"event"      => "User Signup",
		"properties" => array(
			"subscribed_newsletter" => $subscribed
			)
		)
	);
}
add_action( 'gform_after_submission_1', 'cgc_user_registration', 10, 2);