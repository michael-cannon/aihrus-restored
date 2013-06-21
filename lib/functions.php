<?php
/**
 *  WordPress Thesis theme helper functions 
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


/**
 * Returns string of a filename or string converted to a spaced extension
 * less header type string.
 *
 * @author Michael Cannon <mc@aihr.us>
 * @param string filename or arbitrary text
 * @return mixed string/boolean
 */
function cbMkReadableStr( $str) {
	if ( is_numeric( $str ) ) {
		return number_format( $str );
	}

	if ( is_string( $str ) ) {
		$clean_str = htmlspecialchars( $str );

		// remove file extension
		$clean_str = preg_replace( '/\.[[:alnum:]]+$/i', '', $clean_str );

		// remove funky characters
		$clean_str = preg_replace( '/[^[:print:]]/', '_', $clean_str );

		// Convert camelcase to underscore
		$clean_str = preg_replace( '/([[:alpha:]][a-z]+)/', "$1_", $clean_str );

		// try to cactch N.N or the like
		$clean_str = preg_replace( '/([[:digit:]\.\-]+)/', "$1_", $clean_str );

		// change underscore or underscore-hyphen to become space
		$clean_str = preg_replace( '/(_-|_)/', ' ', $clean_str );

		// remove extra spaces
		$clean_str = preg_replace( '/ +/', ' ', $clean_str );

		// convert stand alone s to 's
		$clean_str = preg_replace( '/ s /', "'s ", $clean_str );

		// remove beg/end spaces
		$clean_str = trim( $clean_str );

		// capitalize
		$clean_str = ucwords( $clean_str );

		// reset words
		$clean_str = str_replace( __(' And ', 'custom'), __(' and ', 'custom'), $clean_str );
		$clean_str = str_replace( __(' Or ', 'custom'), __(' or ', 'custom'), $clean_str );

		// close ) back up
		$clean_str = str_replace( ' )', ')', $clean_str );

		// restore previous entities facing &amp; issues
		$clean_str = preg_replace( '/(&amp ;)([a-z0-9]+) ;/i'
			, '&\2;'
			, $clean_str
		);

		return $clean_str;
	}

	return false;
}


function object_to_unordered_list( $values, $include_ul = true ) {
	$string						= '';

	foreach ( $values as $key => $value ) {
		if ( is_object( $value ) || is_array( $value ) )
			$value			= object_to_unordered_list( $value );

		$key					= cbMkReadableStr($key);
		$string					.= '<li>' . $key . ': ' . $value . '</li>';
	}

	if ( $include_ul )
		$string				= '<ul>' . $string . '</ul>';

	return $string;
}


function time2seconds( $time ) {
	$time						= trim( $time );
	$timeParts					= explode( ':', $time );
	$seconds					= array_pop( $timeParts );

	if ( ! empty( $timeParts ) )
		$minutes				= array_pop( $timeParts );
	else
		$minutes				= 0;

	$seconds					+= ( $minutes * 60 );

	if ( ! empty( $timeParts ) )
		$hours					= array_pop( $timeParts );
	else
		$hours					= 0;

	$seconds					+= ( $hours * 60 * 60 );
	
	return $seconds;
}

/**
 * Helper for getting the current URL
 * @ref http://webcheatsheet.com/PHP/get_current_page_url.php
 */
function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER['HTTPS'] == 'on') {$pageURL .= 's';}
	$pageURL .= '://';
	if ($_SERVER['SERVER_PORT'] != '80') {
		$pageURL .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
	} else {
		$pageURL .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	}
	return $pageURL;
}

?>
