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
require_once( __DIR__ . '/../lib/gallery.php' );
require_once( __DIR__ . '/../lib/javascript.php' );
// require_once( __DIR__ . '/../lib/pages.php' );
require_once( __DIR__ . '/../lib/posts.php' );
// require_once( __DIR__ . '/../lib/query.php' );
// require_once( __DIR__ . '/../lib/roles.php' );
// require_once( __DIR__ . '/../lib/rss.php' );
// require_once( __DIR__ . '/../lib/relevanssi.php' );
require_once( __DIR__ . '/../lib/search.php' );
require_once( __DIR__ . '/../lib/shortcodes.php' );
// require_once( __DIR__ . '/../lib/template.php' );
// require_once( __DIR__ . '/../lib/testimonials-widget.php' );
require_once( __DIR__ . '/../lib/thumbnails.php' );
require_once( __DIR__ . '/../lib/users.php' );
// require_once( __DIR__ . '/../lib/widgets.php' );

require_once __DIR__ . '/shortcodes.php' ;

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
<!-- ClickTale Bottom part -->
<div id="ClickTaleDiv" style="display: none;"></div>
<script type="text/javascript">
	if(document.location.protocol != 'https:') document.write(unescape("%3Cscript%20src='" +'http://cdn.clicktale.net/www/' + "WRe6.js'%20type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	if(typeof ClickTale=='function')  ClickTale(6494, 0.5, "www08");
</script>
<!-- ClickTale end of Bottom part -->

<!-- Zendesk support -->
<script type="text/javascript">
	if (typeof(Zenbox) !== "undefined") {
		Zenbox.init({
			dropboxID:   "20182507",
			url:         "https://aihrus.zendesk.com",
			tabTooltip:  "Need Help?",
			tabImageURL: "{$url}/media/tab_support_right.png",
			tabColor:    "#ff0000",
			tabPosition: "Right"
		});
	}
</script>
<!-- end Zendesk support -->
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
remove_shortcode( 'gallery', 'gallery_shortcode' );
add_shortcode( 'gallery', 'custom_gallery_shortcode' );

// authors
// add_filter( 'gettext', 'gettext_mbr' );
// add_filter( 'ngettext', 'gettext_mbr' );

// excerpts
// add_filter( 'get_the_excerpt', 'excerpt_read_more' );
// add_filter( 'get_the_excerpt', 'excerpt_remove_social' );

// javascript
add_filter( 'script_loader_src', 'aihrus_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'aihrus_remove_script_version', 15, 1 );

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
add_filter( 'pre_get_posts', 'aihrus_search_all_post_types' );

// shortcodes
add_filter( 'widget_text', 'do_shortcode' );
add_shortcode( 'field', 'shortcode_field' );

// thumbnails
// add_theme_support( 'post-thumbnails' );
// add_image_size( 'Slide', 940, 350, true );

// add_filter( 'the_excerpt', 'prepend_post_thumbnail' );
add_filter( 'the_content', 'aihrus_prepend_post_thumbnail', 1 );

function aihrus_prepend_post_thumbnail( $content ) {
	$post_type = get_post_type( get_the_ID() );
	if ( ! in_array( $post_type, array( 'slides', 'download' ) ) )
		$content = prepend_post_thumbnail( $content );

	return $content;
}

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

// add_filter( 'option_siteurl', 'aihrus_option_siteurl' );
function aihrus_option_siteurl( $siteurl ) {
	if ( ! empty( $siteurl ) ) {
		$siteurl = preg_replace( '#https?:#', '', $siteurl );
	}

	return $siteurl;
}

define( 'EDD_BYPASS_NAME_CHECK', true );

// add_filter( 'wp_mail_from', 'aihrus_mail_from' );
function aihrus_mail_from( $email ) {
	// NOTE: replace [at] with @. This was causing problems with the syntax highlighter.
	return 'support@aihr.us';
}

// add_filter( 'wp_mail_from_name', 'aihrus_mail_from_name' );
function aihrus_mail_from_name( $name ) {
	return 'Aihrus Support';
}

remove_action( 'template_redirect', 'edd_csau_checkout_display' );


function aihrus_edd_discount_field() {

	if( ! isset( $_GET['payment-mode'] ) && count( edd_get_enabled_payment_gateways() ) > 1 && ! edd_is_ajax_enabled() )
		return; // Only show once a payment method has been selected if ajax is disabled

	if ( edd_has_active_discounts() && edd_get_cart_total() ) {
?>
	<fieldset id="edd_discount_code">
		<span><legend><?php _e( 'Have a Discount Code?', 'edd' ); ?></legend></span>
		<p id="edd_show_discount" style="display:none;">
			<a href="#" class="edd_discount_link"><?php echo _x( 'Click to enter it', 'Entering a discount code', 'edd' ); ?></a>
		</p>
		<p id="edd-discount-code-wrap">
			<label class="edd-label" for="edd-discount">
				<img src="<?php echo EDD_PLUGIN_URL; ?>assets/images/loading.gif" id="edd-discount-loader" style="display:none;"/>
				<?php _e( 'Discount Code', 'edd' ); ?>
			</label>
			<span class="edd-description"><?php _e( 'Enter a coupon code if you have one.', 'edd' ); ?></span>
			<input class="edd-input" type="text" id="edd-discount" name="edd-discount" placeholder="<?php _e( 'Enter discount', 'edd' ); ?>"/>
		</p>
	</fieldset>
<?php
	}
}


remove_action( 'edd_checkout_form_top', 'edd_discount_field', -1 );
add_action( 'edd_checkout_form_top', 'aihrus_edd_discount_field', -1 );


function aihrus_edd_sl_renewal_form() {

	if( ! edd_sl_renewals_allowed() )
		return;

	$apply_url   = add_query_arg( array( 'edd_action' => 'apply_license_renewal' ), edd_get_checkout_uri() );
	$apply_url   = wp_nonce_url( $apply_url, 'edd_apply_license_renewal' );
	$renewal     = EDD()->session->get( 'edd_is_renewal' );
	$renewal_key = EDD()->session->get( 'edd_renewal_key' );
	$preset_key  = ! empty( $_GET['key'] ) ? urldecode( $_GET['key'] ) : '';

	ob_start(); ?>
	<form method="post" id="edd_sl_renewal_form">
		<fieldset id="edd_sl_renewal_fields">
			<span><legend><?php _e( 'Renewing a License Key?', 'edd' ); ?></legend></span>
			<?php if( empty( $renewal ) || empty( $renewal_key ) ) : ?>
				<p id="edd-license-key-wrap">
					<label class="edd-label" for="edd-license-key"><?php _e( 'License Key', 'edd' ); ?></label>
					<span class="edd-description"><?php _e( 'Enter the license key you wish to renew. Leave blank to purchase a new one.', 'edd' ); ?></span>
					<input class="edd-input required" type="text" name="edd_license_key" placeholder="<?php _e( 'Enter your license key', 'edd' ); ?>" id="edd-license-key" value="<?php echo $preset_key; ?>"/>
				</p>
				<p id="edd-license-key-submit">
					<input type="hidden" name="edd_action" value="apply_license_renewal"/>
					<input type="submit" class="edd-submit button" value="<?php _e( 'Apply License Renewal', 'edd_sl' ); ?>"/>
				</p>
			<?php else : ?>
				<p id="edd-license-key-wrap">
					<label class="edd-label" for="edd-license-key"><?php _e( 'License Key Being Renewed', 'edd' ); ?></label>
					<span class="edd-renewing-key"><?php echo $renewal_key; ?></span>
				</p>
				<p id="edd-license-key-submit">
					<input type="hidden" name="edd_action" value="cancel_license_renewal"/>
					<input type="submit" class="edd-submit button" value="<?php _e( 'Cancel License Renewal', 'edd_sl' ); ?>"/>
				</p>
			<?php endif; ?>
		</fieldset>
	</form>
<?php
	echo ob_get_clean();
}


remove_action( 'edd_before_purchase_form', 'edd_sl_renewal_form', -1 );
add_action( 'edd_before_purchase_form', 'aihrus_edd_sl_renewal_form', -1 );


function aihrus_edd_payment_mode_select() {
	$gateways = edd_get_enabled_payment_gateways();
	$page_URL = edd_get_current_page_url();
	do_action('edd_payment_mode_top'); ?>
	<?php if( ! edd_is_ajax_enabled() ) { ?>
	<form id="edd_payment_mode" action="<?php echo $page_URL; ?>" method="GET">
	<?php } ?>
		<fieldset id="edd_payment_mode_select">
			<?php do_action( 'edd_payment_mode_before_gateways_wrap' ); ?>
			<div id="edd-payment-mode-wrap">
				<span><legend><?php _e( 'Select Payment Method', 'edd' ); ?></legend></span>
<?php

	do_action( 'edd_payment_mode_before_gateways' );

	foreach ( $gateways as $gateway_id => $gateway ) :
		$checked = checked( $gateway_id, edd_get_default_gateway(), false );
	$checked_class = $checked ? ' edd-gateway-option-selected' : '';
	echo '<label for="edd-gateway-' . esc_attr( $gateway_id ) . '" class="edd-gateway-option' . $checked_class . '" id="edd-gateway-option-' . esc_attr( $gateway_id ) . '">';
	echo '<input type="radio" name="payment-mode" class="edd-gateway" id="edd-gateway-' . esc_attr( $gateway_id ) . '" value="' . esc_attr( $gateway_id ) . '"' . $checked . '>' . esc_html( $gateway['checkout_label'] );
	echo '</label>';
endforeach;

do_action( 'edd_payment_mode_after_gateways' );

?>
			</div>
			<?php do_action( 'edd_payment_mode_after_gateways_wrap' ); ?>
		</fieldset>
		<fieldset id="edd_payment_mode_submit" class="edd-no-js">
			<p id="edd-next-submit-wrap">
				<?php echo edd_checkout_button_next(); ?>
			</p>
		</fieldset>
	<?php if( ! edd_is_ajax_enabled() ) { ?>
	</form>
	<?php } ?>
	<div id="edd_purchase_form_wrap"></div><!-- the checkout fields are loaded into this-->
<?php do_action('edd_payment_mode_bottom');
}


remove_action( 'edd_payment_mode_select', 'edd_payment_mode_select' );
add_action( 'edd_payment_mode_select', 'aihrus_edd_payment_mode_select' );


global $edd_slg_render;
remove_action( 'edd_checkout_form_top', array( $edd_slg_render, 'edd_slg_social_login_buttons' ) );
add_action( 'edd_before_purchase_form', array( $edd_slg_render, 'edd_slg_social_login_buttons' ), -2 );

// @ref http://wordpress.stackexchange.com/a/113550/8219
function my_embed_oembed_html( $html ) {
	return preg_replace( '@src="https?:@', 'src="', $html );
}
add_filter( 'embed_oembed_html', 'my_embed_oembed_html' );

// @ref http://stackoverflow.com/questions/11821419/wordpress-plugin-notifications
$func = function ($a) {
	global $wp_version;

	return (object) array(
		'last_checked' => time(),
		'version_checked' => $wp_version,
	);
};
// add_filter( 'pre_site_transient_update_core', $func );
// add_filter( 'pre_site_transient_update_plugins', $func );
// add_filter( 'pre_site_transient_update_themes', $func );

unregister_widget('featured_user_widget');

// gist https://gist.github.com/michael-cannon/b8b5f1e3925fd918f534
// tests
// TWP http://aihrus.localhost/checkout/?edd_license_key=9499c673384035e2fcfa148ffd5a227d&download_id=14714
// WPSEO CBQE http://aihrus.localhost/checkout/?edd_license_key=9fdc2b216fa170570a07f9919be21957&download_id=19963
// CBQEP http://aihrus.localhost/checkout/?edd_license_key=3c94b00010856765fe1ae63ba07fb3fe&download_id=17383
add_filter( 'edd_add_to_cart_item', 'aihrus_add_to_cart_item' );
function aihrus_add_to_cart_item( $item ) {
	if ( empty( $_GET['edd_license_key'] ) ) {
		return $item;
	} else {
		$license = ! empty( $_GET['edd_license_key'] ) ? sanitize_text_field( $_GET['edd_license_key'] ) : false;
		if ( ! $license ) {
			return $item;
		}
	}

	$license_id = edd_software_licensing()->get_license_by_key( $license );
	if ( empty( $license_id ) ) {
		return $item;
	}

	$download_id = ! empty( $_GET['download_id'] ) ? absint( $_GET['download_id'] ) : false;
	if ( empty( $download_id ) ) {
		return $item;
	}

	$payment_id   = get_post_meta( $license_id, '_edd_sl_payment_id', true );
	$cart_details = edd_get_payment_meta_cart_details( $payment_id, true );
	if ( empty( $cart_details ) ) {
		return $item;
	}

	$base_prices = array(
		14714 => 29.99,
		17383 => 39.99,
		19963 => 14.99,
	);
	$base_price  = $base_prices[ $download_id ];

	$price_ids = array(
		14714 => 3,
		17383 => 3,
		19963 => 3,
	);
	$price_id  = $price_ids[ $download_id ];
	foreach ( $cart_details as $key => $detail ) {
		if ( empty( $detail['id'] ) || $download_id != $detail['id'] ) {
			continue;
		}

		if ( empty( $detail['price'] ) ) {
			continue;
		}

		$price   = $detail['price'];
		$compare = bccomp( $base_price, $price, 2 );
		if ( 1 == $compare ) {
			$item['options']['price_id'] = $price_id;
		} elseif ( 0 == $compare ) {
			$item['options']['price_id'] = $price_id;
		}
	}

	return $item;
}
?>