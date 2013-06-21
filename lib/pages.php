<?php
/**
 *  WordPress Thesis theme pages customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_filter( 'thesis_show_sidebars', 'show_sidebars' );


function show_sidebars() {
	if ( is_front_page() && ! is_paged() )
		return false;

	return true;
}

?>
