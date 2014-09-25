<?php
/**
 * Template Name: Blog
 */

global $is_blog;
$is_blog						= true;

add_filter( 'the_excerpt', 'prepend_post_thumbnail' );
 
class custom_loop extends thesis_custom_loop {
	function page() {
		global $paged;
		query_posts('type="posts"&paged='.$paged);
		thesis_loop::home();
	}
}

$blog_loop = new custom_loop;

thesis_html_framework();

remove_filter( 'the_excerpt', 'prepend_post_thumbnail' );