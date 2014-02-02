<?php
/**
 *  WordPress theme customizations for FITV VideoZoom
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// require_once( __DIR__ . '/../lib/admin-filter.php' );
require_once( __DIR__ . '/../lib/admin.php' );
require_once( __DIR__ . '/../lib/attachments.php' );
require_once( __DIR__ . '/../lib/authors.php' );
// require_once( __DIR__ . '/../lib/comments.php' );
// require_once( __DIR__ . '/../lib/debug.php' );
require_once( __DIR__ . '/../lib/define.php' );
require_once( __DIR__ . '/../lib/excerpts.php' );
require_once( __DIR__ . '/../lib/gallery.php' );
// require_once( __DIR__ . '/../lib/GT_Breadcrumbs2.php' );
require_once( __DIR__ . '/../lib/javascript.php' );
// require_once( __DIR__ . '/../lib/pages.php' );
require_once( __DIR__ . '/../lib/posts.php' );
require_once( __DIR__ . '/../lib/query.php' );
require_once( __DIR__ . '/../lib/roles.php' );
require_once( __DIR__ . '/../lib/rss.php' );
require_once( __DIR__ . '/../lib/relevanssi.php' );
// require_once( __DIR__ . '/../lib/search.php' );
require_once( __DIR__ . '/../lib/class.search_video_widget.php' );
require_once( __DIR__ . '/../lib/shortcodes.php' );
require_once( __DIR__ . '/../lib/thumbnails.php' );
require_once( __DIR__ . '/../lib/template.php' );
require_once( __DIR__ . '/../lib/users.php' );
require_once( __DIR__ . '/../lib/widgets.php' );

require_once( __DIR__ . '/functions.php' );

if ( ! defined( 'EMPTY_TRASH_DAYS' ) )
	define( 'EMPTY_TRASH_DAYS', 30 );

// admin
add_action( 'admin_init', 'own_admin_init' );
add_action('admin_menu', 'mb_wpzoom_options_box');

remove_action('admin_menu', 'wpzoom_options_box');

if ( ! current_user_can('administrator') ) {
	add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ), 2 );
	add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
	// Disable WordPress Admin Bar for all users but admins
	show_admin_bar( false );
}

// footer
// add_action( 'wp_footer', 'fitv_vzaar_autostart', 99 );
// add_action( 'wp_footer', 'footer_analytics' );

// attachments
add_action( 'admin_init', 'register_attachment_taxonomy' );
add_filter( 'wp_generate_attachment_metadata', 'add_attachment_alt_text', '', 2 );
add_filter( 'wp_generate_attachment_metadata', 'add_attachment_post_tags', '', 2 );
add_filter( 'wp_read_image_metadata', 'read_all_image_metadata', '', 3 );
remove_filter('wp_generate_attachment_metadata', 'wp_smushit_resize_from_meta_data');

// authors

// gallery
add_shortcode('gallery', 'custom_gallery_shortcode');
remove_shortcode('gallery', 'gallery_shortcode');

// excerpts
// add_filter( 'get_the_excerpt', 'excerpt_read_more' );
add_filter( 'get_the_excerpt', 'excerpt_remove_social' );

// javascript

// posts
add_action( 'pre_ping', 'disable_self_ping' );
add_action( 'wp_enqueue_scripts', 'fitv_vzaar_chapters_scripts' );
add_image_size( 'wpzoom-feat-cat', 60, 45, true );
add_image_size( 'wpzoom-slider', 135, 98, true );
add_image_size( 'wpzoom-slider-thumb', 460, 360, true );
add_image_size( 'wpzoom-thumb', 228, 160, true );

// roles
add_action( 'admin_init', 'modify_role_author' );
add_action( 'admin_init', 'modify_role_contributor' );
add_action( 'admin_init', 'modify_role_editor' );

// comments

// next/prev links

// rss
add_filter( 'request', 'custom_rss_request' );
add_filter( 'the_content_feed', 'prepend_post_thumbnail' );
add_filter( 'the_excerpt_rss', 'prepend_post_thumbnail' );

// search
// add_filter( 'relevanssi_stemmer', 'relevanssi_simple_english_stemmer' );
add_filter( 'relevanssi_get_words_query', 'rel_get_words_query' );
add_filter( 'relevanssi_hits_filter', 'exact_title_boost' );
add_filter( 'relevanssi_hits_filter', 'fitv_hits_filter' );
add_filter( 'relevanssi_match', 'fitv_relevanssi_match' );
add_filter( 'get_search_form', 'fitv_get_search_form' );

// shortcodes
// add_filter( 'widget_text', 'do_shortcode' );
add_shortcode( 'field', 'shortcode_field' );

// users
add_filter( 'user_contactmethods','custom_user_contactmethods' );
if ( is_admin() ) {
	add_action('personal_options', 'prefix_hide_personal_options');
}

// query mods
add_filter( 'posts_fields', 'fitv_admin_posts_fields' );
add_filter( 'posts_orderby', 'fitv_admin_posts_orderby' );
add_filter( 'pre_get_posts', 'posts_for_current_author' );
add_filter( 'pre_get_posts', 'pre_get_posts_allow_video_document' );

// widgets
add_action( 'widgets_init', 'remove_wp_widgets', 1 );

// $breadcrumbs					= new GT_Breadcrumbs();
// $breadcrumbs->hook('thesis_hook_before_content');

load_theme_textdomain( 'custom', TEMPLATEPATH . '/languages' );

// Thank you to Cats Who Code for the localization how to
// @ref http://www.catswhocode.com/blog/how-to-make-a-translatable-wordpress-theme
$locale							= get_locale();
$locale_file					= TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable( $locale_file ) )
	require_once( $locale_file );

// require_once 'video-quick-edit.php';

global $mp;
remove_action( 'manage_posts_custom_column', array(&$mp, 'manage_orders_custom_columns') );
add_action( 'manage_product_posts_custom_column', array(&$mp, 'manage_orders_custom_columns') );

remove_action( 'manage_posts_custom_column', array(&$mp, 'edit_products_custom_columns') );
add_action( 'manage_product_posts_custom_column', array(&$mp, 'edit_products_custom_columns') );

if ( ( have_posts() && in_array( get_post_type( get_the_ID() ), array( 'video', 'document' ) ) ) || ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], array( 'video', 'document' ) ) ) ) {
	add_filter( 'get_terms', 'admin_get_terms', 10, 3 );
	add_filter( 'gettext', 'gettext_mbr' );
	add_filter( 'ngettext', 'gettext_mbr' );
	add_filter( 'posts_distinct', 'fitv_admin_posts_distinct' );
	add_filter( 'posts_join', 'fitv_admin_posts_join' );
	add_filter( 'posts_where', 'fitv_admin_posts_where' );
	add_filter( 'wp_dropdown_users', 'fitv_wp_dropdown_users' );
}

add_action( 'the_content', 'fitv_vzaar_chapters' );

remove_action( 'save_post', 'custom_add_save' );
add_action( 'save_post', 'mbi_save_post' );

// require_once 'video-map.php';
// add_shortcode( 'video_map', 'fitv_video_map' );

?>