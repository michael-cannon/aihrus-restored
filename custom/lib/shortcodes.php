<?php
/**
 *  WordPress Thesis theme shortcode customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_shortcode( 'field', 'shortcode_field' );


/**
 * Add iframes to your WordPress post using custom fields and shortcodes
 *
 * Usage
 * [field name="map"]
 *
 * @ref http://www.vividvisions.com/2009/02/11/wordpress-add-iframes-to-your-post/
 * @return	string	shortcode's custom_meta contents
 */
function shortcode_field( $atts ) {
   global $post;
   
   $name						= $atts['name'];
   if ( empty( $name ) )
	   return;

   return get_post_meta( $post->ID, $name, true );
}

?>
