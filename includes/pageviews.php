<?php

function cgc_track_pageviews() {

	$properties = array();

	if( is_user_logged_in() ) {
	?>
		<script type="text/javascript">

			var expiration = new Date( cgc_analytics_vars.expiration );
			var createdAt = new Date( cgc_analytics_vars.createdAt );

			analytics.identify( cgc_analytics_vars.userId, {
				firstName: cgc_analytics_vars.firstName,
				lastName: cgc_analytics_vars.lastName,
				email: cgc_analytics_vars.email,
				createdAt: createdAt.toUTCString(),
				userRoles: cgc_analytics_vars.userRoles,
				type: cgc_analytics_vars.type,
				status: cgc_analytics_vars.status,
				level: cgc_analytics_vars.level,
				expiration: expiration.toUTCString(),
				subjects: cgc_analytics_vars.subjects,
				topics: cgc_analytics_vars.topics,
				betaUser: Boolean( cgc_analytics_vars.betaUser )
			}
			);
		</script>
	<?php
	}

	?>
	<script type="text/javascript">
		analytics.page();
	</script>

<?php

}

add_action( 'wp_footer', 'cgc_track_pageviews', 20 );