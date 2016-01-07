<?php

# Track tour progress
function cgc_track_tour_progress( $user_id, $step ) {

	$properties = array(
		'userId' => $user_id,
		);

	$traits = array(
		'userId' => $user_id,
		);

	// cgcSegment::identify_user( $user_id, $traits );
	if( $step == 'one' ){
		cgcSegment::track( 'Tour Step One', $user_id, $properties );
	}
	if( $step == 'two' ){
		cgcSegment::track( 'Tour Step Two', $user_id, $properties );
	}
	if( $step == 'three' ){
		cgcSegment::track( 'Tour Step Three', $user_id, $properties );
	}

}
add_action( 'cgc_welcome_step_complete', 'cgc_track_tour_progress', 10, 2 );

# Track tour completion
function cgc_track_tour_complete( $user_id ) {

	$properties = array(
		'userId' => $user_id,
		);

	$traits = array(
		'userId' => $user_id,
		);

	cgcSegment::track( 'Tour Complete', $user_id, $properties );

}
add_action( 'cgc_welcome_tour_complete', 'cgc_track_tour_complete', 10, 1 );

