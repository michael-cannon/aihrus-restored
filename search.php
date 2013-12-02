<?php get_header(); ?>
<div id="archive_grid_wrap">
	<h2 class="post_title"><?php /* Search Count */ $allsearch = &new WP_Query("s=$s&showposts=-1&post_type=download"); $count = $allsearch->post_count; echo $count . ' '; wp_reset_query(); ?><?php _e('Search Results for', 'designcrumbs'); ?> <strong><?php the_search_query() ?></strong></h2>
	<div id="archive_grid">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="single_grid_product">
			<?php if (has_post_thumbnail()) { ?>
			<div class="product_med_wrap">
				<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" class="single_product_image_link">
					<?php the_post_thumbnail( 'product_med', array('alt' => get_the_title()) ); ?>
				</a>
			</div>
			<?php } else { ?>
			<div class="product_med_wrap">
				<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" class="single_product_image_link">
					<img src="<?php echo get_template_directory_uri(); ?>/images/defaultimg.png" alt="<?php _e('Digital Download', 'designcrumbs'); ?>" />
				</a>
			</div>
			<?php } ?>
			<h3><a class="grid_title" href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
			<div class="product_meta">
				<div class="left">	
					<?php if(edd_has_variable_prices(get_the_ID())) { ?>	
						<?php _e('Starting at', 'designcrumbs'); ?> <?php edd_price(get_the_ID()); ?>
					<?php } else { ?>
						<?php edd_price(get_the_ID()); ?>	
					<?php } ?>				
				</div>
				<div class="right">
					<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" class="more-link"><?php _e('View Details', 'designcrumbs'); ?> &raquo;</a>
				</div>
				<div class="clear"></div>
			</div>
		</div>
        
		<?php endwhile; ?>
        
		<div class="navigation">
			<div class="nav-prev"><?php next_posts_link( __('&laquo; Older Entries', 'designcrumbs')) ?></div>
			<div class="nav-next"><?php previous_posts_link( __('Newer Entries &raquo;', 'designcrumbs')) ?></div>
			<div class="clear"></div>
		</div>

	<?php else : ?>

	<h2><?php _e('Sorry, we can\'t seem to find what you\'re looking for.', 'designcrumbs'); ?></h2>
	<p><?php _e('Please try one of the links on top.', 'designcrumbs'); ?></p>
        
	<?php endif; ?>
	</div><!-- end #archive_grid -->
</div><!-- end #archive_grid_wrap -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>