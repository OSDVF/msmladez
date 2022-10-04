<?php /* Template Name: Archive */
//
// Template for a generic "archive page", a page which lists posts.
//

$query = new WP_Query(
	[
		'posts_per_page' => 4,
		'paged' => true,
		'post_type' => 'post',
		'orderby' => 'date',
		'order' => 'DESC',
		'paged' => get_query_var('paged')
	]
);

$query->have_posts();
$query->the_post();
$next = get_next_posts_link("Starší aktuality", $query->max_num_pages);
$prev = get_previous_posts_link("Novější aktuality");

$fbPosts = get_fb_posts();

view(
	[
		'posts' => $query->get_posts(),
		'next' => $next,
		'prev' => $prev,
		'fbPosts' => $fbPosts
	]
);
