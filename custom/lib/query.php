<?php
/**
 *  WordPress query customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

// add_filter( 'pre_get_posts', 'post_type_pre_get_posts' );
// add_filter( 'pre_get_posts', 'allow_private_pre_get_posts' );
// add_action( 'edited_term_taxonomy', 'count_edited_term_taxonomy', 10, 2 );
// add_filter( 'get_terms', 'admin_get_terms', 10, 3 );

function admin_get_terms( $terms, $taxonomies, $args ) {
	global $wpdb;

	if ( ! is_admin() )
		return $terms;

	$post_type					= isset( $_GET['post_type'] ) ? "'" . esc_attr( $_GET['post_type'] ) . "'" : 'post';

	foreach ( $terms as $key => $term ) {
		if ( ! is_object( $term ) )
			continue;

		$query					= $wpdb->prepare( "SELECT count(ID) FROM "
			. "$wpdb->term_relationships AS tr INNER JOIN "
			. "$wpdb->posts AS p ON (tr.object_id = p.ID) INNER JOIN "
			. "$wpdb->term_taxonomy AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) "
			. "WHERE tt.term_id = %s AND p.post_type IN ($post_type) AND "
			. get_private_posts_cap_sql( 'post' ), $term->term_id );

		$count					= $wpdb->get_var( $query );
		$term->count			= $count;
	}

	return $terms;
}

// allow private or not in taxonomy counts
function count_edited_term_taxonomy( $term, $taxonomy ) {
	global $wpdb;

	$count						= $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $term ) );
	$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
}

function post_type_pre_get_posts( $query ) {
	if ( false == $query->query_vars['suppress_filters'] ) {
		if ( is_home() || is_category() || is_tag() || is_author() ) {
			$query->set( 'post_type', array( 'post', 'video', 'document' ) );
		}
	}

	return $query;
}

function allow_private_pre_get_posts( $query ) {
	if ( ! current_user_can( 'administrator' ) )
		return $query;

	if ( false == $query->query_vars['suppress_filters'] ) {
		if ( is_archive() || is_home() || is_category() || is_tag() || is_author() ) {
			$query->set( 'post_status', array( 'publish', 'private' ) );
		}
	}

	return $query;
}

/**
 * Alter different parts of the query
 * 
 * @ref http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_clauses
 * @param array $pieces
 * @return array $pieces
 */
function intercept_query_clauses( $pieces ) {
	// >>>> Inspect & Debug the Query 
	// NEVER EVER show this to anyone else than an admin user - unless you're in your local installation
	if ( current_user_can( 'manage_options' ) && is_main_query() ) {
		$dump = var_export( $pieces, true );
		echo '<style>#post-clauses-dump { display: block; background-color: #777; color: #fff; white-space: pre-line; }</style>';
		echo "<pre id='post-clauses-dump'>{$dump}</pre>";
	}

	return $pieces;
}
// add_filter( 'posts_clauses', 'intercept_query_clauses', 20, 1 );

?>
