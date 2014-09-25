<?php
/**
 *  Capability modiifiers
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

// add_action( 'admin_init', 'modify_role_editor' );
// add_action( 'admin_init', 'modify_role_author' );
// add_action( 'admin_init', 'modify_role_contributor' );

function modify_role_editor() {
	global $wp_roles;

	$wp_roles->remove_cap( 'editor', 'delete_posts' );
	$wp_roles->remove_cap( 'editor', 'delete_published_posts' );
}

function modify_role_author() {
	global $wp_roles;

	$wp_roles->add_cap( 'author', 'edit_others_posts' );
	$wp_roles->remove_cap( 'author', 'delete_posts' );
	$wp_roles->remove_cap( 'author', 'delete_published_posts' );
	$wp_roles->remove_cap( 'author', 'publish_posts' );
}

function modify_role_contributor() {
	global $wp_roles;

	$wp_roles->remove_cap( 'contributor', 'delete_posts' );
}

?>
