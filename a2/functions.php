<?php
// To do when Theme is first activated
if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
      wp_redirect( admin_url( 'admin.php?page=themedy&thesis_activated=true' ) );
	  exit;
}

require_once(TEMPLATEPATH.'/functions.php'); // Start Thesis Engine
require_once(STYLESHEETPATH.'/lib/init.php'); // Start Themedy Options

// CSS Filters
remove_filter('thesis_css', 'thesis_gravity_forms_support');
add_filter('thesis_css', 'add_custom_css', 10, 5);
function add_custom_css($contents, $thesis_css_object, $style, $multisite, $child_style) {
	// $style is TEMPLATEPATH . '/style.css' which is the primary Thesis stylesheet. We'll build the css without it.
	$thesis_css_object->build(); // This builds what would be in layout.css
	$css = $thesis_css_object->css . $child_style; // Thesis will build the whole thing according to the design options setting
	return $css;
}

// New Image Sizes
add_image_size('Home Thumb', 201, 125, TRUE);

// Add Featured Image Support
add_theme_support( 'post-thumbnails' );

// Add WP Background Image Support
add_theme_support( 'custom-background', array(
	// Background color default
	'default-color' => 'FFFFFF',
	// Background image default
	'default-image' => get_stylesheet_directory_uri() . '/images/bg-body.jpg',
	// Template header style callback
	'wp-head-callback' => 'custom_background_themedy'
) );

// Add WP Header Image Support
add_theme_support( 'custom-header', array(
	// Header image default
	'default-image'			=> get_stylesheet_directory_uri() . '/images/header.png',
	// Header text display default
	'header-text'			=>  true,
	// Header text color default
	'default-text-color'		=> '333',
	// Header image width (in pixels)
	'flex-width'        => true,
	'width'				=> 155,
	// Header image height (in pixels)
	'flex-height'       => true,
	'height'			=> 70,
	// Header image random rotation default
	'random-default'		=> false,
	// Template header style callback
	'wp-head-callback'		=> 'themedy_header_style',
	// Admin header style callback
	'admin-head-callback'		=> 'admin_header_style',
	// Admin preview style callback
	'admin-preview-callback'	=> ''
) ); 

function themedy_header_style() {
	if (themedy_get_option('full_width_header')) { ?>
	<style type="text/css">
			#header {
				padding: 0px;
				margin: 0px;
				height: auto;
			}
			#header .wrap {
				padding: 0px;
			}
			.header-image #header a {
    			text-indent: 0px;
			}
			#header_area .page {
				padding: 0px;
				margin: 0px;
			}
			#header_area {
				height: auto;
			}
	</style>
	<?php }
	else { ?>
		<style type="text/css">
       .header-image #header_area #header #logo a {
            background: url(<?php header_image(); ?>) left no-repeat;
            width: <?php echo get_custom_header()->width; ?>px;
            height: <?php echo get_custom_header()->height; ?>px;
        }
        #header_area {
        	padding-bottom: 60px;
        	height: <?php echo get_custom_header()->height; ?>px;
        }
        .header-image #header a {
    			text-indent: -9999px;
			}
    </style><?php }

}
function admin_header_style() {
	?><style type="text/css">
        #headimg {
            width: <?php echo get_custom_header()->width; ?>px;
            height: <?php echo get_custom_header()->height; ?>px;
        }
    </style><?php
}

function custom_background_themedy() {

	$repeat = get_theme_mod( 'background_repeat', 'repeat' );
	$position = get_theme_mod( 'background_position_x', 'left' );
	$attachment = get_theme_mod( 'background_attachment', 'scroll' );

	?><style type="text/css">
		body {
			background: url(<?php background_image(); ?>);
			background-repeat: <?php echo $repeat; ?>;
			background-position: <?php echo $position; ?> top;
			background-attachment: <?php echo $attachment; ?>;
			background-color: #<?php background_color(); ?>;
		}
	</style><?php
}

if (themedy_get_option('full_width_header')) { 
	remove_action ('thesis_hook_header','thesis_default_header');
	add_action ('thesis_hook_header','themedy_do_header'); 
}

function themedy_do_header() { 
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( header_image() ); ?>" alt="<?php bloginfo( 'description' ); ?>" /></a>
	<?php
}

// Viewport scale (iPhone)
add_action('wp_head','themedy_viewport_meta');
function themedy_viewport_meta() { 
	?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php
}

// Add our style (set in options panel)
add_action('init', 'themedy_load_styles');
function themedy_load_styles() {
	if (!is_admin()) { 
		wp_enqueue_style('themedy-child-theme-style', STYLES_URL.'/'.themedy_get_option('style').'.css',$deps,CHILD_THEME_VERSION,'screen');
		wp_enqueue_script('hoverIntent', CHILD_URL.'/lib/js/hoverIntent.js', array('jquery'), '1.4.8', TRUE);
		wp_enqueue_script('superfish', CHILD_URL.'/lib/js/superfish.js', array('jquery'), '1.4.8', TRUE);
	}
}

// Custom Body Classes
add_filter('thesis_body_classes', 'custom_body_class');
function custom_body_class($classes) {	
	if(is_single())
		$classes[] = 'single-post';
	if(is_page())
		$classes[] = 'single-page';
	if ( 'blank' == get_header_textcolor() )
		$classes[] = 'header-image';
		
	// Get Columns Layout and add class
	global $thesis_design; 
	$layout = $thesis_design->layout;
	$order = $layout["order"];
	$columns = $layout["columns"];
	
	if ($columns == '1')
		$classes[] = 'full-content';
		elseif ($order == "0" or $order == "")
			$classes[] = 'sidebar-content';
		elseif ($order == "normal")
			$classes[] = 'content-sidebar';
		elseif ($order == "invert")
			$classes[] = 'sidebar-content-sidebar';
		
	// Page Template Class
	global $wp_query;
	$template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );
	if ($template_name)
		$template_name = str_replace(".", "-", $template_name);
		$classes[] = $template_name;
	
	return $classes;
	
}

// Add top pull down area
add_action('thesis_hook_before_html', 'themedy_extra_area');
function themedy_extra_area() {
    if (is_active_sidebar('top-pulldown-area')) { 
	?>        
        <div id="extra-area" class="full_width">
            <div class="page">
                <span id="expand-button"><?php echo themedy_option('expand_text'); ?></span>
                <div id="extra-area-widget" class="widget-area" style="display: none;">
                    <?php dynamic_sidebar('Top Pulldown Area') ; ?> 
                </div>
            </div>
        </div>
    <?php 
	}
}

// Add Wrap Container for Page Layout
add_action('thesis_hook_before_html', 'themedy_wrap_start');
function themedy_wrap_start() {
	?> 
    <div id="wrap" class="page">
	<?php
}
add_action('thesis_hook_after_content_area', 'themedy_wrap_end');
function themedy_wrap_end() {
	?> 
    </div> 
	<?php
}

// Move the navbar above header
remove_action('thesis_hook_before_header', 'thesis_nav_menu');
add_action('thesis_hook_before_html', 'navigation_area');
function navigation_area() { ?>
    <div id="navigation_area" class="full_width">
       <?php thesis_nav_menu(); ?>
    </div>
<?php }

// Add Header Widget Area
add_action('thesis_hook_after_header', 'themedy_header_widget');
function themedy_header_widget() {
	if (is_active_sidebar('header-right') and !themedy_get_option('full_width_header')) { ?>
		<div class="widget-area">
			<?php dynamic_sidebar('Header Right') ; ?> 	
		</div>
	<?php }
}

// Add Widget Area Above Inner
add_action('thesis_hook_before_content_area', 'themedy_above_content_area');
function themedy_above_content_area() { 
    if (is_active_sidebar('above-content-area') and !is_front_page()) { 
	?>
        <div id="single-header-widget" class="full_width">
            <div class="page">
                <div class="widget_area">
                    <?php dynamic_sidebar('Above Content Area') ; ?> 
                </div>
            </div>
        </div>
	<?php 
	}
}

// Remove Post Comment Link
remove_action('thesis_hook_after_post', 'thesis_comments_link');

// Add Author Box
if (themedy_get_option('author_box')) { add_action('thesis_hook_after_post','thesis_author_box'); }
function thesis_author_box() {
	if (is_single() && ! is_singular( 'testimonials-widget' ) ) {
		global $authordata;
		
		$authordata    = is_object( $authordata ) ? $authordata : get_userdata( get_query_var( 'author' ) );
		$gravatar      = get_avatar( get_the_author_meta( 'email' ), 70 );
		$title         = 'About '. get_the_author() .'';
		$description   = wpautop( get_the_author_meta( 'description' ) );
		
		?>
        
        <div class="author-box">
        	<div>
            	<?php echo $gravatar; ?>
                <strong><?php echo $title; ?></strong>
                <br />
                <?php echo $description; ?>
            </div>
        </div>
        
        <?php
	}
}

// Add Single Footer Widget Area (twitter)
add_action('thesis_hook_after_content_area', 'themedy_single_footer_widget', 5);
function themedy_single_footer_widget() { 
    if (is_active_sidebar('below-content-area')) { 
	?>
        <div id="single-footer-widget" class="full_width">
            <div class="page">
                <div class="widget_area">
                    <?php dynamic_sidebar('Below Content Area') ; ?> 
                </div>
            </div>
        </div>
	<?php 
	}
}

// Add Widget Footer
add_action('thesis_hook_after_content_area', 'themedy_widget_footer', 5);
function themedy_widget_footer() { ?>
	<?php if (is_active_sidebar('footer-1') or is_active_sidebar('footer-2') or is_active_sidebar('footer-3')) { ?>
    <div id="footer_widgets_area" class="full_width">
    	<div class="page">
        	<div id="footer-widgets" class="footer-widgets format_text">
            	<?php if (is_active_sidebar('footer-1')) { ?>
                    <div class="widget-area footer-widgets-1">
                        <?php dynamic_sidebar('Footer 1') ; ?> 
                    </div>
                <?php } ?>
                <?php if (is_active_sidebar('footer-2')) { ?>
                    <div class="widget-area footer-widgets-2">
                        <?php dynamic_sidebar('Footer 2') ; ?> 	
                    </div>
                <?php } ?>
                <?php if (is_active_sidebar('footer-3')) { ?>
                    <div class="widget-area footer-widgets-3">
                        <?php dynamic_sidebar('Footer 3') ; ?> 
                    </div>
                <?php } ?>
        	</div>
        </div>
    </div>
    <?php } ?>
<?php }

// Add Custom Footer
if (themedy_get_option('footer')) { 
	remove_action('thesis_hook_footer', 'thesis_attribution');
	add_action('thesis_hook_footer', 'custom_footer');
	function custom_footer() { ?>
		<div class="gototop">
            <p><a rel="nofollow" href="#header_area">Back to Top</a></p>
        </div>
        <div class="creds">
            <p><?php themedy_option('footer_text'); ?></p>
        </div>
	<?php 
	}
}

// Superfish Menus and Top Area
add_action('thesis_hook_after_html','themedy_jquery');
function themedy_jquery() { ?>
	<script type="text/javascript">
        jQuery(document).ready(function($) { 
            jQuery('ul.menu').superfish({
                delay:       100,								// 0.1 second delay on mouseout 
                animation:   {opacity:'show',height:'show'},	// fade-in and slide-down animation 
                dropShadows: false								// disable drop shadows 
            });
        });
    </script>
	<?php if (is_active_sidebar('top-pulldown-area')) { ?>
	<script type='text/javascript'>
    jQuery(document).ready(function(){
        var expandContent = jQuery('#expand-button').html();
        jQuery('#expand-button').toggle(function(){
            jQuery('#extra-area-widget').slideDown('slow', function(){jQuery('#expand-button').html("Close")});
            
        }, function(){
            jQuery('#extra-area-widget').slideUp('slow', function(){jQuery('#expand-button').html(expandContent)});
        });
    });
    </script>
	<?php } ?>
<?php }

// Register widget areas
register_sidebar(array(
	'name' => 'Top Pulldown Area', 
	'id' => 'top-pulldown-area',
	'description' => 'This is the top pull down area (above everything).',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>',
	'before_widget' =>  '<div id="%1$s" class="widget %2$s">','after_widget' =>  '</div>',
));
register_sidebar(array(
	'name'=>'Header Right',
	'id' => 'header-right',
	'description' => 'This is the widget area in the header.',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));
register_sidebar(array(
	'name' => 'Above Content Area', 
	'id' => 'above-content-area',
	'description' => 'This is the widget area directly  above content and below header.', 
	'before_widget' =>  '<div id="%1$s" class="widget %2$s">',
	'after_widget' =>  '</div>',
));
register_sidebar(array(
	'name' => 'Below Content Area', 
	'id' => 'below-content-area',
	'description' => 'This is the widget area below content and above footer.', 
	'before_widget' =>  '<div id="%1$s" class="widget %2$s">',
	'after_widget' =>  '</div>',
));
register_sidebar(array(
	'name'=>'Homepage Top Area',
	'id' => 'homepage-top-area',
	'description' => 'This is the top area of the homepage.',
	'before_title'=>'<h4>','after_title'=>'</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));
register_sidebar(array(
	'name'=>'Homepage 1',
	'id' => 'homepage-1',
	'description' => 'This is first widget column of the homepage.',
	'before_title'=>'<h4>','after_title'=>'</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));
register_sidebar(array(
	'name'=>'Homepage 2',
	'id' => 'homepage-2',
	'description' => 'This is second widget column of the homepage.',
	'before_title'=>'<h4>','after_title'=>'</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));
register_sidebar(array(
	'name'=>'Homepage 3',
	'id' => 'homepage-3',
	'description' => 'This is third widget column of the homepage.',
	'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));
register_sidebar(array(
	'name'=>'Recent Work Column',
	'id' => 'recent-work-column',
	'description' => 'This is Recent Work Column column of the homepage (A text widget works best).',
	'before_title'=>'<h4>','after_title'=>'</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));
register_sidebar(array(
	'name'=>'Latest Posts Column',
	'id' => 'latest-posts-column',
	'description' => 'This is Latest Posts Column of the homepage (A text widget works best).',
	'before_title'=>'<h4>','after_title'=>'</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));
register_sidebar(array(
	'name' => 'Footer 1',
	'id' => 'footer-1',
	'before_title' => '<h4>','after_title' => '</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));
register_sidebar(array(
	'name' => 'Footer 2',
	'id' => 'footer-2',
	'before_title' => '<h4>', 'after_title' => '</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));
register_sidebar(array(
	'name' => 'Footer 3',
	'id' => 'footer-3',
	'before_title' => '<h4>', 'after_title' => '</h4>',
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>'
));


// gallery
require_once( __DIR__ . '/../lib/gallery.php' );
remove_shortcode('gallery', 'gallery_shortcode');
add_shortcode('gallery', 'custom_gallery_shortcode');
?>