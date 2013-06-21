<?php
/**
 *  WordPress Thesis theme admin customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_action( '_admin_menu', 'admin_menu_remove_editor', 1 );
// add_filter( 'thesis_header_auto_tagline', 'header_auto_tagline_keep' );
// add_action( 'get_header', 'cwc_maintenance_mode' );
// add_action( 'wp_footer', 'footer_analytics' );
// add_action('wp_dashboard_setup', 'remove_dashboard_widgets');
// add_filter( 'manage_posts_columns', 'custom_manage_posts_columns' );
// add_filter( 'request', 'custom_request' );
// add_filter( 'pre_get_posts', 'posts_for_current_author' );
// add_action( 'admin_init', 'own_admin_init' );
// add_filter( 'wp_mail_from', 'custom_wp_mail_from', 1 );
// add_filter( 'wp_mail_from_name', 'custom_wp_mail_from_name', 1);


function custom_wp_mail_from( $mail ) {
	return get_bloginfo( 'admin_email' );
}
 

function custom_wp_mail_from_name( $sendername ) {
	return get_bloginfo( 'name' );
}
 

function own_admin_init() {
	global $user_level;

	add_action( '_admin_menu', 'admin_menu_remove_editor', 1 );
	add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );
	// add_filter( 'request', 'custom_request' );

	// include 'admin-posts-filter.php';

	// author's and below
	if ( $user_level < 3 ) {
		add_filter( 'posts_where', 'own_posts_where' );
	}
}


function own_posts_where( $where ) {
	global $wpdb, $user_ID;

	$where						= " AND {$wpdb->posts}.post_author = {$user_ID}}";
	// $where						.= " AND {$wpdb->posts}.ID IN (";
	// $where						.= implode( ',', coauthored_posts( $user_ID ) );
	// $where						.= ")";

	return $where;
}


function coauthored_posts( $user_ID ) {
	global $wpdb;

	$posts						= array();
	$author						= get_userdata( $user_ID );
	$term						= get_term_by( 'name', $author->user_login, 'author' );

	$query						= $wpdb->prepare( "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_author = %d", $user_ID );
	$author_posts				= $wpdb->get_results( $query );

	foreach( $author_posts as $author_post ) {
		$posts[]				= $author_post->ID;
	}

	$query						= $wpdb->prepare( "SELECT {$wpdb->term_relationships}.object_id ID FROM {$wpdb->term_relationships} WHERE $wpdb->term_relationships.term_taxonomy_id = %d", $term->term_taxonomy_id );
	$coauthor_posts				= $wpdb->get_results( $query );

	foreach( $coauthor_posts as $coauthor_post ) {
		$posts[]				= $coauthor_post->ID;
	}

	$posts						= array_unique( $posts );

	return $posts;
}


function posts_for_current_author( $query ) {
	global $user_level, $user_ID;

	// author's and below
	if( $query->is_admin && ! empty( $query->is_main_query ) 
		// && $query->is_post_type_archive( Testimonials_Widget::pt )
		&& $user_level < 3 )
		$query->set( 'post_author', $user_ID );

	return $query;
}


function footer_analytics() {
	if ( function_exists( 'yoast_analytics' ) ) {
		yoast_analytics();
	}
}
 

function header_auto_tagline_keep() {
	return false;
}


function admin_menu_remove_editor() {
	  remove_action( 'admin_menu', '_add_themes_utility_last', 101 );
}


function cwc_maintenance_mode() {
	if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
		wp_die( __('Maintenance, please come back soon.', 'custom') );
	}
}


function remove_dashboard_widgets() {
	global $wp_meta_boxes;
	// unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments'] );
	// unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links'] );
	// unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] ); 
	unset( $wp_meta_boxes['dashboard']['normal']['core']['wp_welcome_panel'] ); 
}

?>
