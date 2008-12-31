<?php
if(basename(__FILE__) == basename($_SERVER['REQUEST_URI'])) {
	exit(header('Location: /'));
}

header('Content-type: application/rss+xml');

$info = SITE_INFO;
$title = '';

// main feed option
if('' == $_VAR) {
	$items = $comic->get_archives(FEED_ITEMS, 'all');
}

else {

	// author feed
	if(preg_match('@^author/(\w+)/?$@', $_ACT, $author)) {
		$author = strtolower( trim($author[1], '/') );
		$uid = (int) $scdb->get_var("SELECT `ID` FROM `$scdb->users` WHERE `nicename` = '$author' LIMIT 1");
		if($scdb->num_rows != 1) return die('Author is not listed in the Database!');
		$info .= ' Author: ' . ucfirst($author);
		$items = $comic->get_archives(FEED_ITEMS, 'all', $uid);
	}

	// specific category feed
	else {
		$cat = $comic->cat_info_by_nicename( strtolower( trim($_ACT, '/') ) );
		if($cat === false) return die('Category is not listed in the Database!');
		$info .= ' Category: ' . $cat->category;
		$items = $comic->get_archives(FEED_ITEMS, $cat->ID);
	}

}

echo '<?xml version="1.0" encoding="UTF-8"?>';

?>

<rss version="2.0"
 xmlns:atom="http://www.w3.org/2005/Atom"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
>

<channel>
<title><?php echo SITE_NAME, $title; ?></title>
<link><?php echo DOMAIN; ?></link>
<description><?php echo $info; ?></description>
<lastBuildDate><?php echo date('D, d M Y H:i:s O', TIME); ?></lastBuildDate>
<language>en-us</language>
<atom:link href="<?php echo rtrim(DOMAIN, '/'), $_ACT; ?>" rel="self" type="application/rss+xml" />

<?php

$i = 0;

if($items)
	foreach($items as $item) {
	
		$cat_info = $comic->cat_info($item->cat);
	
		++$i;
		if($i > FEED_ITEMS) break;

		$url = $comic->get_permalink($item->PID);
		$comment_img = DOMAIN . 'comments/num/' . $cat_info->nicename . '/' . $item->ID;
		$content = '<a href="'.$url.'" title="'.$item->info.'"><img src="'.IMAGES.$cat_info->nicename.'/'.$item->image.'" alt="'.$item->title.'" width="250" /></a><br /><em>click on the image to see it on our site and rate it!</em><br /><br/><a href="'.$url.'#comments" title="Leave a Comment!"><img src="'.$comment_img.'" alt="Comments" /><br />';
	
?>

	<item>
		<title><?php echo $item->title; ?></title>
		<link><?php echo $url; ?></link>
		<comments><?php echo $url; ?>/#comments</comments>
		<dc:creator><?php echo $comic->user_info($item->author)->name; ?></dc:creator>
		<description><![CDATA[<?php echo $content; ?>]]></description>
		<pubDate><?php echo get_date('D, d M Y H:i:s O', $item->time); ?></pubDate>
		<guid isPermaLink="true"><?php echo $url; ?></guid>
	</item>

<?php } ?>

</channel>
</rss>