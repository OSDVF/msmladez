<?php
function truncate($string, $length, $dots = "...")
{
	return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

$loc = (new DateTime)->getTimezone();

function convert_fb_post($fb_post, $trim = false, $trimLinks = false)
{
	// get the local timezone
	global $loc;
	$fbPost = new stdClass();
	$fbPost->date_time = (new DateTimeImmutable($fb_post->created_time))->setTimezone($loc)->format('n.j.Y H:i:s');

	$fbPost->content = $fb_post->message;
	if (isset($fb_post->full_picture))
	{
		$fbPost->img = $fb_post->full_picture;
	}
	if (isset($fb_post->attachments))
	{
		$fbPost->images = [];
		foreach ($fb_post->attachments->data as $attachment)
		{
			foreach ($attachment->subattachments->data as $subatt)
			{
				if (isset($subatt->media) && isset($subatt->media->image))
				{
					array_push($fbPost->images, $subatt->media->image);
				}
			}
		}
	}

	if ($trim)
	{
		$trimmed = truncate($fbPost->content, 500);
	}
	else
	{
		$trimmed = $fbPost->content;
	}

	// Auto links
	$fbPost->content = preg_replace_callback(
		'/((http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?)/',
		function ($matches) use ($trimLinks)
		{
			return '<a href="' . $matches[0] . '">' . ($trimLinks ? truncate($matches[0], 20) : $matches[0]) . '</a>';
		},
		$trimmed
	);

	$fbPost->id = $fb_post->id;
	return $fbPost;
}

function get_fb_posts($maxCount = 3)
{
	include_once __DIR__ . '/../access.php';
	try
	{
		$fb  = @file_get_contents("https://graph.facebook.com/$pageId/posts?access_token=$accessToken&fields=message,created_time,full_picture,story");
	}
	catch (Exception)
	{
		$fb = null;
	}
	if ($fb)
	{
		$fb = json_decode($fb, false);
		$fbPosts = [];
		$i = 0;
		foreach ($fb->data as $fb_post)
		{
			if (!isset($fb_post->message) || isset($fb_post->story))
			{
				continue;
			}
			if ($i++ >= $maxCount)
			{
				break;
			}
			$fbPost = convert_fb_post($fb_post, true, true);
			$fbPost->content = preg_replace("/\R/", '<br/>', $fbPost->content);
			array_push($fbPosts, $fbPost);
		}
	}
	else
	{
		$fbPosts = null;
	}
	return $fbPosts;
}

function get_fb_post($fbId)
{
	include_once __DIR__ . '/../access.php';
	try
	{
		$fb  = @file_get_contents("https://graph.facebook.com/$fbId/?access_token=$accessToken&fields=message,attachments{subattachments},created_time");
		if ($fb == null)
		{
			return null;
		}
		$fb = json_decode($fb, false);
	}
	catch (Exception)
	{
		return null;
	}

	return convert_fb_post($fb);
}
