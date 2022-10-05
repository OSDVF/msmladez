<?php /* Template Name: FB Post */
if (!isset($_GET['fbId']))
{
	view([
		'date' => 'Chyba',
		'content' => 'Tento odkaz na příspěvek z facebooku je neplatný'
	]);
	return;
}


$fbPost = get_fb_post($_GET['fbId']);
if (!isset($fbPost))
{
	view([
		'date' => 'Chyba',
		'content' => 'Nepodařio se načíst příspěvek z facebooku.',
		'link' => 'https://facebook.com/' . $_GET['fbId']
	]);
	return;
};
//'/\n|(\.+)|(?!\!)(?<=\!)|(?<=.{30})\b/u',
$splitted = preg_split(
	'/\n|(\.+)|(?!\!)(?<=\!)/u',
	$fbPost->content,
	2,
	PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
);

$fbPost->content = isset($splitted[1]) ? $splitted[1] : '';
view(
	[
		'title' => $splitted[0],
		'date' => $fbPost->date_time,
		'content' => preg_replace("/\R/", '<br/>', $fbPost->content),
		'images' => $fbPost->images ?? [(object)['src' => $fbPost->img]] ?? [], //All attachments or full_picture or nothing
		'link' => 'https://facebook.com/' . $fbPost->id
	]
);
