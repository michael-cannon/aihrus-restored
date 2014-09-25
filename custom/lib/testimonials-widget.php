<?php
/**
 *  Testimonials Widget helper
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

// add_filter( 'pre_get_posts', 'pre_get_posts_allow_testimonials_widget' );
function pre_get_posts_allow_testimonials_widget( $query ) {
	if ( $query->is_admin ) {
		return $query;
	} elseif ( ( $query->is_main_query() || is_feed() ) && ! is_page() ) {
		$query->set( 'post_type', array( 'post', 'testimonials-widget' ) );
	}

	return $query;
}

?>
