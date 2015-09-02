<?php

function cgc_optimizely_js() {

?>

	<script src="//cdn.optimizely.com/js/577480693.js"></script>

<?php 
}
add_action( 'wp_footer', 'cgc_optimizely_js' );