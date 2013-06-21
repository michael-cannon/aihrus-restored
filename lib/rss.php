<?php
// add_filter( 'request', 'custom_rss_request' );

function custom_rss_request( $args ) {
	if ( isset( $args['feed'] ) && ! isset( $args['post_type'] ) ) {
		$args['post_type'] = array('post', 'video', 'document');
	}

	return $args;
}

?>
