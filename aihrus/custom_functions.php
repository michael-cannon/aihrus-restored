<?php
/**
 *  WordPress Aihrus theme customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// require_once( __DIR__ . '/../lib/GT_Breadcrumbs2.php' );
// require_once( __DIR__ . '/../lib/admin-filter.php' );
// require_once( __DIR__ . '/../lib/admin.php' );
require_once( __DIR__ . '/../lib/attachments.php' );
// require_once( __DIR__ . '/../lib/authors.php' );
// require_once( __DIR__ . '/../lib/comments.php' );
// require_once( __DIR__ . '/../lib/define.php' );
// require_once( __DIR__ . '/../lib/debug.php' );
// require_once( __DIR__ . '/../lib/excerpts.php' );
// require_once( __DIR__ . '/../lib/gallery.php' );
// require_once( __DIR__ . '/../lib/javascript.php' );
// require_once( __DIR__ . '/../lib/pages.php' );
// require_once( __DIR__ . '/../lib/posts.php' );
// require_once( __DIR__ . '/../lib/query.php' );
// require_once( __DIR__ . '/../lib/roles.php' );
// require_once( __DIR__ . '/../lib/rss.php' );
// require_once( __DIR__ . '/../lib/relevanssi.php' );
// require_once( __DIR__ . '/../lib/search.php' );
// require_once( __DIR__ . '/../lib/shortcodes.php' );
// require_once( __DIR__ . '/../lib/template.php' );
// require_once( __DIR__ . '/../lib/testimonials-widget.php' );
require_once( __DIR__ . '/../lib/thumbnails.php' );
// require_once( __DIR__ . '/../lib/users.php' );
// require_once( __DIR__ . '/../lib/widgets.php' );


// add_action( 'wp_enqueue_scripts', 'custom_stylesheet' );
function custom_stylesheet() {
	// Respects SSL, Style.css is relative to the current file
	wp_register_style( 'custom-style', get_bloginfo('template_directory'). '/custom/custom.css' );
	wp_enqueue_style( 'custom-style' );
}


// admin
// add_action( '_admin_menu', 'admin_menu_remove_editor', 1 );
// add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );
// add_action( 'admin_init', 'own_admin_init' );

if ( ! current_user_can('administrator') ) {
	add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ), 2 );
	add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
}

// footer
// add_action( 'wp_footer', 'footer_analytics' );

function aihrus_init() {
	if ( ! is_super_admin() )
		show_admin_bar( false );

	aihrus_scripts();
	aihrus_styles();
}

add_action( 'init', 'aihrus_init' );

function aihrus_scripts() {
	wp_register_script( 'zenbox', get_stylesheet_directory_uri() . '/js/zenbox.js' );
	wp_enqueue_script( 'zenbox' );
}


function aihrus_styles() {
	wp_register_style( 'zenbox', get_stylesheet_directory_uri() . '/css/zenbox.css' );
	wp_enqueue_style( 'zenbox' );
}


function aihrus_wp_footer() {
	$url = get_stylesheet_directory_uri();

	echo <<<EOD
<!-- Zendesk support -->
<script type="text/javascript">
	if (typeof(Zenbox) !== "undefined") {
		Zenbox.init({
			dropboxID:   "20182507",
			url:         "https://aihrus.zendesk.com",
			tabTooltip:  "Need Help?",
			tabImageURL: "{$url}/media/tab_ask_us_right.png",
			tabColor:    "#ff0000",
			tabPosition: "Right"
		});
	}
</script>
<!-- end Zendesk support -->

<!-- Google Analytics Code -->
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-20956818-1']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
<!-- end Google Analytics Code -->
EOD;
}
add_action( 'wp_footer', 'aihrus_wp_footer', 20 );

// attachments
add_action( 'admin_init', 'register_attachment_taxonomy' );
add_filter( 'wp_read_image_metadata', 'read_all_image_metadata', '', 3 );
add_filter( 'wp_generate_attachment_metadata', 'add_attachment_alt_text', '', 2 );
add_filter( 'wp_generate_attachment_metadata', 'add_attachment_post_tags', '', 2 );
// remove_filter('wp_generate_attachment_metadata', 'wp_smushit_resize_from_meta_data');

// call to action
// add_filter( 'the_content', 'custom_call_to_action', 29 );

// gallery
// remove_shortcode('gallery', 'gallery_shortcode');
// add_shortcode('gallery', 'custom_gallery_shortcode');

// authors
// add_filter( 'gettext', 'gettext_mbr' );
// add_filter( 'ngettext', 'gettext_mbr' );

// excerpts
// add_filter( 'get_the_excerpt', 'excerpt_read_more' );
// add_filter( 'get_the_excerpt', 'excerpt_remove_social' );

// javascript

// posts
add_action( 'pre_ping', 'disable_self_ping' );
// add_action( 'thesis_hook_after_headline', 'custom_byline' );
// wp_embed_register_handler( 'gist', '/https:\/\/gist\.github\.com\/(\d+)(\?file=.*)?/i', 'wp_embed_handler_gist' );

// roles
// add_action( 'admin_init', 'modify_role_editor' );
// add_action( 'admin_init', 'modify_role_author' );
// add_action( 'admin_init', 'modify_role_contributor' );

// comments

// next/prev links

// rss
// add_filter( 'the_content_feed', 'prepend_post_thumbnail' );
// add_filter( 'the_excerpt_rss', 'prepend_post_thumbnail' );
// add_filter( 'request', 'custom_rss_request' );

// search
// add_filter( 'relevanssi_stemmer', 'relevanssi_simple_english_stemmer' );

// shortcodes
add_filter( 'widget_text', 'do_shortcode' );
add_shortcode( 'field', 'shortcode_field' );

// thumbnails
// add_theme_support( 'post-thumbnails' );
// add_image_size( 'Slide', 940, 350, true );

// add_filter( 'the_excerpt', 'prepend_post_thumbnail' );
add_filter( 'the_content', 'prepend_post_thumbnail', 1 );

// users
add_filter( 'user_contactmethods','custom_user_contactmethods' );
if ( is_admin() )
	add_action('personal_options', 'prefix_hide_personal_options');

// query mods
// add_filter( 'pre_get_posts', 'posts_for_current_author' );
// add_filter( 'get_terms', 'admin_get_terms', 10, 3 );

// widgets
// add_action( 'widgets_init', 'remove_wp_widgets', 1 );

// $breadcrumbs					= new GT_Breadcrumbs();
// $breadcrumbs->hook('thesis_hook_before_content');

load_theme_textdomain( 'custom', TEMPLATEPATH . '/languages' );

// Thank you to Cats Who Code for the localization how to
// @ref http://www.catswhocode.com/blog/how-to-make-a-translatable-wordpress-theme
$locale							= get_locale();
$locale_file					= TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable( $locale_file ) )
	require_once( $locale_file );

add_filter( 'wp_new_user_notification_html', '__return_true' );

remove_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields' );

?>