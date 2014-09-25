<?php
/** 
 * Template Name: Homepage
 */

remove_filter( 'the_content', 'rtsocial_counter' );
remove_filter( 'the_excerpt', 'rtsocial_counter' );
 
global $thesis_design;

// Load Slider Stuff
if (themedy_get_option('slider')) { 
	wp_enqueue_script('flexslider', CHILD_URL.'/lib/js/jquery.flexslider-min.js', array('jquery'), '1.8', TRUE);
	wp_enqueue_style('flexslider-style', CHILD_URL.'/lib/js/flexslider.css',$deps,'1.8');
}

// Add slider options
add_action('thesis_hook_after_html', 'themedy_slider_options');
function themedy_slider_options() { 
	if (themedy_get_option('slider')) { ?>
		<script type="text/javascript" charset="utf-8">
			jQuery(window).load(function() {
				jQuery('.flexslider').flexslider({
					controlNav: true,
					animation: "<?php echo themedy_option('slider_effect'); ?>",
					slideshowSpeed: <?php echo themedy_option('slider_pause'); ?>, 
					animationDuration: <?php echo themedy_option('slider_speed'); ?>
				});
			});
		</script>
	<?php }
}

echo apply_filters('thesis_doctype', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">') . "\n"; 
?> <html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>> <?php
thesis_head::build();
echo "<body" . thesis_body_classes() . ">\n"; #filter
thesis_hook_before_html(); #hook

thesis_wrap_header();
thesis_hook_before_content_area(); #hook
?>
<div id="content_area" class="full_width themedy-frontpage">
    <div class="page">
    	<div id="content_box" class="no_sidebars format_text">
	       	<?php if (is_active_sidebar('homepage-top-area')) { ?>
                <div class="home-row home-single">
                    <div class="wrap">
                        <div class="widget_area">
                            <?php dynamic_sidebar('Homepage Top Area'); ?> 
                        </div>
                    </div>
                </div>
            <?php } ?>

			<?php if (themedy_get_option('slider') != 'remove') { ?>
                <div class="flexslider">
                    <ul class="slides">
                        <?php 
                        $terms = explode(',', themedy_get_option('slider_categories'));
                        if (themedy_get_option('slider') == 'portfolio' and themedy_get_option('slider_categories')) {
                            query_posts( array ('posts_per_page' => themedy_get_option('slider_limit'), 'post_type' => themedy_get_option('slider'), 'tax_query' => array( array( 'taxonomy' => 'portfolio-category', 'field' => 'id', 'terms' => $terms, 'operator' => 'IN' ) ) ) ); 
                        }
                        elseif (themedy_get_option('slider') == 'post' and themedy_get_option('slider_categories')) {
                            query_posts( array ('posts_per_page' => themedy_get_option('slider_limit'), 'post_type' => themedy_get_option('slider'), 'cat' => themedy_get_option('slider_categories') ) );
                        }
                        elseif (themedy_get_option('slider') == 'page' and themedy_get_option('slider_categories')) {
                            $include = explode(',', themedy_get_option('slider_categories'));
                            query_posts( array ('post_type' => themedy_get_option('slider'), 'post__in' => $include, 'showposts' => themedy_get_option('slider_limit') ) );
                        }
                        else {
                            query_posts( array ('posts_per_page' => themedy_get_option('slider_limit'), 'post_type' => themedy_get_option('slider') ) );
                        }
                        
                        global $post;
                        if ( have_posts() ) : while ( have_posts() ) : the_post();
                            ?>
                            <li class="slide">	
                                <?php 
                                if (has_post_thumbnail()) {
									$img = get_the_post_thumbnail($page->ID, 'Slide', array( 'title'	=> trim(strip_tags( $attachment->post_title )) ) );
								} else {
									$img = '<img src="'.CHILD_URL.'/images/noimage940x350.png" alt="" title="'.$post_title.'" />';
								}
								echo '<a href="'.get_permalink().'" title="'.the_title_attribute('echo=0').'">'.$img.'</a>';
								$excerpt = get_the_excerpt();
								if (strlen($excerpt) > 120) {
								$excerpt = substr($excerpt,0,strpos($excerpt,' ',120)); }
								$excerpt = $excerpt.' ...';
								// echo '<a href="'.get_permalink().'" title="'.the_title_attribute('echo=0').'">'.$img.'</a>';
								if (themedy_get_option('slider_captions')) {
									echo '<div class="flex-caption"><div class="container"><h4>';
									echo '<a href="'.get_permalink().'" title="'.the_title_attribute('echo=0').'">';
									echo get_the_title();
									echo '</a>';
									echo '</h4><p>';
									echo $excerpt;
									echo '</p></div></div>';
								}
                                ?>	
                            </li>
                        <?php endwhile; else: endif; wp_reset_query(); ?>
                    </ul>
                </div>
            <?php } ?>
            
            <?php if ( is_active_sidebar('homepage-1') or is_active_sidebar('homepage-2') or is_active_sidebar('homepage-3') ) { ?>
            <div class="home-row home-columns">
            	<div class="wrap">
                    <div class="one-third first">
                        <?php dynamic_sidebar('Homepage 1'); ?> 
                    </div>
                    <div class="one-third">
                        <?php dynamic_sidebar('Homepage 2'); ?> 
                    </div>
                    <div class="one-third">
                        <?php dynamic_sidebar('Homepage 3'); ?> 
                    </div>
                </div>
            </div>
            <?php } ?>
			<?php if (themedy_get_option('recent_work')) { ?>
                <div class="home-row recentwork">
                	<div class="wrap">
                        <div class="one-fourth first">
                        	<?php dynamic_sidebar('Recent Work Column'); ?>
                        </div>
                        <?php 
                        query_posts( array ('posts_per_page' => 3, 'post_type' => 'testimonials-widget', 'orderby' => 'rand', 'meta_key' => '_thumbnail_id' ) );
                        global $is_blog, $post, $wp_query;
						if ( empty( $is_blog ) ) {
							$wp_query->is_front_page = 1;
							$wp_query->is_home = 1;
						}
                        $loop_counter = 1;
                        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                            <div class="one-fourth">
                                <?php 
                                $title = get_the_title();
                                
                                $excerpt = get_the_content();
                                if (strlen(strip_tags($excerpt)) > 200) {
									$excerpt = strip_tags($excerpt);
									$excerpt = substr($excerpt,0,strpos($excerpt,' ',200));
                                $excerpt = $excerpt.' …';
								}
                                
								if (has_post_thumbnail()) {
									$img = get_the_post_thumbnail($page->ID, 'Home Thumb', array( 'title'	=> trim(strip_tags( $attachment->post_title )) ) ); 
									printf( '<a href="%s" class="thumb" title="%s">%s</a>', get_permalink(), the_title_attribute('echo=0'), $img ); 
                            	} 
                                ?>
                                <div class="excerpt_wrap">
                                    <h3><a href="<?php the_permalink(); ?>" class="title"><?php echo $title; ?></a></h3>
                                    <div class="excerpt">
                                        <?php echo apply_filters('the_excerpt',$excerpt); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; else: endif; wp_reset_query(); ?>
                    </div>
                </div>
            <?php } ?>
            <?php if (themedy_get_option('latest_posts')) { ?>
                <div class="home-row latestposts">
                	<div class="wrap">
                        <div class="one-fourth first">
                        	<?php dynamic_sidebar('Latest Posts Column'); ?>
                        </div>
                        <?php 
                        query_posts( array ('posts_per_page' => 3, 'post_type' => 'post' ) );
                        global $post;
                        $loop_counter = 1;
                        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                            <div class="one-fourth">
                                <?php 
                                $title = get_the_title().' ';
                                
                                $excerpt = get_the_excerpt();
                                if (strlen($excerpt) > 150) {
                                $excerpt = substr($excerpt,0,strpos($excerpt,' ',150)); }
                                $excerpt = $excerpt.' ...';
                                ?>
                                <h3><a href="<?php the_permalink(); ?>" class="title"><?php echo $title; ?></a></h3>
                                <p class="date"><?php echo get_the_date(); ?></p>
                                <div class="excerpt">
                                    <?php echo apply_filters('the_excerpt',$excerpt); ?>
                                    <p class="readmore"><a href="<?php the_permalink(); ?>">Read More ...</a></p>
                                </div>
                            </div>
                        <?php endwhile; else: endif; wp_reset_query(); ?>
                    </div>
                </div>
            <?php } ?>

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

?>