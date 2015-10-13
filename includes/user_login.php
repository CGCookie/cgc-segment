<?php

function cgc_track_login( $user_login, $user ) {

	$user_id = $user->ID;

	cgcSegment::identify_user( $user_id );
	cgcSegment::track( 'User Login', $user_id );

}
add_action('wp_login', 'cgc_track_login', 11, 2);