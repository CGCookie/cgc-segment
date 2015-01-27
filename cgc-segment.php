<?php
/**
 * Plugin Name: CG Cookie Segment.io Tracking
 * Description: Sends CGC data to segment.io
 * Author: Jonathan Williamson
 * Author URI: http://cgcookie.com
 * Version: 1.0
 */


# Setup our Segment tracking and
# alias to Analytics for convenience
if ( ! class_exists( 'Segment' ) ) {
	require_once dirname( __FILE__ ) . "/analytics-php/lib/Segment.php"
	class_alias('Segment', 'Analytics');
	Analytics::init("jOMIQl4Nqe4zzkUNITBHlyKKVixnTpTl");
}


function cgc_segment_edd_added_product_to_cart( $download_id ) {
	
	Analytics::track(array(
	  "userId" => "test user",
	  "event" => "Added Product to Cart",
	  "properties" => array(
	    "product" => get_the_title( $download_id );
	    "value" => edd_get_cart_item_price( $download_id, $options );
	  )
	));
}
add_action( 'edd_post_add_to_cart', 'cgc_segment_edd_added_product_to_cart' );

/* JS snippet, which is tentatively not needed as it's handled by the Segment.io Wordpress plugin.

function cgc_segment_js() {

	global $user_login;
	get_currentuserinfo();

?>

	<script type="text/javascript">
	  (function(){

	    // Create a queue, but don't obliterate an existing one!
	    var analytics = window.analytics = window.analytics || [];

	    // If the real analytics.js is already on the page return.
	    if (analytics.initialize) return;

	    // If the snippet was invoked already show an error.
	    if (analytics.invoked) {
	      if (window.console && console.error) {
	        console.error('Segment snippet included twice.');
	      }
	      return;
	    }

	    // Invoked flag, to make sure the snippet
	    // is never invoked twice.
	    analytics.invoked = true;

	    // A list of the methods in Analytics.js to stub.
	    analytics.methods = [
	      'trackSubmit',
	      'trackClick',
	      'trackLink',
	      'trackForm',
	      'pageview',
	      'identify',
	      'group',
	      'track',
	      'ready',
	      'alias',
	      'page',
	      'once',
	      'off',
	      'on'
	    ];

	    // Define a factory to create stubs. These are placeholders
	    // for methods in Analytics.js so that you never have to wait
	    // for it to load to actually record data. The `method` is
	    // stored as the first argument, so we can replay the data.
	    analytics.factory = function(method){
	      return function(){
	        var args = Array.prototype.slice.call(arguments);
	        args.unshift(method);
	        analytics.push(args);
	        return analytics;
	      };
	    };

	    // For each of our methods, generate a queueing stub.
	    for (var i = 0; i < analytics.methods.length; i++) {
	      var key = analytics.methods[i];
	      analytics[key] = analytics.factory(key);
	    }

	    // Define a method to load Analytics.js from our CDN,
	    // and that will be sure to only ever load it once.
	    analytics.load = function(key){
	      // Create an async script element based on your key.
	      var script = document.createElement('script');
	      script.type = 'text/javascript';
	      script.async = true;
	      script.src = ('https:' === document.location.protocol
	        ? 'https://' : 'http://')
	        + 'cdn.segment.com/analytics.js/v1/'
	        + key + '/analytics.min.js';

	      // Insert our script next to the first script element.
	      var first = document.getElementsByTagName('script')[0];
	      first.parentNode.insertBefore(script, first);
	    };

	    // Add a version to keep track of what's in the wild.
	    analytics.SNIPPET_VERSION = '3.0.1';

	    // Load Analytics.js with your key, which will automatically
	    // load the tools you've enabled for your account. Boosh!
	    analytics.load("YOUR_WRITE_KEY");

	    // Make the first page call to load the integrations. If
	    // you'd like to manually name or tag the page, edit or
	    // move this call however you'd like.
	    analytics.page();

	    // CG Cookie events

	    // Identify users upon login
	    analytics.identify

	  })();
	</script>

*/
