<?php
/**
 *  Simple search video widget
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

class Search_Video_Widget extends WP_Widget {
	public $field				= 'fitv_show_only_posts_videos';

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'Search_Video_Widget',
			'Search: Video Posts Only',
			array(
				'description'	=> __( 'Limit search to only those entries with videos', 'fitv' ),
			)
		);

		$this->update_var();
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;

		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		echo $this->search_videos();

		echo $after_widget;
	}

	public function update_var() {
		if ( isset( $_POST[ $this->field ] ) ) {
			$value				= $_POST[ $this->field ];
			update_option( $this->field, $value );
		}
	}

	private function search_videos() {
		// create form
		$this_url				= curPageURL();
		$label					= __( 'Only show posts with videos', 'fitv' );
		$label_yes				= __( 'Yes', 'fitv' );
		$label_no				= __( 'No', 'fitv' );
		// show checkbox "only show posts with videos"
		// set checked or not
		$option					= get_option( $this->field, 0 );
		$checked_yes			= checked( $option, 1, false );
		$checked_no				= checked( $option, 0, false );

		$form					= <<<EOD
			<form action="{$this_url}" method="post">
				<label for="{$this->field}">
					{$label}<br />
					{$label_yes}
					<input type="radio" name="{$this->field}" value="1" {$checked_yes} />
					{$label_no}
					<input type="radio" name="{$this->field}" value="0" {$checked_no} />
				</label>
				<input type="submit" />
			</form>
EOD;

		return $form;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance				= array();
		$instance['title']		= strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title				= $instance[ 'title' ];
		}
		else {
			$title				= __( 'Video Posts', 'fitv' );
		}
?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
<?php 
	}

} // class Search_Video_Widget

function fitv_search_video_widget() {
	register_widget( 'Search_Video_Widget' );
}

if ( true ) {
	add_action( 'init', 'fitv_init_get_search_form' );
	add_action( 'widgets_init', 'fitv_search_video_widget' );
	add_filter( 'pre_get_posts', 'fitv_video_posts_filter' );
	// add_filter( 'posts_where', 'fitv_video_posts_where' );
	add_filter( 'relevanssi_where', 'fitv_video_posts_relevanssi_where' );
}

function fitv_video_posts_filter( $query ) {
	if ( is_admin() || ! $query->is_search )
		return;

	global $wpdb;

	$do_it						= get_option( 'fitv_show_only_posts_videos' );

	if ( $do_it ) {
		$query->query_vars['post_type']	= array( 'video' );

		$fitv_post_ids			= $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'wpzoom_post_embed_code' AND meta_value != ''" );

		if ( empty( $fitv_post_ids ) )
			return;

		// fails for Relevanssi and other search plugin tie-ins
		$query->query_vars['post__in']	= $fitv_post_ids;

		// use posts_where then as failsafe
		update_option( 'fitv_show_only_posts_videos_values', $fitv_post_ids );
	}
}


function fitv_init_get_search_form() {
	$field						= 'fitv_show_only_posts_videos';

	if ( isset( $_REQUEST[ $field ] ) ) {
		$value					= intval( $_REQUEST[ $field ] );
		update_option( $field, $value );
	} else {
		update_option( $field, 0 );
	}
}


function fitv_video_posts_relevanssi_where( $where ) {
	if ( is_admin() || ! is_search() )
		return $where;

	$do_it						= get_option( 'fitv_show_only_posts_videos' );
	$posts						= get_option( 'fitv_show_only_posts_videos_values' );

	if ( $do_it && ! empty( $posts ) ) {
		$where					.= ' AND doc IN ( ' . implode( ',', $posts ) . ' ) ';
	}

	return $where;
}


function fitv_video_posts_where( $where ) {
	if ( is_admin() || ! is_search() )
		return $where;

	$do_it						= get_option( 'fitv_show_only_posts_videos' );
	$posts						= get_option( 'fitv_show_only_posts_videos_values' );

	if ( $do_it && ! empty( $posts ) ) {
		$where					.= ' AND ID IN ( ' . implode( ',', $posts ) . ' ) ';
	}

	return $where;
}


function fitv_get_search_form( $form ) {
	$field						= 'fitv_show_only_posts_videos';
	$label						= __( 'Only show posts with videos', 'fitv' );
	$label_yes					= __( 'Yes', 'fitv' );
	$label_no					= __( 'No', 'fitv' );
	// show checkbox "only show posts with videos"
	// set checked or not
	$option						= get_option( $field, 0 );
	$checked_yes				= checked( $option, 1, false );

	$find						= '<input type="submit" id="searchsubmit" value="Search" />';
	$replace					= <<<EOD
		<label for="{$field}">
			{$label}
		</label>
		<input type="checkbox" name="{$field}" value="1" {$checked_yes} />
EOD;
	$form						= str_replace( $find, $find . $replace, $form );

	return $form;
}

?>
