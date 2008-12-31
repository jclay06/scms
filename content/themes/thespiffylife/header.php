<?php
global $comic;
echo '<?xml version="1.0" encoding="UTF-8"?>',"\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="keywords" content="Comics, Webcomics, GJComics, TheSpiffyLife, Spiffy, Guy, Josh, the spiffy life, hilarious web comics, spiffy life, jackofallblades, college, funny, funnies, guitar loser, mud wrestling, innuendos, sex, disney, comic, fun, stuff, quirky, barebones, blog, spiffynews, rss, hosting, gamers, crazy, school, dreamhost, donate, spiffyalert, hosting, shared hosting, dreamhost, dream, website, reseller"/>
<link rel="icon" href="<?php theme_link(); ?>/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" href="<?php theme_link(); ?>/style.css?<?php echo filemtime(THEMES.THEME.'/style.css'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="RSS" href="http://feeds.feedburner.com/thespiffylife-comics-news" />

<script type="text/javascript" src="<?php echo DOMAIN; ?>content/js/mootools-1.2.1.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN; ?>content/js/mootools-1.2m.js"></script>

<script type="text/javascript" src="<?php theme_link(); ?>/search.js"></script>

<?php if(got_comic()) { ?>
	<?php if(!is_index()) { ?>
	<script type="text/javascript" src="<?php theme_link(); ?>/comments.js"></script>
	<?php } ?>
	<link rel="image_src" href="<?php echo $comic->get_image(); ?>" />
<?php } ?>
