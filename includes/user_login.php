<?php

function cgc_track_login( $user_login, $user ) {

	$user_id = $user->ID;

	$is_group_member = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->is_group_member( $user_id ) : false;

	cgcSegment::identify_user( $user_id );
	cgcSegment::track( 'User Login', $user_id );

	if( $is_group_member ) {
		cgcSegment::identify_group( $user_id );
	}

}
add_action('wp_login', 'cgc_track_login', 11, 2);