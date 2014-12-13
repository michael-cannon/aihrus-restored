<?php
/*
Template Name: Store
*/
get_header(); ?>
<div class="store-home"> 
    <?php /* START PRODUCTS */
    global $paged, $wp_query, $wp;
	if  ( empty($paged) ) {
		if ( !empty( $_GET['paged'] ) ) {
			$paged = $_GET['paged'];
		} elseif ( !empty($wp->matched_query) && $args = wp_parse_args($wp->matched_query) ) {
			if ( !empty( $args['paged'] ) ) {
				$paged = $args['paged'];
			}
		}
		if ( !empty($paged) )
			$wp_query->set('paged', $paged);
        }      
	$temp = $wp_query;
	$args = array(
		'meta_key' => '_edd_download_sales',
		'meta_value_num' => '_edd_download_sales',
		'order' => 'DESC',
		'orderby' => 'meta_value_num',
		'paged' => $paged,
		'posts_per_page' => stripslashes( of_get_option('products_total') ),
		'post_type' => 'download',
	);
	$wp_query = new WP_Query( $args );
	if ( $wp_query->have_posts() ) : ?>
	
	<div id="products_grid">
	
	<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
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
					<img src="<?php echo get_template_directory_uri(); ?>/images/defaultimg.png" alt="" />
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
	<div class="navigation">
		<div class="nav-prev"><?php previous_posts_link( __('&laquo; Previous Page', 'designcrumbs')) ?></div>
		<div class="nav-next"><?php next_posts_link( __('Next Page &raquo;', 'designcrumbs')) ?></div>
		<div class="clear"></div>
	</div>

	<?php else : ?>
	<h2><?php _e('Sorry, we can\'t seem to find what you\'re looking for.', 'designcrumbs'); ?></h2>
	<p><?php _e('Please try one of the links on top.', 'designcrumbs'); ?></p>
        
	<?php endif; wp_reset_query(); ?>
</div><!-- end .store-home -->

<?php get_footer(); ?>