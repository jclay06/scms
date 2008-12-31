<?php echo '<?xml version="1.0" encoding="UTF-8" ?>',"\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<link rel="icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo THEMES_URL,THEME; ?>/style.css?<?php echo filemtime(THEMES.THEME.'/style.css'); ?>" type="text/css" />
<?php if(getcwd() == ROOT.'admin') { ?>
	<style type="text/css">@import url("<?php echo THEMES_URL,THEME; ?>/admin.css?<?php echo filemtime(THEMES.THEME.'/style.css'); ?>");</style>
<?php } ?>
	<link rel="alternate" type="application/rss+xml" title="RSS" href="/feed.php" />
	<title><?php if(isset($page_title)) echo $page_title; ?></title>
</head>
<body>

<div id="content">

<h1><a href="#">The Spiffy Life . com</a></h1>
	
<div id="menubar">

	<div id="menunav">
		<?php if($comic->ID !== $comic->first) { ?><a href="<?php echo $comic->get_link($comic->prev); ?>"><span class="prev">&#9668;</span></a><?php } ?>
		<?php if($comic->ID !== $comic->last) { ?><a href="<?php echo $comic->get_link($comic->next); ?>"><span class="next">&#9658;</span></a><?php } ?>
	</div>
	
	<div id="menu">
		<ul>
			<li><a href="<?php echo DOMAIN; ?>">Home</a></li>
			<li><a href="#">Comics</a></li>
			<li><a href="#">Extras</a></li>
			<li><a href="<?php echo DOMAIN; ?>archives">Archives</a></li>
			<li><a href="<?php echo DOMAIN; ?>blog/">News</a></li>
		</ul>
	</div>
	
	<div id="searchbar">
		<input type="text" id="search" value="Search Our Comics!" />
		<div id="searchresults"></div>
	</div>
	
</div>