<?php
/**
 *  WordPress user customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_filter( 'user_contactmethods','custom_user_contactmethods' );


function custom_user_contactmethods( $contactmethods ) {
	// unset( $contactmethods['aim'] );
	// unset( $contactmethods['yim'] );
	// unset( $contactmethods['jabber'] );

	$contactmethods['facebook']	= __( 'Facebook', 'custom' );
	$contactmethods['twitter']	= __( 'Twitter', 'custom' );
	$contactmethods['linkedin']	= __( 'LinkedIn', 'custom' );

	return $contactmethods;
}


// remove nickname
// @ref http://wpmu.org/how-to-restrict-usernames-and-disable-nicknames-in-wordpress/
function prefix_hide_personal_options() {
	if (current_user_can('manage_options')) return false;
?>
	<script type="text/javascript">
	jQuery(document).ready(function( $ ){
		$("#nickname,#display_name").parent().parent().remove();
	});
	</script>
<?php
}

// if ( is_admin() ) { add_action('personal_options', 'prefix_hide_personal_options'); }

?>
