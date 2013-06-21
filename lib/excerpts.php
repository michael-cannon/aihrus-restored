<?php
/**
 *  WordPress Thesis theme excerpt customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_filter( 'get_the_excerpt', 'excerpt_remove_social' );
// add_filter( 'get_the_excerpt', 'excerpt_read_more' );


function custom_excerpt_length( $length ) {
	return is_home() ? 85 : $length;
}

function t3vb_the_excerpt( $echo = true ) {
	global $post;

	// $the_excerpt = get_transient( 't3vb_the_excerpt_' . $post->ID );

	// if ( false === $the_excerpt ) {
		$the_excerpt = apply_filters('the_excerpt', get_the_excerpt());
	// 	set_transient( 't3vb_the_excerpt_' . $post->ID, $the_excerpt, T3VB_TIME_DAY );
	// }

	if ( $echo ) {
		echo $the_excerpt;
	} else {
		return $the_excerpt;
	}
}

function excerpt_remove_social( $text ) {
	$text						= preg_replace( '#\sEmail(Digg)?(\s+)?$#', '', $text );
	$text						= trim( $text );

	return $text;
}


function excerpt_read_more( $text ) {
	$link						= '<a href="'
		. get_permalink()
		. '" rel="bookmark" title="'
		. get_the_title()
		. '">' . __( 'Read on &rarr;', 'custom' ) . '</a>';

	$text						= str_replace( ' [...]', '…', $text );
	$text						.= ' ' .$link;

	return $text;
}


?>
