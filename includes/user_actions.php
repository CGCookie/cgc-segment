<?php

# Track Exercise actions
function cgc_track_exercise_submitted( $user_id, $submission_id, $type ) {

	$exercise_parent = get_post_meta( $submission_id, '_cgc_exercise_submission_linked_to', true);
	$exercise = get_the_title( $exercise_parent );

	$properties = array(
		'userId' => $user_id,
		'exercise'   => $exercise,
		'exerciseType' => $type
		);

	$traits = array(
		'userId' => $user_id,
		);

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'Exercise Submitted', $properties, $traits, $user_id );
}
add_action( 'cgc_exercise_submitted', 'cgc_track_exercise_submitted', 10, 3 );

function cgc_track_exercise_deleted( $user_id, $submissions ) {

	$properties = array(
		'userId' => $user_id,
		'exercises'   => $submissions,
		);

	$traits = array(
		'userId' => $user_id,
		);

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'Exercise Deleted', $properties, $traits, $user_id );
}
add_action( 'cgc_exercise_deleted', 'cgc_track_exercise_deleted', 10, 2 );

# Track Flows
function cgc_track_flows_enrolled( $user_id, $post_id ) {

	$flow = get_the_title( $post_id );

	$properties = array(
		'userId' => $user_id,
		'flow'   => $flow
		);

	$traits = array(
		'userId' => $user_id,
		'flow'   => $flow
		);

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'Enrolled in Flow', $properties, $traits, $user_id );

}
add_action( 'flow_enrolled', 'cgc_track_flows_enrolled', 20, 2 );


function cgc_track_flows_dropped( $user_id, $post_id ) {

	$flow = get_the_title( $post_id );

	$properties = array(
		'userId' => $user_id,
		'flow'   => $flow
		);

	$traits = array(
		'userId' => $user_id,
		'flow'   => $flow
		);

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'Dropped Flow', $properties, $traits, $user_id );

}
add_action( 'flow_dropped', 'cgc_track_flows_dropped', 20, 2 );


# Track Questions
function cgc_track_question_asked( $user_id, $comment_id ) {

	$question = get_comment( $comment_id );

	$properties = array(
		'userId'   => $user_id,
		'question' => $question->comment_content,
		);

	$traits = array(
		'userId'   => $user_id,
		'question' => $question,
		);

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'Asked Question', $properties, $traits, $user_id );

}
add_action( 'question_added', 'cgc_track_question_asked', 10, 2 );


# Track Bookmarks
function cgc_track_bookmark_added( $user_id, $post_id ) {

	$bookmark = get_the_title( $post_id );

	$properties = array(
		'userId'   => $user_id,
		'bookmark' => $bookmark,
		);

	$traits = array(
		'userId'   => $user_id,
		'bookmark' => $bookmark,
		);

	cgcSegment::track( 'Bookmark Added', $properties, $traits, $user_id );

}
add_action( 'bookmark_added', 'cgc_track_bookmark_added', 10, 2 );


function cgc_track_bookmark_removed( $user_id, $post_id ) {

	$bookmark = get_the_title( $post_id );

	$properties = array(
		'userId'   => $user_id,
		'bookmark' => $bookmark,
		);

	$traits = array(
		'userId'   => $user_id,
		'bookmark' => $bookmark,
		);

	cgcSegment::track( 'Bookmark Removed', $properties, $traits, $user_id );

}
add_action( 'bookmark_removed', 'cgc_track_bookmark_removed', 10, 2 );


# Track Images
function cgc_track_image_uploaded( $user_id, $post_id ) {

	$image = get_the_title( $post_id );

	$properties = array(
		'userId' => $user_id,
		'image'  => $image,
		);

	$traits = array(
		'userId' => $user_id,
		'image'  => $image,
		);

	cgcSegment::track( 'Image Uploaded', $properties, $traits, $user_id );

}
add_action( 'image_added', 'cgc_track_image_uploaded', 10, 2 );


# Track user interests
function cgc_track_interests_updated( $user_id, $main_interests, $sub_interests ) {
	$subjects = !empty( $main_interests ) ? implode( ', ', $main_interests ) : '';
	$topics   = !empty( $sub_interests ) ? implode( ', ', $sub_interests ) : '';

	$properties = array(
		'userId'   => $user_id,
		'subjects' => $subjects,
		'topics'   => $topics,
		);
	$traits = array(
		'userId'   => $user_id,
		'subjects' => $subjects,
		'topics'   => $topics,
		);

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'Interests Updated', $properties, $traits, $user_id );
}
add_action( 'learning_interests_saved', 'cgc_track_interests_updated', 10, 3 );

