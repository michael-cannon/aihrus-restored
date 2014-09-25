<?php
/*
Plugin Name: Admin Filter BY Custom Fields
Plugin URI: http://en.bainternet.info
Description: Filter posts or pages in admin by custom fields (post meta)
Version: 1.0
Author: Bainternet
Author URI: http://en.bainternet.info
*/



// add_filter( 'parse_query', 'ba_admin_posts_filter' );
// add_action( 'restrict_manage_posts', 'ba_admin_posts_filter_restrict_manage_posts' );

function ba_admin_posts_filter( $query ) {
    global $pagenow;

	print_r($query); echo "\n<br />"; echo '' . __LINE__ . ':' . basename( __FILE__ )  . "\n<br />";	

    if ( is_admin() && 'edit.php' == $pagenow && ! empty($_GET['ADMIN_FILTER_FIELD_NAME']) ) {
        $query->query_vars['meta_key'] = esc_attr( $_GET['ADMIN_FILTER_FIELD_NAME'] );
		$query->query_vars['meta_compare'] = 'LIKE';
		if ( ! empty($_GET['ADMIN_FILTER_FIELD_VALUE']))
			$query->query_vars['meta_value'] = esc_attr( $_GET['ADMIN_FILTER_FIELD_VALUE'] );
    }
}

function ba_admin_posts_filter_restrict_manage_posts() {
    global $wpdb;
	$sql = 'SELECT DISTINCT meta_key FROM ' . $wpdb->postmeta;
	$sql .= ' WHERE 1 = 1';
	$sql .= ' AND meta_key NOT LIKE "field_%"';
	$sql .= ' AND meta_key NOT LIKE "wpcf-%"';
	$sql .= ' ORDER BY meta_key ASC';
    $fields = $wpdb->get_results($sql, ARRAY_N);
?>
<select name="ADMIN_FILTER_FIELD_NAME">
<option value=""><?php _e('Filter By Custom Fields', 'baapf'); ?></option>
<?php
    $current = isset($_GET['ADMIN_FILTER_FIELD_NAME'])? esc_attr( $_GET['ADMIN_FILTER_FIELD_NAME'] ) :'';
    $current_v = isset($_GET['ADMIN_FILTER_FIELD_VALUE'])? esc_attr( $_GET['ADMIN_FILTER_FIELD_VALUE'] ) :'';
    foreach ($fields as $field) {
        if (substr($field[0],0,1) != "_"){
        printf
            (
                '<option value="%s"%s>%s</option>',
                $field[0],
				selected( $field[0] == $current, true, false ),
                $field[0]
            );
        }
    }
?>
</select> <?php _e('Value:', 'baapf'); ?><input type="TEXT" name="ADMIN_FILTER_FIELD_VALUE" value="<?php echo $current_v; ?>" />
<?php
}