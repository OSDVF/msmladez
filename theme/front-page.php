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

function truncate($string, $length, $dots = "...")
{
	return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

include_once __DIR__ . '/access.php';
$fb  = file_get_contents("https://graph.facebook.com/102242531280956/posts?access_token=$accessToken&fields=message,created_time,full_picture,story");
$fb = json_decode($fb, false);

$latestPosts = get_posts([
	"orderby" => "date",
	"order" => "DESC",
	"numberposts" => 4,
]);

// get the local timezone
$loc = (new DateTime)->getTimezone();
$fbPosts = [];
$i = 0;
foreach ($fb->data as $fb_post)
{
	if (!isset($fb_post->message) || isset($fb_post->story))
	{
		continue;
	}
	if ($i++ >= 3)
	{
		break;
	}
	$fbPost = new stdClass();
	$fbPost->date_time = (new DateTimeImmutable($fb_post->created_time))->setTimezone($loc)->format('n.j.Y H:i:s');

	/*$splitted = preg_split(
		'/\n|(\.+)|(?!\!)(?<=\!)|(?<=.{30})\b/u',
		$fb_post->message,
		2,
		PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
	);*/

	$fbPost->content = $fb_post->message;
	$fbPost->img = $fb_post->full_picture;

	$trimmed = truncate($fbPost->content,500);
	// Auto links
	$fbPost->content = preg_replace_callback(
		'/((http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?)/',
		function ($matches)
		{
			return '<a href="' . $matches[0] . '">' . truncate($matches[0], 20) . '</a>';
		},
		$trimmed
	);
	$fbPost->content = preg_replace("/\R/", '<br/>', $fbPost->content);

	$fbPost->link = 'https://facebook.com/' . $fb_post->id;
	array_push($fbPosts, $fbPost);
}

view([
	"upcomingEvents" => $upcomingEvents,
	"latestPosts" => $latestPosts,
	'fbPosts' => $fbPosts
]);
