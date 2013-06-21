<?php
/*
Plugin Name: Private Posts Easy Access
Plugin URI: http://me.abelcheung.org/devel/show-private-posts-in-wordpress/
Description: Show private post links in monthly archive, calendar, and admin interface
Author: Abel Cheung
Author URI: http://me.abelcheung.org/
Version: 1.3.0

Copyright (c) 2008, 2009, 2010 Abel Cheung
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above
      copyright notice, this list of conditions and the following
      disclaimer in the documentation and/or other materials provided
      with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
*/

function wppriv_show ($sql)
{
	$val = "WHERE post_type IN ('post','video','document') AND " .
		get_private_posts_cap_sql( 'post' );

	return $val;
}

/*
 * Replace post count manually for each category or tag,
 * instead of using the count stored in database
 */
function wppriv_fix_post_count ($terms, $taxonomies, $args) {
	global $wpdb;

	$allowed						= array(
		'category',
		'post_tag',
		'genres',
		'languages',
		'collections',
		'producers',
	);

	if ( ! count( array_intersect( $taxonomies, $allowed ) ) )
		return $terms;

	foreach ($terms as $key => $term) {
		$query					= $wpdb->prepare( "SELECT count(ID) FROM "
			. "$wpdb->term_relationships AS tr INNER JOIN "
			. "$wpdb->posts AS p ON (tr.object_id = p.ID) INNER JOIN "
			. "$wpdb->term_taxonomy AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) "
			. "WHERE tt.term_id = %s AND p.post_type IN ('post','video','document') AND "
			. get_private_posts_cap_sql( 'post' ), $term->term_id );

		$term->count			= $wpdb->get_var( $query );
		$term->counts			= $wpdb->get_var( $query );
	}

	return $terms;
}

/*
 * Don't hide empty categories in category widget when logged in
 * This is an internim solution, until get_terms() also retrieves
 * categories with count = 0
 */
function wppriv_cat_no_hide_empty ($cat_args)
{
	$user = wp_get_current_user();
	return ( $user->ID ) ? array_merge( $cat_args, array( 'hide_empty' => false ) ) : $cat_args;
}

// add_filter( 'getcalendar_where', 'wppriv_show' );
// add_filter( 'getarchives_where', 'wppriv_show' );
// add_filter( 'get_terms', 'wppriv_fix_post_count', 10, 3 );
// add_filter( 'widget_categories_dropdown_args', 'wppriv_cat_no_hide_empty' );
// add_filter( 'widget_categories_args', 'wppriv_cat_no_hide_empty' );

?>
