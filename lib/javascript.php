<?php
/**
 *  JavaScript helpers
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

function aihrus_remove_script_version( $src ) {
	$parts = explode( '?', $src );

	return $parts[0];
}

?>