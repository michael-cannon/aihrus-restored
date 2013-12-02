<?php
/**
 *  Search helper functions for WordPress
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

// add_filter( 'pre_get_posts', 'aihrus_search_all_post_types' );
function aihrus_search_all_post_types( $query ) {
	if ( $query->is_search ) {
		$args        = array(
			'public' => true,
			'_builtin' => true,
		);
		$pt_built_in = get_post_types( $args );

		$args      = array(
			'public' => true,
			'_builtin' => false,
		);
		$pt_custom = get_post_types( $args );
		
		$post_types = array_merge( $pt_custom, $pt_built_in );
		$query->set( 'post_type', $post_types );
	}

	return $query;
}
?>
