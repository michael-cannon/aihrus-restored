<?php get_header(); ?>

	<?php if (of_get_option('home_widgets_location') == 'above') { ?>
	<?php $sb_count = wp_get_sidebars_widgets(); ?>
	<?php if (count( $sb_count['Home_Page']) != '0') { ?>
	<div id="home_widgets_top" class="<?php if (count( $sb_count['Home_Page']) <= '4') { ?>home_widget_count<?php count_sidebar_widgets( 'Home_Page' );?><?php } else { ?>home_widget_overflow<?php } ?>">
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Home_Page') ) : endif; ?>
		<div class="clear"></div>
	</div>
	<?php } ?>
	<?php } ?>
	
<?php /* START PRODUCTS */
$args = array(
	'ignore_sticky_posts' => 1,
	'meta_key' => '_edd_download_sales',
	'meta_value_num' => '_edd_download_sales',
	'order' => 'DESC',
	'orderby' => 'meta_value_num',
	'paged' => $paged,
	'posts_per_page' => stripslashes( of_get_option('home_products_total') ),
	'post_type' => 'download',
);
$query_default = new WP_Query( $args );
if ( $query_default->have_posts() ) : ?>
	<?php if (of_get_option('subheading_text') != '') { ?>
	<h2 id="latest_products_title"><?php echo stripslashes(of_get_option('subheading_text')); ?></h2>
	<?php } ?>
	<div id="products_grid">
	<?php while ( $query_default->have_posts() ) : $query_default->the_post(); global $more; $more = 0; ?>
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
		<div class="clear"></div>
	</div> <?php // end #products_grid ?>
	<?php else : // else; no posts
		echo "Uh oh, we don't have any products yet!";
	endif; ?>
	<?php wp_reset_query(); /* END PRODUCTS */ ?>
	
	<?php if (of_get_option('view_all_products_text') != '') { ?>
	<?php if (of_get_option('store_link') != '') { ?>
	<h3 id="all_products_cta"><a class="all_products_call" href="<?php echo stripslashes(of_get_option('store_link')); ?>" title="<?php _e('All Products', 'designcrumbs'); ?>"><?php echo stripslashes(of_get_option('view_all_products_text')); ?></a></h3>
	<?php } } ?>
	
	<?php if (of_get_option('home_widgets_location') == 'below') { ?>
	<?php $sb_count = wp_get_sidebars_widgets(); ?>
	<?php if (count( $sb_count['Home_Page']) != '0') { ?>
	<div id="home_widgets" class="<?php if (count( $sb_count['Home_Page']) <= '4') { ?>home_widget_count<?php count_sidebar_widgets( 'Home_Page' );?><?php } else { ?>home_widget_overflow<?php } ?>">
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Home_Page') ) : endif; ?>
		<div class="clear"></div>
	</div>
	<?php } ?>
	<?php } ?>
	
<?php get_footer(); ?>