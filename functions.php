<?php

add_filter( 'widget_text', 'do_shortcode' );

function aihrus_init() {
	if ( ! is_super_admin() )
		show_admin_bar( false );

	aihrus_scripts();
	aihrus_styles();
}

add_action( 'init', 'aihrus_init' );

// global $Isa_EDD_Related_Downloads;
// remove_action( 'edd_after_download_content', array( $Isa_EDD_Related_Downloads, 'isa_after_download_content' ), 90 );

// add_filter( 'edd_purchase_download_form', 'aihrus_edd_purchase_download_form', 10, 2 );
function aihrus_edd_purchase_download_form( $purchase_form, $args ) {
	$id    = $args['download_id'];
	$price = edd_get_download_price( $id );
	if ( 0 == $price ) {
		$price         = '&#036;' . $price;
		$replace       = 'Free';
		$purchase_form = str_replace( $price, $replace, $purchase_form );
	} elseif ( class_exists( 'EDD_Recurring' ) && EDD_Recurring()->is_recurring( $id ) ) {
		$period = EDD_Recurring()->get_period_single( $id );
		if ( 'never' != $period ) {
			$replace       = $price . ' per ' . $period;
			$purchase_form = str_replace( $price, $replace, $purchase_form );
		}
	}

	return $purchase_form;
}

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

?>