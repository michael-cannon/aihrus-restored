<?php
/**
 *  Relevanssi helper functions
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// add_filter( 'relevanssi_get_words_query', 'rel_get_words_query' );
function rel_get_words_query( $query ) {
    $query						= $query . " HAVING c > 1";

    return $query;
}


// add_filter( 'relevanssi_hits_filter', 'exact_title_boost' );
function exact_title_boost( $hits ) {
	$terms						= relevanssi_tokenize( $hits[1], true, -1 );
	if ( count( $terms ) > 1 ) {
		$title_matches			= array();
		$the_rest				= array();
		foreach ( $hits[0] as $hit ) {
			$x					= 0;
			foreach ( array_keys( $terms ) as $term ) {
				if ( stripos( $hit->post_title, $term ) !== false )
					$x++;
			}

			$x == count( $terms ) ? $title_matches[] = $hit : $the_rest[] = $hit;
		}

		$hits[0]				= array_merge( $title_matches, $the_rest );
	}

	return $hits;
}

?>
