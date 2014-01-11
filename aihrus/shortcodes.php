<?php
// A collection of shortcodes integrated into the theme for simple use of repetitive items.
// To use in a theme's template file / .php doc, use <?php do_shortcode('[shortcode]'); ? > (no space between ? and >)
// Let's go!

/*****/

// Buttons
function digigit_buttons( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'type' => '', /* radius, round */
	'size' => 'medium', /* tiny, small, medium, large */
	'color' => 'blue', /* success, alert, secondary */
	'url'  => '',
	'text' => '', 
	), $atts ) );

	$output = '<a href="' . $url . '" class="button '. $type . ' ' . $size . ' ' . $color;
	$output .= '">';
	$output .= $text;
	$output .= '</a>';

	return $output;
}

add_shortcode('button', 'digigit_buttons'); 

// To use: [button type="(radius round)" size="(small medium large)" color="(blue black red white)" nice="true false" text="Button text" url="http://google.com"]


/*****/

// Alerts
function alerts( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'type' => '	', /* sucess, alert, secondary */
	'close' => 'false', /* display close link */
	'text' => '', 
	), $atts ) );

	$output = '<div class="fade in alert-box '. $type . '">';

	$output .= $text;
	if($close == 'true') {
		$output .= '<a class="close" href="#">×</a>';
	}
	
	$output .= '</div>';
	
	return $output;
	
}

add_shortcode('alert', 'alerts');

// To use: [alert type="(default warning success error)" close="(true false)" text="Alert text goes here."]


/*****/

// Panels (same as comments)
function panels( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'type' => ' ', /* callout */
	'corner' => ' ', /* radius */
	'text' => '', 
	), $atts ) );

	$output = '<div class="panel '. $type . ' '. $corner . '">';
	$output .= $text;
	$output .= '</div>';

	return $output;
}

add_shortcode('panel', 'panels');

// To use: [panel text="This is a panel."]


/*****/

// Home link
function digigit_home( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'text' => 'Home!',
	), $atts ) );

	$output = '<a href="';
	$output .= home_url();
	$output .= '">';
	$output .= $text;
	$output .= '</a>';
	
    return $output;
}

add_shortcode('home', 'digigit_home');

// To use: [home text="Go To Home Page"] --- default text if not stated is 'Home'


/*****/

// Store Search Box
add_shortcode('storesearch', 'digigit_dl_searchform');

// To use: [storesearch] --- too easy, right?

/*****/

// Main Featured Slider
add_shortcode('ftslider', 'digigit_featured_slider');

// To use: [ftslider] --- even easier!

/*****/

// Extra Short Codes:
/*	Log In form - [login_form]
	Register form  - [register_form]
	*/


/*****/

// Facebook Like Shortcode
function digigit_fb_like( $atts, $content=null ){
/* Author: Nicholas P. Iler
 * URL: http://www.ilertech.com/2011/06/add-facebook-like-button-to-wordpress-3-0-with-a-simple-shortcode/
 */
    extract(shortcode_atts(array( 
            'send' => 'false',
            'layout' => 'standard',
            'show_faces' => 'true',
            'width' => '400px',
            'action' => 'like',
            'font' => '',
            'colorscheme' => 'light',
            'ref' => '',
            'locale' => 'en_US',
            'appId' => '' // Put your AppId here is you have one
    ), $atts));
 
    $fb_like_code = <<<HTML
        <div id="fb-root"></div><script src="http://connect.facebook.net/$locale/all.js#appId=$appId&amp;xfbml=1"></script>
        <fb:like ref="$ref" href="$content" layout="$layout" colorscheme="$colorscheme" action="$action" send="$send" width="$width" show_faces="$show_faces" font="$font"></fb:like>
HTML;
 
    return $fb_like_code;
}
add_shortcode('fb', 'digigit_fb_like');

// To use: [fb]
/*	Send – true, false (eg. send='true')
	Layout – button_count, …
	Show Faces – true, false
	Width – Default is 400px
	Action – like, recommendation
	Font – Check FaceBook API for Details
	Colorscheme – light, dark
	Ref - Check FaceBook API for Details
	Locale - Check FaceBook API for Details
	AppId – Add FaceBook AppId for FaceBook tracking */
	
/*****/

// G+ Button Shortcode
// Global namespace in functions.php
$plus1flag = false;
 
function digigit_plus1( $atts, $content=null ){
/* Author: Nicholas P. Iler 
 * URL: http://www.ilertech.com/2011/06/add-google-1-to-wordpress-3-0-with-a-simple-shortcode/
 */
 
extract(shortcode_atts(array(
        'url' => '',
        'lang' => 'en-US',
        'parsetags' => 'onload',
        'count' => 'false',
        'size' => 'medium',
        'callback' => '',
 
        ), $atts));
 
    // Set global flag
    global $plus1flag;
    $plus1flag = true;
         
        // Check for $content and set to URL if not provided
        if($content != null) $url = $content;
 
    $plus1_code = <<<HTML
    <g:plusone href='$url' count="$count" size="$size" callback="$callback"></g:plusone>
HTML;
 
    return $plus1_code;
}
 
// Add meta for front page ONLY and add scripts to any page with a shortcode
function digigit_addPlus1Meta(){
/* Author: Nicholas P. Iler 
 * URL: http://www.ilertech.com/2011/06/add-google-1-to-wordpress-3-0-with-a-simple-shortcode/
 */
    global $plus1flag;
    if($plus1flag){
        if(is_home()){ // check for front page
            echo "<link rel='canonical' href='" . site_url() ."' />";
        }
         
        echo <<<HTML
                         
        <script type="text/javascript">
          (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();
        </script>
HTML;
    }
}
 
add_shortcode('p1', 'digigit_plus1');
add_action('wp_footer', 'digigit_addPlus1Meta');

// To use: [p1 size='small/medium/standard/tall' count='true/false' lang=/callback=/load 

/*****/