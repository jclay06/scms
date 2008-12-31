<?php

$info = '';
$title = '';

// show most recent comments
if('' == $_VAR) {
	$items = $comments->get_archives(FEED_ITEMS);
	$title = ' - Comments on TheSpiffyLife';
	$info = count($items) . ' Most Recent Comments';
}

else {

	// generate Comment # images for RSS Feed
	if(preg_match('@^num/(\w+)[-|/](\d+)/?$@', $_VAR, $cnum)) {
		$cat = $comic->cat_info_by_nicename( strtolower($cnum[1]) );
		$ID = (int) $cnum[2];
		if($cat === false) return die('Category is not listed in the Database!');
		$num = $comments->comments_by_cat_on($cat->ID, $ID);
		$text = ($num > 0) ? 'Comments ('.$num.')' : 'Add a Comment';
		header('Content-type: image/png');
		$im = imagecreate(100, 30);
		$white = imagecolorallocate($im, 255, 255, 255);
		$blue = imagecolorallocate($im, 0, 0, 255);
		imagefill($im, 0, 0, $white);
		imagettftext($im, 10, 0, 5, 20, $blue, PHP_DIR.'SenateBold.ttf', $text);
		imagepng($im);
		imagedestroy($im);
		exit;
	}

	// specific category comment feed
	elseif(preg_match('@^(\w+)/?(?:(\d+)/?)?@', $_VAR, $cnum)) {
		$cat = $comic->cat_info_by_nicename( strtolower($cnum[1]) );
		if($cat === false) return die('Category is not listed in the Database!');
		if(!isset($cnum[2])) {
			$comments->archives_cat = $cat->ID;
			$items = $comments->get_archives(FEED_ITEMS);
			$title = ' - Comments on the &quot;'.$cat->category.'&quot; Category';
			$info = count($items) . ' Most Recent Comments';
		}
		else {
			$comic->cat = $cat->ID;
			$ID = (int) $cnum[2];
			$comic->get_comic($ID);
			$comments->get_comments($comic->PID);
			$items = $comments->return_comments();
			$title = ' - Comments on &quot;'.$comic->title.'&quot;';
			$info = count($items) . ' Most Recent Comments';
		}
	}

}

header('Content-type: application/rss+xml');

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
<atom:link href="<?php echo rtrim(DOMAIN, '/'), $_PATH; ?>" rel="self" type="application/rss+xml" />

<?php

$i = 0;

if($items)
	foreach($items as $item) {
	
		++$i;
		if($i > FEED_ITEMS) break;

		$url = DOMAIN . $comic->cat_info($item->cat)->nicename . '/' . $item->comicID . '#comment-' . $item->ID;
		if(!isset($item->title))
			$item->title = $scdb->get_var("SELECT `title` FROM `$scdb->comics` WHERE `PID` = '$item->comicPID' LIMIT 1");
	
?>

	<item>
		<title><?php echo $item->name; ?> on &quot;<?php echo $item->title; ?>&quot;</title>
		<link><?php echo $url; ?></link>
		<dc:creator><?php echo $item->name; ?></dc:creator>
		<description><![CDATA[<?php echo nl2br( replace_smilies($item->comment) ); ?>]]></description>
		<pubDate><?php echo get_date('D, d M Y H:i:s O', $item->time); ?></pubDate>
		<guid isPermaLink="true"><?php echo $url; ?></guid>
	</item>

<?php } ?>

</channel>
</rss>