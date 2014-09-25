<?php
/**
 *  WordPress Thesis theme post thumbnail customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

// copy and uncomment as needed to custom_functions.php
// @ref http://codex.wordpress.org/Function_Reference/add_theme_support
// add_theme_support( 'post-thumbnails' );
// add_action( 'admin_init', 'register_attachment_taxonomy' );
// add_action( 'thesis_hook_after_post', 'display_exif' );
// add_action( 'thesis_hook_before_teaser_headline','teaser_thumbnail' );
// add_filter( 'wp_generate_attachment_metadata', 'add_attachment_alt_text', '', 2);
// add_filter( 'wp_generate_attachment_metadata', 'add_attachment_post_tags', '', 2);
// @ref http://codex.wordpress.org/Function_Reference/add_image_size
// add_image_size( 'custom', 630, 200, TRUE );
// set_post_thumbnail_size( 590, 472, true );


// add_filter( 'the_excerpt', 'prepend_post_thumbnail' );
// add_filter( 'the_content', 'prepend_post_thumbnail' );
// add_filter( 'the_content_feed', 'prepend_post_thumbnail' );
// add_filter( 'the_excerpt_rss', 'prepend_post_thumbnail' );
function post_thumbnail() {
	echo prepend_post_thumbnail( '' );
}


function prepend_post_thumbnail( $content ) {
	if ( ! has_post_thumbnail( get_the_ID() ) )
		return $content;

	$thumbnail					= '';

	if ( is_single() || is_feed() ) {
		$thumbnail				.= '<div class="post_thumbnail single_post_thumbnail">';
		$thumbnail				.= wp_get_attachment_link( get_post_thumbnail_id(), 'large', true );
		$thumbnail				.= '</div>';
	} elseif ( is_front_page() || is_home() ) {
		$thumbnail				.= '<div class="post_thumbnail home_post_thumbnail">';
		$thumbnail				.= '<a title="';
		$thumbnail				.= get_the_title();
		$thumbnail				.= '" href="';
		$thumbnail				.= get_permalink();
		$thumbnail				.= '">';
		$thumbnail				.= get_the_post_thumbnail( get_the_ID(), 'medium' );
		$thumbnail				.= '</a>';
		$thumbnail				.= '</div>';
	} elseif ( is_page() ) {
		$thumbnail				.= '<div class="post_thumbnail page_post_thumbnail">';
		$thumbnail				.= wp_get_attachment_link( get_post_thumbnail_id(), 'large', true );
		$thumbnail				.= '</div>';
	} else {
		// achives, category, tags, etc. listings
		$thumbnail				.= '<div class="post_thumbnail other_post_thumbnail">';
		$thumbnail				.= '<a title="';
		$thumbnail				.= get_the_title();
		$thumbnail				.= '" href="';
		$thumbnail				.= get_permalink();
		$thumbnail				.= '">';
		$thumbnail				.= get_the_post_thumbnail( get_the_ID(), 'medium' );
		$thumbnail				.= '</a>';
		$thumbnail				.= '</div>';
	}

	return $thumbnail . $content;
}


function teaser_thumbnail() {
	if ( ! has_post_thumbnail( get_the_ID() ) )
		return;

	echo '<div class="teaser_thumbnail">';
	echo '<a title="';
	echo get_the_title();
	echo '" href="';
	echo get_permalink();
	echo '">';
	echo get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
	echo '</a>';
	echo '</div>';
}

?>
