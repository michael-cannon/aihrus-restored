<?php
/**
 *  WordPress template helpers
 *
 *  @author Michael Cannon <mc@aihr.us>
 */
function t3vb_get_template_part_cache( $slug, $name = null ) {
	if ( ! function_exists( 'get_template_part' ) ) {
		return false;
	}

	$content = get_transient( 't3vb_get_template_part_' . $slug . '-' . $name );

	if ( false === $content ) {
		ob_start();
		get_template_part( $slug, $name );
		$content				= ob_get_contents();
		ob_end_clean();
		set_transient( 't3vb_get_template_part_' . $slug . '-' . $name, $content, T3VB_TIME_HOUR );
	}

	echo $content;
}

?>
