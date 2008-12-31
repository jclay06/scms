<?php
require('config.php');
require(PHP_DIR.'functions.php');

require(PHP_DIR.'comic.class.php');
require(PHP_DIR.'comments.class.php');

require(PHP_DIR.'news.class.php');

# permalink via query string
if( isset($_GET['p']) && $pid = is_nat($_GET['p']) ) {
	$comic->get_comic_by_permalink($pid);
	if(FORWARD_FROM_PERMALINK === true) {
		exit( header('Location: '.$comic->get_link()) );
	}
	get_cached_page();
	require(theme().'/single.php');
}

# search
elseif(isset($_GET['s']{1})) {
	if(file_exists(theme().'/search.php')) {
		$page = (int) (isset($_GET['page']) && is_nat($_GET['page'])) ? $_GET['page'] : 1;
		$comic->search($_GET['s'], $page, 10, false);
		require(theme().'/search.php');
	}
	else not_found();
}

# not a permalink
else {

get_cached_page();

$_PATH = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$_PATH = trim($_PATH, '/');
$_PATH = explode('/', $_PATH);
$_ACT = $_PATH[0];
$_VAR = isset($_PATH[1]) ? $_PATH[1] : '';
$_VARS = isset($_PATH[2]) ? array_slice($_PATH, 2) : array();

# home page
if('' == $_ACT) {
	require(theme().'/index.php');
}

# assumes default category (ID = 1)
elseif($ID = is_nat($_ACT)) {
	$comic->get_comic($ID);
	require(theme().'/single.php');
}

# archive page
elseif('archive' == $_ACT) {
	require(theme().'/archive.php');
}

# news posts (blog)
elseif('news' == $_ACT) {
	require(theme().'/news.php');
}

# rss feed
elseif('feed' == $_ACT) {
	require('feed.php');
}

# rss feed comment stuff
elseif('comments' == $_ACT) {
	require('feed-comments.php');
}

# /category/ or /category/ID/
elseif($cat = is_category($_ACT)) {
	$comic->cat = $cat->ID;
		$ID = is_nat($_VAR);
		$comic->get_comic($ID);
		require(theme().'/single.php');
}

# random comic
elseif($cat = is_category($_ACT) && 'random' == $_VAR) {
	$comic->cat = $cat->ID;
	$comic->random();
}
elseif('random' == $_ACT) {
	$comic->random();
}

# regex?
/*
elseif(LINK_STRUCTURE != '' && preg_match('@'.link_regex().'@', $_PATH, $link)) {

}
*/

# allowed theme files
elseif(preg_match('@^/(\w+\.php)$@', $_ACT, $file)) {
	$allowed = array('faq.php', 'contact.php', 'hosting.php');
	if(in_array($file[1], $allowed) && file_exists(theme().'/pages/'.$file[1])) {
		require(theme().'/pages/'.$file[1]);
	}
	else not_found();
}

else not_found();

}

cache_page();

db_stats();

?>