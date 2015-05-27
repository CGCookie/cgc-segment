<?php

# Track Flows
function cgc_track_flows_enrolled( $user_id, $post_id ) {

	$flow = get_the_title( $post_id );

	$properties = array(
		'userId' => $user_id,
		'flow'   => $flow
		);

	$traits = array(
		'userId'    => $user_id,
		'flow'   => $flow
		);

	cgcSegment::track( 'Enrolled in Flow', $properties, $traits, $user_id );

}
add_action( 'flow_enrolled', 'cgc_track_flows_enrolled', 10, 2 );


function cgc_track_flows_dropped( $user_id, $post_id ) {

	$flow = get_the_title( $post_id );

	$properties = array(
		'userId' => $user_id,
		'flow'   => $flow
		);

	$traits = array(
		'userId'    => $user_id,
		'flow'   => $flow
		);

	cgcSegment::track( 'Dropped Flow', $properties, $traits, $user_id );

}
add_action( 'flow_dropped', 'cgc_track_flows_dropped', 10, 2 );

# Track Questions


# Track Bookmarks


# Track Images