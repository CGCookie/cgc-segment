<?php

function cgc_track_pageviews() {


	$properties = array();

	if( is_user_logged_in() ) {

		$user_id = get_current_user_id();
		$is_group_member = function_exists('cgc_group_accounts') ? cgc_group_accounts()->members->is_group_member( $user_id ) : false;

		?>
		<script type="text/javascript">
			analytics.identify( cgc_analytics_vars.userId, {
				firstName: cgc_analytics_vars.firstName,
				lastName: cgc_analytics_vars.lastName,
				email: cgc_analytics_vars.email,
				createdAt: cgc_analytics_vars.createdAt,
				userRoles: cgc_analytics_vars.userRoles,
				type: cgc_analytics_vars.type,
				status: cgc_analytics_vars.status,
				level: cgc_analytics_vars.level,
				expiration: cgc_analytics_vars.expiration,
				subjects: cgc_analytics_vars.subjects,
				topics: cgc_analytics_vars.topics,
				betaUser: Boolean( cgc_analytics_vars.betaUser )
				}
			);
		</script>

		<?php
		if( $is_group_member ) {
		?>
			<script type="text/javascript">
				analytics.group( cgc_analytics_vars.groupId, {
					name: cgc_analytics_vars.groupName,
					userId: cgc_analytics_vars.user_id,
					groupRole: cgc_analytics_vars.group_role
					}
				);
			</script>
		<?php
		}
	}

	?>
	<script type="text/javascript">
		analytics.page();
	</script>

<?php

}

add_action( 'wp_footer', 'cgc_track_pageviews', 20 );