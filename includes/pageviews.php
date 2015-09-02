<?php

function cgc_track_pageviews() {

	$properties = array();

	if( is_user_logged_in() ) {
	?>
		<script type="text/javascript">
			analytics.identify( cgc_analytics_vars.userId, {
				firstName: cgc_analytics_vars.firstName,
				lastName: cgc_analytics_vars.lastName,
				email: cgc_analytics_vars.email,
				createdAt: cgc_analytics_vars.createdAt,
				type: cgc_analytics_vars.type,
				status: cgc_analytics_vars.status,
				level: cgc_analytics_vars.level,
				expiration: cgc_analytics_vars.expiration,
				subjects: cgc_analytics_vars.subjects,
				topics: cgc_analytics_vars.topics,
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