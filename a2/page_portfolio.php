<?php
/**
 * Template Name: Portfolio
 */

global $thesis_design;

// New Excerpt Length
add_filter('excerpt_length', 'new_excerpt_length');
function new_excerpt_length($length) { 
	return 250; 
}

// New Excerpt More
add_filter('excerpt_more', 'new_excerpt_more');
function new_excerpt_more($more) {
	return '...';
}

echo apply_filters('thesis_doctype', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">') . "\n"; 
?> <html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>> <?php
thesis_head::build();
echo "<body" . thesis_body_classes() . ">\n"; #filter
thesis_hook_before_html(); #hook

thesis_wrap_header();
thesis_hook_before_content_area(); #hook
?>
<div id="content_area" class="full_width">
    <div class="page">
    	<div id="content_box" class="no_sidebars">
            <div id="content" class="testimonials-widget-page format_text">
                <?php 
if ( function_exists( 'testimonialswidgetpremium_count' ) ) {
	echo '<h1>';
	echo testimonialswidgetpremium_count();
	echo ' Cheers for Aihrus and Michael Cannon</h1>';
}

				global $post;
				$portfolio_category = get_post_meta($post->ID, 'portfolio_category', true);
                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                $args = array('post_type' => 'testimonials-widget', 'showposts' => -1, 'paged' => $paged, 'portfolio-category' => $portfolio_category);
                $query_args = wp_parse_args($cf, $args);
                $wp_query = new WP_Query($query_args);
                $loop_counter = 0;
                if ( $wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); /* Start Posts */
                    ?>
                    <div id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> testimonials-widget hentry type-testimonials-widget testimonials-widget-teaser one-third<?php if ($loop_counter == 0) { echo ' first'; } ?>">
                        <?php 
                        if ( false && has_post_thumbnail()) { ?>
                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                                <?php the_post_thumbnail('Portfolio Thumbnail');  ?>
                                </a>
                         <?php } ?>
                        <div class="headline_area">
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" ><?php the_title(); ?></a>
                            </h2>
                        </div>
                        <div class="entry-content excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>
                    <?php 
                     
                $loop_counter++;
				if ($loop_counter == 3) { echo '<div class="clear"></div>'; $loop_counter = 0; }
                endwhile; /** end of one post **/ else : /** if no posts exist **/ endif; /** end loop **/
                wp_reset_query();
				thesis_post_navigation();
                ?>
            </div>
        </div>
    </div>
</div>
<?php
thesis_hook_after_content_area(); #hook
thesis_wrap_footer();

thesis_ie_clear();
thesis_javascript::output_scripts();
thesis_hook_after_html(); #hook
echo "</body>\n</html>";