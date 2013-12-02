<?php
/**
 *  Search helper functions for WordPress
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

// add_filter( 'pre_get_posts', 'aihrus_search_all_post_types' );
function aihrus_search_all_post_types( $query ) {
	if ( $query->is_search ) {
		$args = array(
			'public' => true,
			'_builtin' => true,
		);

		$post_types = get_post_types( $args );
		$query->set( 'post_type', $post_types );
	}

	return $query;
}
?>
