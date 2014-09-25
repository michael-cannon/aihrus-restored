<?php
/**
 * WordPress debug helpers
 *
 * @author Michael Cannon <mc@aihr.us>
 */

function wtfami( $uk = null ) {
	if ( ! is_null( $uk ) ) echo $uk . " is…\n<br />";

	if ( comments_open( $uk ) ) echo 'comments_open()' . "\n<br />";
	if ( email_exists( $uk ) ) echo 'email_exists()' . "\n<br />";
	if ( has_excerpt( $uk ) ) echo 'has_excerpt()' . "\n<br />";
	if ( has_post_thumbnail( $uk ) ) echo 'has_post_thumbnail()' . "\n<br />";
	if ( has_tag( $uk ) ) echo 'has_tag()' . "\n<br />";
	if ( in_category( $uk ) ) echo 'in_category()' . "\n<br />";
	if ( in_the_loop( $uk ) ) echo 'in_the_loop()' . "\n<br />";
	if ( is_404( $uk ) ) echo 'is_404()' . "\n<br />";
	if ( is_active_sidebar( $uk ) ) echo 'is_active_sidebar()' . "\n<br />";
	if ( is_active_widget( $uk ) ) echo 'is_active_widget()' . "\n<br />";
	if ( is_admin( $uk ) ) echo 'is_admin()' . "\n<br />";
	if ( is_admin_bar_showing( $uk ) ) echo 'is_admin_bar_showing()' . "\n<br />";
	if ( is_archive( $uk ) ) echo 'is_archive()' . "\n<br />";
	if ( is_attachment( $uk ) ) echo 'is_attachment()' . "\n<br />";
	if ( is_author( $uk ) ) echo 'is_author()' . "\n<br />";
	if ( is_blog_installed( $uk ) ) echo 'is_blog_installed()' . "\n<br />";
	if ( is_category( $uk ) ) echo 'is_category()' . "\n<br />";
	if ( is_comments_popup( $uk ) ) echo 'is_comments_popup()' . "\n<br />";
	if ( is_date( $uk ) ) echo 'is_date()' . "\n<br />";
	if ( is_day( $uk ) ) echo 'is_day()' . "\n<br />";
	if ( is_dynamic_sidebar( $uk ) ) echo 'is_dynamic_sidebar()' . "\n<br />";
	if ( is_feed( $uk ) ) echo 'is_feed()' . "\n<br />";
	if ( is_front_page( $uk ) ) echo 'is_front_page()' . "\n<br />";
	if ( is_home( $uk ) ) echo 'is_home()' . "\n<br />";
	if ( is_local_attachment( $uk ) ) echo 'is_local_attachment()' . "\n<br />";
	if ( is_month( $uk ) ) echo 'is_month()' . "\n<br />";
	if ( is_multi_author( $uk ) ) echo 'is_multi_author()' . "\n<br />";
	if ( is_new_day( $uk ) ) echo 'is_new_day()' . "\n<br />";
	if ( is_page( $uk ) ) echo 'is_page()' . "\n<br />";
	if ( is_page_template( $uk ) ) echo 'is_page_template()' . "\n<br />";
	if ( is_paged( $uk ) ) echo 'is_paged()' . "\n<br />";
	if ( is_plugin_active( $uk ) ) echo 'is_plugin_active()' . "\n<br />";
	if ( is_plugin_active_for_network( $uk ) ) echo 'is_plugin_active_for_network()' . "\n<br />";
	if ( is_plugin_inactive( $uk ) ) echo 'is_plugin_inactive()' . "\n<br />";
	if ( is_plugin_page( $uk ) ) echo 'is_plugin_page()' . "\n<br />";
	if ( is_post_type_archive( $uk ) ) echo 'is_post_type_archive()' . "\n<br />";
	if ( is_preview( $uk ) ) echo 'is_preview()' . "\n<br />";
	if ( is_rtl( $uk ) ) echo 'is_rtl()' . "\n<br />";
	if ( is_search( $uk ) ) echo 'is_search()' . "\n<br />";
	if ( is_single( $uk ) ) echo 'is_single()' . "\n<br />";
	if ( is_singular( $uk ) ) echo 'is_singular()' . "\n<br />";
	if ( is_sticky( $uk ) ) echo 'is_sticky()' . "\n<br />";
	if ( is_tag( $uk ) ) echo 'is_tag()' . "\n<br />";
	if ( is_tax( $uk ) ) echo 'is_tax()' . "\n<br />";
	if ( is_taxonomy_hierarchical( $uk ) ) echo 'is_taxonomy_hierarchical()' . "\n<br />";
	if ( is_time( $uk ) ) echo 'is_time()' . "\n<br />";
	if ( is_trackback( $uk ) ) echo 'is_trackback()' . "\n<br />";
	if ( is_user_logged_in( $uk ) ) echo 'is_user_logged_in()' . "\n<br />";
	if ( is_year( $uk ) ) echo 'is_year()' . "\n<br />";
	if ( pings_open( $uk ) ) echo 'pings_open()' . "\n<br />";
	if ( post_type_exists( $uk ) ) echo 'post_type_exists()' . "\n<br />";
	if ( taxonomy_exists( $uk ) ) echo 'taxonomy_exists()' . "\n<br />";
	if ( term_exists( $uk ) ) echo 'term_exists()' . "\n<br />";
	if ( username_exists( $uk ) ) echo 'username_exists()' . "\n<br />";
	if ( wp_attachment_is_image( $uk ) ) echo 'wp_attachment_is_image()' . "\n<br />";
	if ( wp_script_is( $uk ) ) echo 'wp_script_is()' . "\n<br />";
}

?>