<?php
/**
 * Look up in WordPress the old video or document URLs and redirect to the new 
 * location if possible.
 *
 * test urls
 * http://fitv.aihr.us/Video-Preview.128.0.html?&uid=5129
 * http://fitv.aihr.us/Video-Preview.128.0.html?&action=2&uid=5943
 *
 * @author Michael Cannon <mc@aihr.us>
 */

require_once( './wp-load.php' );

$video							= isset( $_REQUEST[ 'video' ] ) ? $_REQUEST[ 'video' ] : false;
$video							= intval( $video );

$document						= isset( $_REQUEST[ 'document' ] ) ? $_REQUEST[ 'document' ] : false;
$document						= intval( $document );

$redirect_url					= site_url( '/' );
$url_found						= false;

if ( $video || $document ) {
	// lookup incoming arg against meta_key t3:mbr.uid for video
	$args						= array(
		'post_type'				=> $video ? 'video' : 'document',
		'meta_key'				=> $video ? 't3:mbr.uid' : 't3:doc.uid',
		'meta_value'			=> $video ? $video : $document,
	);
	$query						= new WP_Query( $args );
	if ( $query->have_posts() ) {
		$query->the_post();
		$redirect_url			= get_permalink( get_the_ID() );
		$url_found				= true;
	}
}

// print_r($redirect_url); echo '<br />'; echo '' . __LINE__ . ':' . basename( __FILE__ )  . '<br />';	
header( 'Location: ' . $redirect_url, true, $url_found ? 301 : 307 );
exit;

?>