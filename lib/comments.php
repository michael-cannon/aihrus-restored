<?php
/**
 *  WordPress Thesis theme comment customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_action( 'thesis_hook_comment_form_top', 'janrain_login' );


function janrain_login() {
	if ( ! is_user_logged_in() ) {
		echo '<p class="leftie">';
		_e( 'Login with...' , 'custom');
		echo '</p>';
		echo do_shortcode('[rpxlogin]');
		echo '<div class="clear"></div>';
	}
}

?>
