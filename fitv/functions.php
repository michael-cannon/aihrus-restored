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

	if ( $orderby ) {
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
				$fields		.= ', ( SELECT meta_value FROM ' . $wpdb->postmeta . ' WHERE meta_key = "' . $orderby . '" AND wp_postmeta.post_id = ' . $wpdb->posts . '.ID )' . ' CUSTOM_' . $orderby;
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

	if ( $orderby ) {
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


function document_manage_sortable_columns( $columns ) {
	$columns['control_number']		= 'control_number';
	// $columns['publication_date']	= 'publication_date';
	// $columns['document_type']		= 'document_type';

	return $columns;
}


function document_manage_posts_columns( $columns ) {
	$columns['control_number']		= __( 'Control Number', 'fitv' );
	$columns['document_type']		= __( 'Type', 'fitv' );

	// $columns['language']			= __( 'Language', 'fitv' );
	$columns['genre']				= __( 'Genre', 'fitv' );
	$columns['collection']			= __( 'Collection', 'fitv' );
	$columns['producer']			= __( 'Producer', 'fitv' );
	$columns['publication_date']	= __( 'Publication Date', 'fitv' );

	// $columns['actions']    = __( 'Actions', 'fitv' );
	$columns['additional_copies']    = __( 'Additional Copies', 'fitv' );
	$columns['additional_credits']    = __( 'Additional Credits', 'fitv' );
	// $columns['alternative_titles']    = __( 'Alternative Titles', 'fitv' );
	$columns['collection_name']    = __( 'Collection Name', 'fitv' );
	// $columns['color']				= __( 'Color', 'fitv' );
	$columns['condition']    = __( 'Condition', 'fitv' );
	// $columns['corporate_names']    = __( 'Corporate Names', 'fitv' );
	$columns['date_notation']    = __( 'Date Notation', 'fitv' );
	$columns['general_note']    = __( 'General Note', 'fitv' );
	// $columns['generation']    = __( 'Generation', 'fitv' );
	// $columns['location']    = __( 'Location', 'fitv' );
	// $columns['location_of_production']    = __( 'Location of Production', 'fitv' );
	// $columns['main_contributors']    = __( 'Main Contributors', 'fitv' );
	$columns['main_credits']    = __( 'Main Credits', 'fitv' );
	// $columns['not_for_sale']    = __( 'Not For Sale', 'fitv' );
	$columns['number_of_pages']    = __( 'Number Of Pages', 'fitv' );
	$columns['size']    = __( 'Size', 'fitv' );
	$columns['staff_comments']    = __( 'Staff Comments', 'fitv' );
	// $columns['title_list']    = __( 'Title List', 'fitv' );

	return $columns;
}


function video_manage_sortable_columns( $columns ) {
	$columns['control_number']		= 'control_number';
	// $columns['production_date']		= 'production_date';
	$columns['video_tape_format']	= 'video_tape_format';

	return $columns;
}


function video_manage_posts_columns( $columns ) {
	$columns['control_number']		= __( 'Control Number', 'fitv' );
	$columns['video_tape_format']	= __( 'Video Tape Format', 'fitv' );
	// $columns['color']				= __( 'Color', 'fitv' );
	// $columns['language']			= __( 'Language', 'fitv' );
	$columns['genre']				= __( 'Genre', 'fitv' );
	$columns['collection']			= __( 'Collection', 'fitv' );
	$columns['producer']			= __( 'Producer', 'fitv' );
	$columns['production_date']		= __( 'Production Date', 'fitv' );

	$columns['acquisition_source']    = __( 'Acquisition Source', 'fitv' );
	$columns['actions']    = __( 'Actions', 'fitv' );
	$columns['additional_copies']    = __( 'Additional Copies', 'fitv' );
	$columns['additional_credits']    = __( 'Additional Credits', 'fitv' );
	// $columns['alternative_titles']    = __( 'Alternative Titles', 'fitv' );
	// $columns['audio_format']    = __( 'Audio Format', 'fitv' );
	$columns['condition']    = __( 'Condition', 'fitv' );
	// $columns['corporate_names']    = __( 'Corporate Names', 'fitv' );
	$columns['date_notation']    = __( 'Date Notation', 'fitv' );
	// $columns['film_format']    = __( 'Film Format', 'fitv' );
	$columns['general_note']    = __( 'General Note', 'fitv' );
	// $columns['generation']    = __( 'Generation', 'fitv' );
	// $columns['location_of_production']    = __( 'Location of Production', 'fitv' );
	// $columns['main_contributors']    = __( 'Main Contributors', 'fitv' );
	$columns['main_credits']    = __( 'Main Credits', 'fitv' );
	$columns['not_for_sale']    = __( 'Not For Sale', 'fitv' );
	// $columns['number_of_reels']    = __( 'Number of Reels', 'fitv' );
	// $columns['old_number']    = __( 'Old Number', 'fitv' );
	$columns['performers']    = __( 'Performers', 'fitv' );
	$columns['running_time']    = __( 'Running Time', 'fitv' );
	// $columns['sound']    = __( 'Sound', 'fitv' );
	$columns['staff_comments']    = __( 'Staff Comments', 'fitv' );
	// $columns['titles_on_reel']    = __( 'Titles on Reel', 'fitv' );
	// $columns['video_start_point']    = __( 'Video Start Point', 'fitv' );
	$columns['wpzoom_post_embed_code']    = __( 'Embedded Video', 'fitv' );

	return $columns;
}
 

function fitv_admin_init() {
	add_filter( 'manage_document_posts_custom_column', 'fitv_manage_posts_custom_column', 10, 2 );
	add_filter( 'manage_edit-document_columns', 'document_manage_posts_columns' );
	add_filter( 'manage_edit-document_sortable_columns', 'document_manage_sortable_columns' );
	add_filter( 'manage_edit-video_columns', 'video_manage_posts_columns' );
	add_filter( 'manage_edit-video_sortable_columns', 'video_manage_sortable_columns' );
	add_filter( 'manage_video_posts_custom_column', 'fitv_manage_posts_custom_column', 10, 2 );

	// include 'admin-posts-filter.php';
}


function custom_manage_posts_custom_column( $column, $post_id, $echo = true ) {
	return fitv_manage_posts_custom_column( $column, $post_id, $echo );
}

function fitv_manage_posts_custom_column( $column, $post_id, $echo = true ) {
	// This function is ACF get_field specific than get_post_meta
	switch ( $column ) {
		case 'collection' :
		case 'genre' :
		case 'language' :
		case 'production_date' :
		case 'producer' :
		case 'publication_date' :
			$taxonomy			= $column;
			$post_type			= get_post_type($post_id);
			$terms				= get_the_terms($post_id, $taxonomy);

			if ( ! empty($terms) ) {
				foreach ( $terms as $term ) {
					$post_terms[]	="<a href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
				}
				$text			= join(',', $post_terms );
			} else {
				$text			= '';
			}

			break;

		case 'color' :
			$data				=                 array (
				1 => 'Color',
				2 => 'Black & White',
				3 => 'Grey scale',
			);
			$values				= get_post_meta($post_id, $column, true);
			// $values				= get_field($column, $post_id);
			if ( $values ) {
				$values			= explode( ',', $values );
				$result			= array();
				foreach( $values as $key => $value ) {
					$value		= trim( $value );
					$result[]	= isset( $data[ $value ] ) ? $data[ $value ] : 'Not Defined';
				}

				$text			= implode( ', ', $result );
			} else {
				$text			= '';
			}
			break;

		case 'sound' :
			$data				= array(
				1 => 'Mono',
				3 => 'Silent',
				2 => 'Stereo',
				4 => 'Unknown',
			);
			$id				= get_post_meta($post_id, $column, true);
			// $id					= get_field($column, $post_id);
			if ( $id ) {
				$text			= isset( $data[ $id ] ) ? $data[ $id ] : 'Not Defined';
			} else {
				$text			= '';
			}
			break;

		case 'not_for_sale' :
			$data				= array(
				0 => 'For sale',
				1 => 'Not for sale',
			);
			$id				= get_post_meta($post_id, $column, true);
			// $id					= get_field($column, $post_id);
			if ( $id ) {
				$text			= isset( $data[ $id ] ) ? $data[ $id ] : 'Not Defined';
			} else {
				$text			= '';
			}
			break;

		case 'document_type' :
			$data				= array(
				1	=> 'Photograph',
				2	=> 'Artwork',
				3	=> 'Press',
				4	=> 'Correspondence',
				5	=> 'Financial',
				6	=> 'Notes',
				7	=> 'Other',
				8	=> 'Newsletter',
				9	=> 'Program',
				10	=> 'Proposal',
				11	=> 'Cartoon',
				12	=> 'Magazine Article',
				13	=> 'Newspaper Article',
				14	=> 'Memo',
			);
			$id				= get_post_meta($post_id, $column, true);
			// $id					= get_field($column, $post_id);
			if ( $id ) {
				$text			= isset( $data[ $id ] ) ? $data[ $id ] : 'Not Defined';
			} else {
				$text			= '';
			}
			break;

		case 'generation' :
			$post = &get_post($post_id);
			$post_type = $post->post_type;
			if ( 'video' == $post_type ) {
				$data				= array(
					1 => 'Camera Original',
					5 => 'Listening Copy',
					2 => 'Master',
					3 => 'Submaster',
					4 => 'Viewing Copy',
				);
			} else {
				$data				= array( 
					1 => 'original',
					2 => 'photocopy',
					3 => 'enlargement',
				);
			}
			$id				= get_post_meta($post_id, $column, true);
			// $id					= get_field($column, $post_id);
			if ( $id ) {
				$text			= isset( $data[ $id ] ) ? $data[ $id ] : '';
			} else {
				$text			= '';
			}
			break;

		case 'film_format' :
			$data				= array(
				2 => '16mm',
				1 => '35mm',
				4 => '8mm',
				3 => 'Super-8mm',
			);
			$id				= get_post_meta($post_id, $column, true);
			// $id					= get_field($column, $post_id);
			if ( $id ) {
				$text			= isset( $data[ $id ] ) ? $data[ $id ] : '';
			} else {
				$text			= '';
			}
			break;

		case 'audio_format' :
			$data				= array(
				1 => '1/4 in. reel-to-reel',
				12 => '33 1/3 RPM Vinyl Record',
				13 => '78 RPM Vinyl Record',
				2 => 'Audio cassette',
				3 => 'CD',
				4 => 'CD-R',
				14 => 'DAT',
				5 => 'DVD-A',
				6 => 'Floppy Disk',
				7 => 'Hard Drive',
				8 => 'LP',
				9 => 'SACD',
				10 => 'Sound Disk',
				11 => 'Zip Disk',
			);
			$id				= get_post_meta($post_id, $column, true);
			// $id					= get_field($column, $post_id);
			if ( $id ) {
				$text			= isset( $data[ $id ] ) ? $data[ $id ] : '';
			} else {
				$text			= '';
			}
			break;

		case 'sound' :
			$data				= array(
				1 => 'Mono',
				3 => 'Silent',
				2 => 'Stereo',
				4 => 'Unknown',
			);
			$id				= get_post_meta($post_id, $column, true);
			// $id					= get_field($column, $post_id);
			if ( $id ) {
				$text			= isset( $data[ $id ] ) ? $data[ $id ] : '';
			} else {
				$text			= '';
			}
			break;

		case 'video_tape_format' :
			$data				= array(
				1	=> 'Betacam',
				4	=> 'Betamax',
				5	=> 'D1',
				6	=> 'D2',
				7	=> 'D3',
				8	=> 'D5',
				9	=> 'DV',
				10	=> 'DVCAM',
				11	=> 'DVCPRO',
				12	=> 'Hi 8 mm',
				13	=> 'Video 8',
				14	=> 'VHS',
				15	=> 'VHS-C',
				16	=> 'S-VHS',
				17	=> '2 in.',
				18	=> '1 in.',
				20	=> 'U-matic',
				21	=> '1/2 in. reel-to-reel',
				22	=> 'Betacam SP',
				23	=> 'Betacam SX',
				26	=> 'D8',
				27	=> 'DVD',
				28	=> 'SVHSC',
				29	=> 'Laserdisc',
				32	=> 'Mini-DV',
				33	=> 'Digital Betacam',
				35	=> 'U-matic SP',
				36	=> 'Digital S',
				37	=> 'DVD-R',
				38	=> 'Digital file',
			);
			$id				= get_post_meta($post_id, $column, true);
			// $id					= get_field($column, $post_id);
			if ( $id ) {
				$text			= isset( $data[ $id ] ) ? $data[ $id ] : 'Not Defined';
			} else {
				$text			= '';
			}
			break;

		case 'performers':
			$text				= get_post_meta($post_id, $column, true);
			// $text				= get_field($column, $post_id);
			// $text				= str_replace( '<br />', '', $text );
			// $text				= str_replace( chr( 13 ), ', ', $text );
			break;

		case 'wpzoom_post_embed_code':
			$data				= get_post_meta($post_id, $column, true);
			// $data				= get_field($column, $post_id);
			$text				= ! empty( $data ) ? __( 'Yes', 'custom' ) : __( 'No', 'custom' );
			break;

		default:
			$text				= get_post_meta($post_id, $column, true);
			// $text				= get_field($column, $post_id);
			break;
	}

	if ( $echo )
		echo $text;
	else
		return $text;
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
	} elseif ( ( $query->is_main_query() || is_feed() ) && ! is_page() && ( 'post' == $query->query_vars['post_type'] || is_archive()) ) {
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

	function init() {
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