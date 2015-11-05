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
	cgcSegment::track( 'Exercise Submitted', $user_id, $properties, $traits );
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
	cgcSegment::track( 'Exercise Deleted', $user_id, $properties, $traits );
}
add_action( 'cgc_exercise_deleted', 'cgc_track_exercise_deleted', 10, 2 );


# Track exercise votes
function cgc_track_exercise_vote( $submission_id, $user_id, $vote ) {

	$exercise_parent = get_post_meta( $submission_id, '_cgc_exercise_submission_linked_to', true );
	$exercise        = get_the_title( $exercise_parent );
	$exercise_type   = get_post_meta( $exercise_parent, '_cgc_edu_exercise_type', true );

	$properties = array(
		'userId'       => $user_id,
		'exercise'     => $exercise,
		'exerciseType' => $exercise_type,
		'vote'         => $vote,
		);

	$traits = array(
		'userId' => $user_id,
		);

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'Exercise Vote', $user_id, $properties, $traits );
}
add_action( 'cgc_edu_exercise_voted', 'cgc_track_exercise_vote', 10, 3 );


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
	cgcSegment::track( 'Enrolled in Flow', $user_id, $properties, $traits );

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
	cgcSegment::track( 'Dropped Flow', $user_id, $properties, $traits );

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
	cgcSegment::track( 'Asked Question', $user_id, $properties, $traits );

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

	cgcSegment::track( 'Bookmark Added', $user_id, $properties, $traits );

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

	cgcSegment::track( 'Bookmark Removed', $user_id, $properties, $traits );

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

	cgcSegment::track( 'Image Uploaded', $user_id, $properties, $traits );

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
	cgcSegment::track( 'Interests Updated', $user_id, $properties, $traits );
}
add_action( 'learning_interests_saved', 'cgc_track_interests_updated', 10, 3 );


# Track user downloads
function cgc_track_download( $user_id, $post_id, $post_type, $download_name ) {

	$post_name = get_the_title( $post_id );
	$download_count = class_exists('cgcUserAPI') ? cgcUserAPI::get_download_count( $user_id ) : false;

	$properties = array(
		'userId'   => $user_id,
		'download' => $download_name,
		'postType'   => $post_type,
		'postName'   => $post_name,
		'postId'   => $post_id,
		);

	$traits = array(
		'userId'   => $user_id,
		'downloadCount' => $download_count,
		);

	cgcSegment::identify_user( $user_id, $traits );
	cgcSegment::track( 'File Download', $user_id, $properties, $traits );
}
add_action( 'cgc_user_download', 'cgc_track_download', 10, 4 );

#Track user progress through Courses and Flows
function cgc_track_education_progress( $user_id, $new_progress, $lesson_id, $course_id, $flow_id ){

	$lesson_name     = get_the_title( $lesson_id );

	$course_name     = get_the_title( $course_id );
	$course_progress = function_exists('cgc_get_course_progress') ? cgc_get_course_progress( $course_id ) : false;

	$flow_name       = $flow_id ? get_the_title( $flow_id ) : 'null';
	$flow_progress   = $flow_id && function_exists('cgc_get_flow_progress') ? cgc_get_flow_progress( $flow_id ) : 'null';

	$course_item_exists 	 = function_exists('cgc_activity_get_item') ? cgc_activity_get_item( $user_id, $course_id, 'course_completed' ) : false;
	$flow_item_exists 	 = function_exists('cgc_activity_get_item') ? cgc_activity_get_item( $user_id, $flow_id, 'flow_completed' ) : false;

	$properties = array(
		'userId' => $user_id,
		'flow'   => $flow_name,
		'course' => $course_name,
		'lesson' => $lesson_name
	);

	if( $course_progress > 95 && false == $course_item_exists ) {
		cgcSegment::track( 'Course Completed', $user_id, $properties );
	}

	if( $flow_progress > 95 && false == $flow_item_exists ) {
		cgcSegment::track( 'Flow Completed', $user_id, $properties );
	}
}
add_action('cgc_lesson_progress_updated', 'cgc_track_education_progress', 10, 5 );

