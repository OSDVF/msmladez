<?php
//
// Template for 'Homepage' as configured in 'Settings > Reading'
//
// This is the actual home page of the website.

$upcomingEvents = get_posts([
	"post_type"	=> "event",
	"meta_query" => [
		"key" => "end_date",
		"value" => date("Y-m-d"),
		"compare" => ">="
	],
	"meta_key" => "start_date",
	"orderby" => "meta_value",
	"order" => "ASC",
	"numberposts" => 3,
]);

$latestPosts = get_posts([
	"orderby" => "date",
	"order" => "DESC",
	"numberposts" => 4,
]);

$fbPosts = get_fb_posts(4);

view([
	"upcomingEvents" => $upcomingEvents,
	"latestPosts" => $latestPosts,
	'fbPosts' => $fbPosts
]);
