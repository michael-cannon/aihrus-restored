<?php
/**
 *  WordPress Thesis theme widget customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_action( 'thesis_hook_footer','custom_footer_widgets', 1 );
$widget_footer_count			= 3;


/**
 * widgetised footer
 */
function custom_footer_widgets() {
	global $widget_footer_count;

	// if ( is_front_page() && ! is_paged() )
		// return;

	if ( isset( $widget_footer_count ) && 0 >= $widget_footer_count )
		return;

	echo '<div id="footer_widgets" class="sidebar">';

	// twenty eleven starts with 3 for footer
	for ( $i = 1; $i <= $widget_footer_count; $i++ ) {
		$j						= $i + 2;
		echo '<ul class="sidebar_list">';
		thesis_default_widget('sidebar-' . $j);
		echo '</ul>';
	}

	echo '<div class="clear"></div>';
	echo '</div>';
}


// TODO register during init
// register extra widget areas
if ( false && isset( $widget_footer_count ) && 0 < $widget_footer_count ) {
	for ( $i = 1; $i <= $widget_footer_count; $i++ ) {
		$j						= $i + 2;
		register_sidebar(
			array(
				'id' => 'sidebar-' . $j,
				'name' => __("Footer {$i}", 'custom'),
				'before_widget' => '<li class="widget %2$s" id="%1$s">',
				'after_widget' => '</li>',
				'before_title' => '<h3>',
				'after_title' => '</h3>'
			)
		);
	}
}

// add_action( 'widgets_init', 'remove_wp_widgets', 1 );

function remove_wp_widgets() {
	unregister_widget( 'WP_Widget_Archives' );
	unregister_widget( 'WP_Widget_Calendar' );
	unregister_widget( 'WP_Widget_Categories' );
	unregister_widget( 'WP_Widget_Links' );
	unregister_widget( 'WP_Widget_Meta' );
	unregister_widget( 'WP_Widget_Pages' );
	// unregister_widget( 'WP_Widget_Recent_Comments' );
	// unregister_widget( 'WP_Widget_Recent_Posts' );
	unregister_widget( 'WP_Widget_RSS' );
	// unregister_widget( 'WP_Widget_Search' );
	// unregister_widget( 'WP_Widget_Tag_Cloud' );
	// unregister_widget( 'WP_Widget_Text' );
}

// captures contents of sidebar to cache
function t3vb_dynamic_sidebar_cache( $index = 1 ) {
	if ( ! function_exists( 'dynamic_sidebar' ) ) {
		return false;
	}

	$sidebar = get_transient( 'dynamic_sidebar_' . $index );

	if ( false === $sidebar ) {
		ob_start();
		dynamic_sidebar( $index );
		$sidebar				= ob_get_contents();
		ob_end_clean();
		set_transient( 'dynamic_sidebar_' . $index, $sidebar, T3VB_TIME_HOUR );
	}

	if ( ! empty( $sidebar ) ) {
		echo $sidebar;
		return true;
	} else {
		return false;
	}
}

?>
