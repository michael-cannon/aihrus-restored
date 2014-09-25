<?php
/**
 *  WordPress Thesis theme attachment customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_action( 'admin_init', 'register_attachment_taxonomy' );
// add_action( 'thesis_hook_after_post', 'display_exif' );
// add_filter( 'wp_read_image_metadata', 'read_all_image_metadata', '', 3 );
// add_filter( 'wp_generate_attachment_metadata', 'add_attachment_alt_text', '', 2 );
// add_filter( 'wp_generate_attachment_metadata', 'add_attachment_post_tags', '', 2 );


require_once( 'functions.php' );
require_once( 'exif.php' );


function register_attachment_taxonomy() {
	add_post_type_support( 'attachment', 'post_tag' );
	register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}


function add_attachment_post_tags( $meta, $attachment_id ) {
	if ( isset( $meta['image_meta']['keywords'] ) )
		wp_add_post_tags( $attachment_id, $meta['image_meta']['keywords'] );

	return $meta;
}


function add_attachment_alt_text( $meta, $attachment_id ) {
	if ( isset( $meta['image_meta']['title'] ) ) {
		$keywords				= isset( $meta['image_meta']['keywords'] ) ? trim( $meta['image_meta']['keywords'] ) : false;
		$title					= $meta['image_meta']['title'];

		if ( ! $title ) {
			$attachment	= get_post( $attachment_id );
			$title		= $attachment->post_title;
		}

		$alt_title				= ( $keywords ) ? $keywords : $title;

		update_post_meta( $attachment_id, '_wp_attachment_image_alt', addslashes($alt_title) );
	}

	return $meta;
}

?>
