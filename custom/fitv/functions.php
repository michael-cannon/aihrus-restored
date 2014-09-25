<?php
/**
 *  FITV VideoZoom theme custom functions
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


$sort_by_episode				= array(
	6850,
	7054,
	8942,
	10788,
	10790,
);


// add_action('admin_menu', 'mb_wpzoom_options_box');
// remove_action('admin_menu', 'wpzoom_options_box');
function mb_wpzoom_options_box() {
	add_meta_box('wpzoom_post_layout', 'Video Layout', 'wpzoom_post_layout_options', 'video', 'normal', 'high');
	add_meta_box('wpzoom_post_embed', 'Video Options', 'wpzoom_post_embed_info', 'video', 'side', 'high');
}


// add_filter( 'posts_distinct', 'fitv_admin_posts_distinct' );
function fitv_admin_posts_distinct() {
	global $wpdb, $wp_query, $pagenow;

	if ( is_admin()
		&& 'edit.php' == $pagenow
		&& in_array( $wp_query->query_vars['post_type'], array( 'video', 'document' ) )
		&& ! empty( $wp_query->query_vars['s'] )
		// && ctype_digit( $wp_query->query_vars['s'] )
	) {
		return 'DISTINCT';
	}

	return '';
}

function fitv_relevanssi_match( $match, $idf = null ) {
	$post_title					= get_the_title( $match->doc );

	if ( strpos( $post_title, '[' ) ) {
		$match->weight			/= 1000;
	}

	if ( 1 > $match->weight )
		$match->weight			= 0;

	return $match;
}

// add_filter( 'posts_join', 'fitv_admin_posts_join' );
function fitv_admin_posts_join( $join ) {
	global $pagenow, $wpdb, $wp_query;

	if ( is_admin()
		&& 'edit.php' == $pagenow
		&& in_array( $wp_query->query_vars['post_type'], array( 'video', 'document' ) )
		&& ! empty( $wp_query->query_vars['s'] )
		// && ctype_digit( $wp_query->query_vars['s'] )
	) {
		$join				.= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
	}

	return $join;
}


// add_filter( 'posts_where', 'fitv_admin_posts_where' );
function fitv_admin_posts_where( $where ) {
	global $pagenow, $wpdb, $wp_query;

	if ( is_admin()
		&& 'edit.php' == $pagenow
		&& in_array( $wp_query->query_vars['post_type'], array( 'video', 'document' ) )
		&& ! empty( $wp_query->query_vars['s'] )
	) {
		if ( preg_match( '#^\d+(\.\d+)?$#', $wp_query->query_vars['s'] ) ) {
			$where				= preg_replace( "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/", "(".$wpdb->posts.".post_title LIKE $1) OR ( " . $wpdb->postmeta . ".meta_key = 'control_number' AND " . $wpdb->postmeta . ".meta_value LIKE $1 )", $where );
		} else {
			$s					= $wp_query->query_vars['s'];
			$where				= preg_replace( "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/", "(".$wpdb->posts.".post_title LIKE $1) OR ( " . $wpdb->postmeta . ".meta_key = 'additional_copies' AND " . $wpdb->postmeta . ".meta_value LIKE '%" . $s . "%')", $where );
		}
	}

	return $where;
}


// add_filter( 'posts_fields', 'fitv_admin_posts_fields' );
function fitv_admin_posts_fields( $fields ) {
	global $wpdb, $wp_query, $pagenow, $sort_by_episode;

	if ( is_category( $sort_by_episode ) && is_main_query() ) {
		$orderby				= 'episode';
	} elseif( ! $wp_query->is_admin || 'edit.php' != $pagenow ) {
		return $fields;
	} else {
		$orderby				= $wp_query->get( 'orderby' );
	}

	if ( $orderby && is_main_query() ) {
		switch ( $orderby ) {
			case 'episode' :
				$fields		.= ", REPLACE( {$wpdb->posts}.post_title, '[', 'zzzzzz[') CUSTOM_" . $orderby;

				break;

			case 'production_date' :
			case 'publication_date' :
				$fields		.= ", ( 
					SELECT UNIX_TIMESTAMP( IFNULL( {$wpdb->terms}.slug, {$wpdb->posts}.post_date ) )
					FROM {$wpdb->term_relationships}
					LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
					LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
					WHERE (taxonomy = '{$orderby}' OR taxonomy IS NULL)
						AND {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID
					LIMIT 1
				) CUSTOM_" . $orderby;

				break;

			case 'control_number' :
			case 'video_tape_format' :
				$fields		.= ', ( SELECT wp_postmeta.meta_value FROM ' . $wpdb->postmeta . ' WHERE wp_postmeta.meta_key = "' . $orderby . '" AND wp_postmeta.post_id = ' . $wpdb->posts . '.ID LIMIT 1 )' . ' CUSTOM_' . $orderby;
				break;

			default :
				break;
		}
	}

	return $fields;
}


// add_filter( 'posts_orderby', 'fitv_admin_posts_orderby' );
function fitv_admin_posts_orderby( $vars ) {
	global $wp_query, $pagenow, $sort_by_episode;

	if ( is_category( $sort_by_episode ) && is_main_query() ) {
		$orderby				= 'episode';
		$order					= 'ASC';
	} elseif( ! $wp_query->is_admin || 'edit.php' != $pagenow )  {
		return $vars;
	} else {
		$orderby				= $wp_query->get( 'orderby' );
		$order					= $wp_query->get( 'order' );
	}

	if ( $orderby && is_main_query() ) {
		switch ( $orderby ) {
			case 'production_date' :
			case 'publication_date' :
				$vars		= 'CUSTOM_' .  $orderby . ' ' . $order
							. ', post_title  ' . $order;
				break;

			case 'control_number' :
			case 'episode' :
			case 'video_tape_format' :
				$vars		= 'CUSTOM_' .  $orderby . ' ' . $order;
				break;

			default :
				break;
		}
	}

	return $vars;
}


function custom_manage_posts_custom_column( $column, $post_id, $echo = true ) {
	return mbr_manage_posts_custom_column( $column, $post_id, $echo );
}



// add_filter( 'wp_dropdown_users', 'fitv_wp_dropdown_users' );
// remove all but editors and above
function fitv_wp_dropdown_users( $users ) {
	$cache_name					= 'fitvddu_';
	$md5						= md5( $users );
	$cache						= get_transient( $cache_name . $md5 );

	if ( $cache ) {
		return $cache;
	}

	global $wpdb;

	$query						= "SELECT DISTINCT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'wp_user_level' AND meta_value < 3";
	$authors					= $wpdb->get_col( $query );

	foreach ( $authors as $author ) {
		$option					= "#\t<option value='{$author}'>[^<]+</option>\n#";
		$users					= preg_replace( $option, '', $users );
	}

	$md5						= md5( $users );
	$set						= set_transient( $cache_name . $md5, $users, 60 * 5 );

	return $users;
}


// add_action( 'the_content', 'fitv_vzaar_chapters' );
function fitv_vzaar_chapters( $content ) {
	$id       = get_the_ID();
	// $vzaar_id = get_post_meta( $id, 'vzaar_id', true );
	if ( ! preg_match_all( '#(\d+(:\d+)+)\s#', $content, $matches )
		|| ! is_singular()
		|| ! get_post_meta( $id, 'wpzoom_post_embed_code', true )
	//	|| empty( $vzaar_id )
	) {
		return $content;
	}

	$times    = $matches[ 1 ];
	foreach ( $times as $time ) {
		// http://help.vzaar.com/help/kb/api/javascript-api
		// vzPlayer.seekTo(seconds:Number)
		$seconds  = time2seconds( $time );

		$bookmark = '<em class="chapter" onclick="playVzaarVideoAt(' . $seconds . ');">' . $time . '</em>';
		$bookmark.= Fitv_Theme::fitv_copy_time_url( $time );
		$content  = preg_replace( '#\b' . $time . '\b#', $bookmark, $content );
	}
	
	return $content;
}


// add_action( 'wp_enqueue_scripts', 'fitv_vzaar_chapters_scripts' );
function fitv_vzaar_chapters_scripts() {
	wp_register_script( 'vzaar_library', 'http://player.vzaar.net/libs/flashtakt/client.js', array( 'jquery' ), '20130109', true );
	wp_enqueue_script( 'vzaar_library' );

	// wp_register_script( 'ZeroClipboard', get_bloginfo('template_directory') . '/functions/user/custom/js/ZeroClipboard.js', array( 'jquery' ), '20130603', true );
	// wp_enqueue_script( 'ZeroClipboard' );

	wp_register_script( 'vzaar_chapters', get_bloginfo('template_directory') . '/functions/user/custom/js/vzaar_chapters.js', array( 'jquery' ), '20120428', true );
	wp_enqueue_script( 'vzaar_chapters' );
}


// add_filter( 'pre_get_posts', 'pre_get_posts_allow_video_document' );
function pre_get_posts_allow_video_document( $query ) {
	if ( $query->is_admin ) {
		return $query;
	} elseif ( ( $query->is_main_query() || is_feed() ) && ! is_page() && ( ! empty( $query->query_vars['post_type'] ) && ( 'post' == $query->query_vars['post_type'] ) || is_archive()) ) {
		$query->set( 'post_type', array( 'post', 'video', 'document' ) );
	}

	return $query;
}


function fitv_hits_filter( $hits ) {
	$everything_else			= array();
	$originals					= array();

	foreach ( $hits[0] as $hit ) {
		if ( false === strpos( $hit->post_title, '[' ) ) {
			array_push( $everything_else, $hit );
		} else {
			array_push( $originals, $hit );
		}
	}

	$hits[0]					= array_merge( $everything_else, $originals );

	return $hits;
}

class Fitv_Theme {
	static $startPointJS;
	static $vzaar_id;
	static $zeroClipboardJS;

	static function init() {
		if ( empty( self::$vzaar_id ) )
			self::$vzaar_id = get_post_meta( get_the_ID(), 'vzaar_id', true );

		if ( empty( self::$zeroClipboardJS ) ) {
			$wp_url                  = get_bloginfo( 'wpurl' );
			self::$zeroClipboardJS   = array();
			// self::$zeroClipboardJS[] = "ZeroClipboard.setDefaults( { moviePath: '{$wp_url}/wp-content/themes/responsive/functions/user/custom/js/ZeroClipboard.swf' } );";
			// self::$zeroClipboardJS[] = "var clip = new ZeroClipboard();";
			self::$zeroClipboardJS[] = "function fitv_window_prompt( text ) {
				window.prompt('Press CTRL+C to copy video clip URL', text )
			}";
		}
	}

	static function fitv_copy_time_url( $time ) {
		self::init();

		$seconds  = time2seconds( $time );
		$id       = 'fitv-clip-' . $seconds;
		$time_url = get_permalink() . '?t=' . $time;
		$content  = '';
		// $content .= '<span id="' . $id . '" class="time-url" data-clipboard-text="' . $time_url . '" title="Copy time code URL to clipboard">';
		$content .= '<span class="time-url" onclick="fitv_window_prompt(\'' . $time_url . '\');">';
		$content .= '<span class="time-url-click">Copy video clip URL</span>';
		// $content .= '<input id="' . $id . '" class="time-url" onclick="this.select(); fitv_copy_alert();" readonly="readonly" title="Click and press CTRL+C to copy time coded URL to your clipboad" type="text" value="' . $time_url . '" />';
		$content .= '</span>';

		// self::$zeroClipboardJS[] = "clip.glue( document.getElementById( '$id' ) );";
		// self::$zeroClipboardJS[] = "var clip{$seconds} = new ZeroClipboard( document.getElementById( '$id' ) )";

		return $content;
	}

	static function fitv_footer_javascript() {
		self::init();

		$startPointJS    = self::$startPointJS;
		$vzaar_id        = self::$vzaar_id;
		$zeroClipboardJS = implode( "\n", self::$zeroClipboardJS );

		echo <<< EOM
<script type="text/javascript">
	function beginPlayingVideo() {
		vzPlayer = document.getElementById("vzvd-{$vzaar_id}");
		{$startPointJS}
	}

	function vzaarPlayerReady() {
		setTimeout( beginPlayingVideo, 1250 );
	}

	{$zeroClipboardJS}
</script>
EOM;
}
}

?>