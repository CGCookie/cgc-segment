<?php

function cgc_track_login( $user_login, $user ) {

	$traits = array(
		"userId" => $user->ID
		);
	cgcSegment::identify_user( $traits );
	cgcSegment::track( 'User Login' );

}
add_action('wp_login', 'cgc_track_login', 9999, 2);