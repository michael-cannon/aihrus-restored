<?php
/**
 *  WordPress Thesis theme post customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_action( 'thesis_hook_after_headline', 'custom_byline' );
// add_action( 'thesis_hook_after_post', 'post_date' );
// add_action( 'thesis_hook_before_headline', 'byline_item_avatar' );
// add_filter( 'thesis_next_post','next_post_text' );
// add_filter( 'thesis_previous_post','previous_post_text' );
// add_action( 'thesis_hook_after_post', 'custom_related_posts' );
// add_action( 'pre_ping', 'disable_self_ping' );

function disable_self_ping( &$links ) {
	foreach ( $links as $l => $link ) {
		if ( 0 === strpos( $link, get_option( 'home' ) ) ) {
			unset($links[$l]);
		}
	}
}

function custom_byline($post_count = false) {
	if ( is_page() )
		return;

	$author						= true;
	$date						= false;
	$show_comments				= true;

	if ($author || $date || $show_comments) {
		echo "\t\t\t\t\t<p class=\"headline_meta\">";

		if ($author && ! is_singular( 'testimonials-widget' ) )
			thesis_author();

		if ( ! is_page() && ! is_attachment() )
			echo ' ' . __('in', 'custom') . ' <span>' . get_the_category_list( ', ' ) . '</span>';

		if ($author && $date)
			echo ' ' . __('on', 'custom') . ' ';

		if ($date)
			echo '<abbr class="published" title="' . get_the_time('Y-m-d') . '">' . get_the_time( 'F jS' ) . '</abbr>';
		
		if ($show_comments && comments_open() ) {
			if ($author || $date)
				$sep = ' &middot; ';

			echo $sep . '<span><a href="' . get_permalink() . '#comments" rel="nofollow">';
			comments_number(__('0 comments', 'custom'), __('1 comment', 'custom'), __('% comments', 'custom'));
			echo '</a></span>';
		}

		thesis_hook_byline_item($post_count);

		echo "</p>\n";
	}
}


function byline_item_avatar() {
	if ( is_home() || is_single() || is_archive() && ! is_author() ) {
		echo get_avatar( get_the_author_id(), 44 );
	}
}


function next_post_text() {
	$next_text = '&rarr; ';

	return $next_text;
}


function previous_post_text() {
	$previous_text = '&larr; ';

	return $previous_text;
}


function post_date() {
	if ( ! is_single() )
		return;

	echo "\t\t\t\t\t<p class=\"post_tags post_date\">" . __('Posted on:', 'custom') . "\n";
	echo get_the_time( get_option('date_format')  . ' @ H:i' );
	echo "\t\t\t\t\t</p>\n";
}


function custom_related_posts() {
	if ( class_exists('efficientRelatedPosts') && is_single() && ! is_attachment() ) {
		do_action( 'erp-show-related-posts' );
	}
}

/**
 * Usage:
 * Paste a gist link into a blog post or page and it will be embedded eg:
 * https://gist.github.com/2926827
 *
 * If a gist has multiple files you can select one using a url in the following format:
 * https://gist.github.com/2926827?file=embed-gist.php
 */
wp_embed_register_handler( 'gist', '/https:\/\/gist\.github\.com\/(\d+)(\?file=.*)?/i', 'wp_embed_handler_gist' );

function wp_embed_handler_gist( $matches, $attr, $url, $rawattr ) {

	$embed = sprintf(
		'<script src="https://gist.github.com/%1$s.js%2$s"></script>',
		esc_attr($matches[1]),
		esc_attr($matches[2])
	);

	return apply_filters( 'embed_gist', $embed, $matches, $attr, $url, $rawattr );
}
function custom_meta_sidebar( $post, $parts ) {
	foreach( $parts as $part => $title ) {
		// $value					= get_post_meta($post->ID, $part, true);
		$value					= custom_manage_posts_custom_column( $part, $post->ID, false );

		if ( ! empty( $value ) ) {
			echo '<div class="section">';
			echo '<h3>';
			echo $title;
			echo '</h3>';
			echo $value;
			echo '</div>';
		}
	}
}

function custom_get_the_terms( $post, $taxonomy ) {
	// get the terms related to post
	$terms = get_the_terms( $post->ID, $taxonomy );
	if ( !empty( $terms ) ) {
		$out = array();
		foreach ( $terms as $term )
			$out[] = '<a href="' .get_term_link($term->slug, $taxonomy) .'">'.$term->name.'</a>';
		$return = join( ', ', $out );
	}

	return $return;
}

function t3vb_the_permalink( $id = 0, $echo = true ) {
	$the_permalink = get_transient( 't3vb_the_permalink_' . $id );

	if ( false === $the_permalink ) {
		$the_permalink = apply_filters( 'the_permalink', get_permalink( $id ) );
		set_transient( 't3vb_the_permalink_' . $id, $the_permalink, T3VB_TIME_DAY );
	}

	if ( $echo )
		echo $the_permalink;
	else
		return $the_permalink;
}

function t3vb_post_class( $class = '', $post_id = null, $echo = true ) {
	if ( is_null( $post_id ) )
		$post_id = get_the_ID();

	// delete_transient( 't3vb_post_class_' . $class . '-' . $post_id );
	$post_class = get_transient( 't3vb_post_class_' . $class . '-' . $post_id );

	if ( false === $post_class ) {
		$post_class = 'class="';
		$post_class .= join( ' ', get_post_class( $class, $post_id ) );
		$post_class .= '"';
		set_transient( 't3vb_post_class_' . $class . '-' . $post_id, $post_class, T3VB_TIME_DAY );
	}

	if ( $echo )
		echo $post_class;
	else
		return $post_class;
}

?>