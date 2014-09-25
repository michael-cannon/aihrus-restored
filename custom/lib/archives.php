<?php
/**
 *  WordPress Thesis theme archive customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// remove_action('thesis_hook_archives_template', 'thesis_archives_template');
// add_action('thesis_hook_archives_template', 'custom_template_archives');


function custom_template_archives() {
	$cats	= get_categories("hierarchical=0");
	if( $cats != NULL) { ?>
	<ul id="cat_list">
	<?php foreach ( $cats as $cat) { ?>
		<li>
		<?php if( $cat != NULL) {
			$catName	= $cat->cat_name;
			$catID		= get_cat_ID( $catName);
			$base_url	= get_category_link( $catID);
		?>
			<h3><a href="<?php echo $base_url?>"><?php echo $catName; ?></a></h3>
		<?php } ?>
		<?php // Show category description
			if ( $cat->category_description != NULL) { ?>
				<p><?php echo $cat->category_description; ?></p>
			<?php } ?>

				<?php
					$args		= array(
										'numberposts' => 10,
										'offset'=> 0,
										'category' => $cat->cat_ID
								);
					$myposts	= get_posts( $args );
				?>
				<ul>
				<?php foreach( $myposts as $post) : 
					$date	= $post->post_date;
					$time	= strtotime( $date);
				?>
					<li><?php echo date("F j, Y", $time); ?> <a href="<?php echo $post->guid; ?>"><?php echo $post->post_title; ?></a></li>
				<?php endforeach; ?>
					<li><?php _e('See the rest of', 'custom'); ?> <a href="<?php echo $base_url?>"><?php echo $catName; ?></a></li>
				</ul>
			</li>
	<?php } ?>
	</ul>
	<?php }
}

?>
