<?php
//Exit if not called in proper context
if (!defined('ABSPATH')) exit();

//-------------------------------------------
//Custom Field Bulk Editor Actions
//-------------------------------------------
function fitv_cfbe_metabox($post_type) {
	if ($post_type != 'video') return;

	$change_to_text = __("Change To");
	$leave_unchanged_text = __("Leave Unchanged");

	?>
	<table class="widefat cfbe_table">
		<thead>
			<tr>
				<th>Bulk Edit Video Fields</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<label for="_additional_copies" class="cfbe_special_label">Additional Copies</label>
					<input type="radio" name="_additional_copies_status" id="_additional_copies_status0" value="0" checked="checked" />
					<label for="_additional_copies_status0" class="cfbe_leave_unchanged"><?php echo $leave_unchanged_text; ?></label>
					<input type="radio" name="_additional_copies_status" id="_additional_copies_status1" value="1" />
					<label for="_additional_copies_status1"><?php echo $change_to_text; ?>:</label>

					<textarea name="_additional_copies" id="_additional_copies" onfocus="jQuery('#_additional_copies_status1').prop('checked', true);"></textarea>
					<div style="clear: both;"></div>
				</td>
			</tr>
		</tbody>
	</table>
<?php
}
// add_action('cfbe_before_metabox', 'fitv_cfbe_metabox');

function fitv_cfbe_save( $post_type, $post_id ) {
	exit( __LINE__ . ':' . basename( __FILE__ ) . " ERROR<br />\n" );
	if ( $post_type != 'video' )
		return;

	// Generic Fields Needing No Special Treatment
	$fields = array('additional_copies');
	foreach ( $fields as $field ) {
		// if ( ! empty( $_POST[ '_' . $field . '_status' ] ) && ! empty( $_POST[ '_' . $field ] ) )
		if ( ! empty( $_POST[ '_' . $field ] ) ) {
			update_post_meta( $post_id, $field, $_POST[ '_' . $field ] );
			// cfbe_save_meta_data( $field, $_POST[ '_' . $field ] );
			exit( __LINE__ . ':' . basename( __FILE__ ) . " ERROR<br />\n" );
		}
	}
}
// add_action('cfbe_save_fields', 'fitv_cfbe_save', 10, 2);
?>