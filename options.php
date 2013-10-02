<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 * 
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet (lowercase and without spaces)
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "", strtolower($themename) );
	
	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = $themename;
	update_option('optionsframework', $optionsframework_settings);
	
	// echo $themename;
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */

function optionsframework_options() {

	$heading_fonts = array("bitter" => "Bitter","droidsans" => "Droid Sans","franchise" => "Franchise","marketingscript" => "Marketing Script","museo" => "Museo Slab","rokkitt" => "Rokkitt");
	$button_colors = array('blue' => __('Blue', 'designcrumbs'), 'green' => __('Green', 'designcrumbs'), 'yellow' => __('Yellow', 'designcrumbs'), 'orange' => __('Orange', 'designcrumbs'), 'red' => __('Red', 'designcrumbs'), 'purple' => __('Purple', 'designcrumbs'));
	
	// Pull all the categories into an array
	$options_categories = array();  
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
    	$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all the pages into an array
	$options_pages = array();  
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
    	$options_pages[$page->ID] = $page->post_title;
	}
		
	// If using image radio buttons, define a directory path
	$imagepath = get_template_directory_uri() . '/images/';
		
	$options = array();
		
	$options[] = array( "name" =>  __('Basic Settings', 'designcrumbs'),
						"type" => "heading");
						
	$options[] = array( "name" =>  __('Logo', 'designcrumbs'),
						"desc" =>  __('Upload your logo. Max height should be 40px, if it is bigger, it will be resized.', 'designcrumbs'),
						"id" => "logo",
						"type" => "upload");
						
	$options[] = array( "name" =>  __('Favicon', 'designcrumbs'),
						"desc" =>  __('The Favicon is the little 16x16 icon that appears next to your URL in the browser. It is not required, but recommended.', 'designcrumbs'),
						"id" => "favicon",
						"type" => "upload");
						
	$options[] = array( "name" =>  __('Site Layout', 'designcrumbs'),
						"desc" =>  __('Select a layout for the site.', 'designcrumbs'),
						"id" => "layout",
						"std" => "content_left",
						"type" => "images",
						"options" => array(
							'content_right' => $imagepath . '2cl.png',
							'content_left' => $imagepath . '2cr.png',)
						);
						
	$options[] = array( "name" =>  __('Sticky Menu', 'designcrumbs'),
						"desc" =>  __('Would you like the menu to stick to the top of the browser as the user scrolls down the site?', 'designcrumbs'),
						"id" => "sticky_header",
						"std" => "yes",
						"type" => "radio",
						"options" => array("yes" => "Yes","no" => "No"));
						
	$options[] = array( "name" =>  __('Products Comments', 'designcrumbs'),
						"desc" =>  __('Do you want to enable comments on product pages? <em>Note that comments must be enabled on each post and in Settings > Discussion. These will both be on by default.</em>', 'designcrumbs'),
						"id" => "product_comments",
						"std" => "no",
						"type" => "radio",
						"options" => array("yes" => __('Yes', 'designcrumbs'),"no" => __('No', 'designcrumbs')));					
						
	$options[] = array( "name" =>  __('Tracking Code', 'designcrumbs'),
						"desc" =>  __('Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme. If you need analytics, you can <a href="http://www.google.com/analytics" target="_blank">go here</a>.', 'designcrumbs'),
						"id" => "analytics",
						"std" => "",
						"type" => "textarea");
						
	$options[] = array( "name" =>  __('Credit Where Credit Is Due', 'designcrumbs'),
						"desc" =>  __('Checking this box will give credit to the ReStored theme and Easy Digital Downloads in the footer.', 'designcrumbs'),
						"id" => "give_credit",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => __('Presstrends', 'designcrumbs'),
						"desc" => __('This theme makes use of Presstrends to track how users are using the theme. This will allow Jake Caputo / Design Crumbs make improvements and adjustments to the theme based on how it\'s being used. To learn more about Presstrends you can view their <a target="_blank" href="http://presstrends.io/terms">terms</a> or their <a target="_blank" href="http://presstrends.io/privacy">privacy policy</a>. <strong>No personal information is transferred.</strong>', 'designcrumbs'),
						"id" => "presstrends",
						"std" => "optin",
						"type" => "radio",
						"options" => array(
							'optin' => __('Opt In', 'designcrumbs'),
							'optout' => __('Opt Out', 'designcrumbs')));
						
	$options[] = array( "name" =>  __('Home Page', 'designcrumbs'),
						"type" => "heading");
						
	$options[] = array( "name" =>  __('Slider Image Box', 'designcrumbs'),
						"desc" =>  __('Would you like a box around the big images in the slider?', 'designcrumbs'),
						"id" => "slider_box",
						"std" => "no",
						"type" => "radio",
						"options" => array("yes" => __('Yes', 'designcrumbs'),"no" => __('No', 'designcrumbs')));
						
	$options[] = array( "name" =>  __('Subheading Text', 'designcrumbs'),
						"desc" =>  __('Just a quick line to separate the slider and the rest of the products. Leaving this blank will simply display nothing.', 'designcrumbs'),
						"id" => "subheading_text",
						"std" => "",
						"type" => "text");
						
	$options[] = array( "name" =>  __('Max Number of Products to be Displayed', 'designcrumbs'),
						"desc" =>  __('On the home page products are displayed 4 wide, so this number should be a multiple of 4.', 'designcrumbs'),
						"id" => "home_products_total",
						"std" => "8",
						"class" => "mini",
						"type" => "text");
				
	$options[] = array( "name" =>  __('View All Products Button Text', 'designcrumbs'),
						"desc" =>  __('This is the big button on the bottom of the home page that will take you to the main Products page. Leave this blank if you wish not to display it.', 'designcrumbs'),
						"id" => "view_all_products_text",
						"std" =>  __('View All Products', 'designcrumbs'),
						"type" => "text");
						
	$options[] = array( "name" =>  __('Home Page Widgets', 'designcrumbs'),
						"desc" =>  __('The home page is widgitized, where would you like these to show up?', 'designcrumbs'),
						"id" => "home_widgets_location",
						"std" => "below",
						"type" => "radio",
						"options" => array("above" =>  __('Above the products', 'designcrumbs'),"below" =>  __('Below the products (bottom of page)', 'designcrumbs')));
						
	$options[] = array( "name" =>  __('Store Settings', 'designcrumbs'),
						"type" => "heading");
						
	$options[] = array( "name" =>  __('Main Store Link', 'designcrumbs'),
						"desc" =>  __('The link to your main store page (which uses the <strong>Store Page Template</strong>. Put the entire URL including the http://. If your permalinks structure is <strong>Post name</strong> (recommended) it would be something like <strong>http://www.yourdomain.com/store</strong>.', 'designcrumbs'),
						"id" => "store_link",
						"std" => "",
						"type" => "text");
						
	$options[] = array( "name" =>  __('Member Login Link', 'designcrumbs'),
						"desc" =>  __('This link will be used in the header. You must create this page and put the login form on it using the Easy Digital Downloads shortcode <strong>[edd_login]</strong>. Put the entire URL including the http://.', 'designcrumbs'),
						"id" => "member_login",
						"std" => "",
						"type" => "text");
						
	$options[] = array( "name" =>  __('Purchase History Link', 'designcrumbs'),
						"desc" =>  __('This link will be used in the header if users are logged in. This page was created by Easy Digital Downloads. Put the entire URL including the http://. If your permalinks structure is <strong>Post name</strong> (recommended) it would be something like <strong>http://www.yourdomain.com/purchase-history</strong>.', 'designcrumbs'),
						"id" => "history_link",
						"std" => "",
						"type" => "text");					
						
	$options[] = array( "name" =>  __('Max Number of Products to be Displayed Per Page', 'designcrumbs'),
						"desc" =>  __('How many products would you like to display per page before pagination starts?', 'designcrumbs'),
						"id" => "products_total",
						"std" => "8",
						"class" => "mini",
						"type" => "text");					
						
	$options[] = array( "name" =>  __('Styles', 'designcrumbs'),
						"type" => "heading");
						
	$options[] = array( "name" =>  __('Color Scheme', 'designcrumbs'),
						"desc" =>  __('Dark is dark boxes with light text. Light is light boxes with dark text.', 'designcrumbs'),
						"id" => "color_scheme",
						"std" => "dark",
						"type" => "select",
						"options" => array("scheme_light" =>  __('Light', 'designcrumbs'),"scheme_dark" =>  __('Dark', 'designcrumbs'), "scheme_light scheme_minimal" =>  __('Minimal Light', 'designcrumbs')));

	$options[] = array( "name" =>  __('Button & Accent Color', 'designcrumbs'),
						"desc" =>  __('Select the color for your buttons.', 'designcrumbs'),
						"id" => "button_color",
						"std" => "blue",
						"type" => "select",
						"options" => $button_colors);
						
	$options[] = array( "name" =>  __('Heading Font', 'designcrumbs'),
						"desc" =>  __('Select the font for the headings of the site.', 'designcrumbs'),
						"id" => "heading_font",
						"std" => "Franchise",
						"type" => "select",
						"options" => $heading_fonts);
						
	$options[] = array( "name" =>  __('Link Color', 'designcrumbs'),
						"desc" =>  __('Select the color for your links.', 'designcrumbs'),
						"id" => "link_color",
						"std" => "",
						"type" => "color");
										
	$options[] = array( "name" => __('Social Networks', 'designcrumbs'),
						"type" => "heading");
					
	$options[] = array( "name" => __('Twitter', 'designcrumbs'),
						"desc" => __('Enter the URL to your Twitter profile.', 'designcrumbs'),
						"id" => "twitter",
						"type" => "text"); 

	$options[] = array( "name" => __('Facebook', 'designcrumbs'),
						"desc" => __('Enter the URL to your Facebook profile.', 'designcrumbs'),
						"id" => "facebook",
						"type" => "text");
						
	$options[] = array( "name" => __('Github', 'designcrumbs'),
						"desc" => __('Enter the URL to your Github profile.', 'designcrumbs'),
						"id" => "github",
						"type" => "text"); 

	$options[] = array( "name" => __('Google+', 'designcrumbs'),
						"desc" => __('Enter the URL to your Google+ profile.', 'designcrumbs'),
						"id" => "google",
						"type" => "text");
					
	$options[] = array( "name" => __('Flickr', 'designcrumbs'),
						"desc" => __('Enter the URL to your Flickr Profile.', 'designcrumbs'),
						"id" => "flickr",
						"type" => "text");
					
	$options[] = array( "name" => __('Forrst', 'designcrumbs'),
						"desc" => __('Enter the URL to your Forrst Profile.', 'designcrumbs'),
						"id" => "forrst",
						"type" => "text");
					
	$options[] = array( "name" => __('Dribbble', 'designcrumbs'),
						"desc" => __('Enter the URL to your Dribbble Profile.', 'designcrumbs'),
						"id" => "dribbble",
						"type" => "text");
					
	$options[] = array( "name" => __('Tumblr', 'designcrumbs'),
						"desc" => __('Enter the URL to your Tumblr Profile.', 'designcrumbs'),
						"id" => "tumblr",
						"type" => "text");
					
	$options[] = array( "name" => __('Vimeo', 'designcrumbs'),
						"desc" => __('Enter the URL to your Vimeo Profile.', 'designcrumbs'),
						"id" => "vimeo",
						"type" => "text");		
				
	$options[] = array( "name" => __('RSS', 'designcrumbs'),
						"desc" => __('Enter the URL to your RSS Feed.', 'designcrumbs'),
						"id" => "rss",
						"type" => "text");		
				
	// Support
						
	$options[] = array( "name" => __('Support', 'designcrumbs'),
						"type" => "heading");					
						
	$options[] = array( "name" => __('Theme Documentation & Support', 'designcrumbs'),
						"desc" => "<p class='dc-text'>Theme support and documentation is available for all ThemeForest customers. Visit the <a target='blank' href='http://support.designcrumbs.com'>Design Crumbs Support Forum</a> to get started. Simply follow the instructions on the home page to verify your purchase and get a support account.</p>
						
						<p class='dc-text'>For support for the Easy Digital Downloads plugin, please visit the <a target='blank' href='http://easydigitaldownloads.com/forums/'>Easy Digital Downloads Support Forum</a>.</p>
						
						<div class='dc-buttons'><a target='blank' class='dc-button help-button' href='http://support.designcrumbs.com/help_files/restoredwp/'><span class='dc-icon icon-help'>Help File</span></a><a target='blank' class='dc-button support-button' href='http://support.designcrumbs.com'><span class='dc-icon icon-support'>Support Forum</span></a><a target='blank' class='dc-button custom-button' href='http://www.designcrumbs.com/theme-customization-request'><span class='dc-icon icon-custom'>Customize Theme</span></a></div>
						
						<h4 class='heading'>More Themes by Design Crumbs</h4>
						
						<div class='embed-themes'></div>
						
						",
						"type" => "info");	
				
	return $options;
}