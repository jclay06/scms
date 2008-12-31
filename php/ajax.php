<?php 
require('../config.php');
require(PHP_DIR.'functions.php');
require(PHP_DIR.'comic.class.php');
require(PHP_DIR.'comments.class.php');

if(!isset($_SERVER['HTTP_REFERER']{1}))
	exit;
$URL = parse_url($_SERVER['HTTP_REFERER']);
$_PATH = $URL['path'];

/*
if(isset($URL['query']{1})) {
	$_QUERY = array();
	foreach(explode('&', $URL['query']) as $var) {
		list($q, $v) = explode('=', $var, 1);
		$_QUERY[$q] = $v;
	}
}
*/

if(isset($_GET['form']) && $_GET['form'] == 'comment') {

	if(isset($_GET['p']) && is_nat($_GET['p'])) {
		$ID = (int) $_GET['p'];
		$comic->get_comic_by_permalink($ID);
	}

	# assumes default category (ID = 1)
	elseif(preg_match('@^/(\d+)/@', $_PATH, $ID)) {
		$ID = (int) (isset($ID[1]) && is_numeric($ID[1])) ? $ID[1] : 0;
		$comic->get_comic($ID);
	}
	
	# /category/ or /category/ID/
	elseif(preg_match('@^/(\w+)/?(?:(\d{1,5})/?)?@', $_PATH, $cat_ID)) {
		$category = safe_text(strtolower($cat_ID[1]));
		$cat = (int) $scdb->get_var("SELECT `ID` FROM `$scdb->categories` WHERE `nicename` = '$category' LIMIT 1");
		if($scdb->num_rows == 1) {
			$comic->cat = $cat;
			$ID = (int) (isset($cat_ID[2]) && is_numeric($cat_ID[2])) ? $cat_ID[2] : 0;
			$comic->get_comic($ID);
		}
		else die;
	}
	
	else die;
	
	if($ID != $comic->ID) die;
	
	$comments->post_comment($_POST);
	
	if($comments->post->error === false) {
		$json = array(
					'time'=>get_date('F jS, Y @ g:i a', NOW),
					'comment'=>replace_smilies($comments->post->comment),
					'name'=>$comments->post->name,
					'website'=>$comments->post->website,
					'ID'=>$comments->post->ID,
					'message'=>$comments->message,
					'error'=>null
				);
		echo json_encode($json);
	}
	
	else {
		echo json_encode(array('error'=>$comments->message));
	}
	
}

elseif(isset($_GET['form']) && $_GET['form'] = 'search' && isset($_GET['s'])) {
	$comic->search($_GET['s'], 1, 5);
	if($comic->search->num_results > 0) {
		echo json_encode($comic->search->results);
	}
	else {
		echo json_encode(array('error'=>'Sorry, No Results.'));
	}
}

?>