<?php

function get_date($format, $mysql_stamp) {
	return date($format, mysql_to_unix($mysql_stamp));
}

function mysql_to_unix($stamp) {
	$datetime = explode(' ', $stamp, 2);
	$dates = explode('-', $datetime[0], 3);
	$times = explode(':', $datetime[1], 3);
	return mktime($times[0], $times[1], $times[2], $dates[1], $dates[2], $dates[0]);
}

function replace_smilies($input) {
	$folder = DOMAIN . 'content/smilies/';
	$trans = array(
		":arrow:"=>"<img src='".$folder."icon_arrow.gif' title='arrow' alt=':arrow:' />",
		" :D"=>"<img src='".$folder."icon_biggrin.gif' title='Big Grin' alt=':D' />",
		" :-?"=>"<img src='".$folder."icon_confused.gif' title='confused' alt=':?' />",
		" 8-)"=>"<img src='".$folder."icon_cool.gif' title='cool' alt='8-)' />",
		" :'("=>"<img src='".$folder."icon_cry.gif' title='cry' alt=':&#39;(' />",
		" :&#39;("=>"<img src='".$folder."icon_cry.gif' title='cry' alt=':&#39;(' />",
		" o.O"=>"<img src='".$folder."icon_eek.gif' title='eek' alt='o.O' />",
		" >:["=>"<img src='".$folder."icon_evil.gif' title='evil' alt='>:[' />",
		" :!:"=>"<img src='".$folder."icon_exclaim.gif' title='exclaim' alt=':!:' />",
		":idea:"=>"<img src='".$folder."icon_idea.gif' title='idea' alt=':idea:' />",
		"LOL"=>"<img src='".$folder."icon_lol.gif' title='LOL' alt='LOL' />",
		" :x"=>"<img src='".$folder."icon_mad.gif' title='mad' alt=':x' />",
		":mrgreen:"=>"<img src='".$folder."icon_mrgreen.gif' title='Mr. Green' alt=':mrgreen:' />",
		" :-/"=>"<img src='".$folder."icon_neutral.gif' title='neutral' alt=':-/' />",
		":?:"=>"<img src='".$folder."icon_question.gif' title='question' alt='???' />",
		" :P"=>"<img src='".$folder."icon_razz.gif' title='razz' alt=':P' />",
		":oops:"=>"<img src='".$folder."icon_redface.gif' title='redface' alt=':oops:' />",
		":roll:"=>"<img src='".$folder."icon_rolleyes.gif' title='roll eyes' alt=':roll:' />",
		" :("=>"<img src='".$folder."icon_sad.gif' title='sad' alt=':(' />",
		" :)"=>"<img src='".$folder."icon_smile.gif' title='smile' alt=':)' />",
		" :O"=>"<img src='".$folder."icon_surprised.gif' title='surprised' alt=':O' />",
		" >:]"=>"<img src='".$folder."icon_twisted.gif' title='twisted' alt='>:]' />",
		" ;)"=>"<img src='".$folder."icon_wink.gif' title='wink' alt=';)' />"
		);
	return strtr($input, $trans);
}

function is_image($file) {
	$dot = strrpos($file, '.') + 1;
	$ext = substr($file, $dot);
	$allowed = array('jpg', 'jpeg', 'gif', 'png');
	if(in_array($ext, $allowed)) return true;
	return false;
}

function safe_text($text, $length=100, $type='') {
	
	global $scdb;

	$text = trim($text);
	
	if('html' == $type) {
		$text = strtr($text, array('<?'=>'&lt;?', '?>'=>'?&gt;'));
		$text = strip_tags($text, "<strong><em><u>");	
	}
	elseif('image' == $type) {
		$ext = substr(strrchr($text, '.'), 1);
		$pos = strrpos($text, $ext);
		if(!$pos)
			return false;
		$name = substr($text, 0, $pos-1);
		$name = substr($name, 0, 96);
		$ext = strtolower($ext);
		$ext = strtr($ext, 'jpeg', 'jpg');
		if(!in_array($ext, array('jpg','gif','png')))
			return false;
		$name = strtr($name, ' ', '_');
		$name = preg_replace("/![A-Za-z0-9_-]/", "", $name);
		$image = $name . '.' . $ext;
		return $image;
	}
	else $text = htmlentities($text, ENT_QUOTES);
	
	$text = $scdb->escape($text);
	
	if(is_numeric($length) && $length > 0 && isset($text{$length}))
		$text = substr($text, 0, $length);
	
	return $text;
	
}

function urltest($url) {
	$syntax = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
	if(eregi($syntax, $url)) return $url; 
	return '';
}

function url2awesome($string, $width=378) {
	$syntax = "(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?";
	if(eregi($syntax, $string, $url)) {
		$url = $url[0];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
		curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		$mime = $info['content_type'];
		if($mime == 'image/jpeg' || $mime == 'image/gif' || $mime == 'image/png') {
			$image = '<a href="'.$url.'" target="_blank" rel="nofollow"><img src="'.$url.'" style="max-width:'.$width.'px" /></a>';
			$string = str_replace($url, $image, $string);
		}
		elseif($info['http_code'] != 404) {
			$link = '<a href="'.$url.'" target="_blank" rel="nofollow">'.$url.'</a>';
			$string = str_replace($url, $link, $string);
		}
	}
	return $string;
}

function mailer($subject, $message) {
	$to = "gjcomics@gmail.com";
	$headers = "From: <".EMAIL.">\r\n";
	$headers .= "MIME-Version: 1.0\r\n"; 
	$headers .= "Content-type: text/html";
	mail($to, $subject, $message, $headers);
}

function serve_time($round=3) {
	if(!defined("START") || !is_numeric($round)) return false;
	return round( microtime(true) - START, $round );
}

function db_stats() {
	global $scdb;
	if(isset($scdb) && is_object($scdb) && isset($scdb->num_queries)) {
		echo "\n<!-- ", $scdb->num_queries, " Total Database Queries -->";
	}
	echo "\n",'<!-- Page served in ', serve_time(), ' seconds -->';
}

function sort_by_mtime($file1, $file2) {
	$time1 = filemtime($file1);
	$time2 = filemtime($file2);
	if($time1 == $time2) {
		return 0;
	}
	return ($time1 < $time2) ? 1 : -1;
}

function sort_by_ID($ID1, $ID2) {
	if($ID1 == $ID2) return 0;
	return ($ID1 > $ID2) ? 1 : -1;
}

function pwhash($pass) {
	$salt = substr(md5($pass), 5, 10);
	$ssalt = substr(sha1($salt), 3, 8);
	$pass = md5($salt . $pass . $ssalt);
	$pass = sha1($pass);
	return $pass;
}

function get_header() {
	if(file_exists(theme().'/header.php'))
		require(theme().'/header.php');
	else echo '<p>Error: header.php file does not exist in the theme folder!</p>';
}

function get_sidebar() {
	if(file_exists(theme().'/sidebar.php'))
		require(theme().'/sidebar.php');
	else echo '<p>Error: sidebar.php file does not exist in the theme folder!</p>';
}

function get_footer() {
	if(file_exists(theme().'/footer.php'))
		require(theme().'/footer.php');
	else echo '<p>Error: footer.php file does not exist in the theme folder!</p>';
}

function get_comments() {
	if(file_exists(theme().'/comments.php'))
		require(theme().'/comments.php');
	else echo '<p>Error: comments.php file does not exist in the them folder!</p>';
}

function get_theme() {
	return rtrim(THEMES, '/') . '/' . THEME;
}

function theme() {
	return rtrim(THEMES, '/') . '/' . THEME;
#	echo get_theme();
}

function get_theme_link() {
	return rtrim(THEMES_URL, '/') . '/' . THEME;
}

function theme_link() {
	echo get_theme_link();
}

function not_found() {
	if(file_exists(theme().'/404.php')) {
		header('HTTP/1.0 404 Not Found');
		require(theme().'/404.php');
	}
	else echo '<p>Oops? Page not found</p>';
	exit;
}

function kill_session() {
	@session_destroy();
	@session_unset();
}

function nicename($text) {
	$nice = strtolower( trim($text) );
	$nice = preg_replace('/\s\s+/', '-', $nice);
	$nice = preg_replace('/\s/', '-', $nice);
	$nice = preg_replace('/![a-zA-Z0-9_-]/', '', $nice);
	if(strlen($nice) > 20) $nice = substr($nice, 0, 20);
	return $nice;
}

function rm_recursive($filepath) {
    if(is_dir($filepath) && !is_link($filepath)) {
        if($dh = opendir($filepath)) {
            while(($sf = readdir($dh)) !== false) {
                if($sf == '.' || $sf == '..') {
                    continue;
                }
                if(!rm_recursive($filepath.'/'.$sf)) {
                    throw new Exception($filepath.'/'.$sf.' could not be deleted.');
                }
            }
            closedir($dh);
        }
        return @rmdir($filepath);
    }
    return @unlink($filepath);
}

function cache_url() {
	$url = parse_url($_SERVER['REQUEST_URI']);
	$ret = $url['path'];
	if(isset($url['query'])) $ret .= '?' . $url['query'];
	return $ret;
}

function cache_page($length=0) {
	if(CACHE_PAGE && empty($_POST)) {
		$length = (int) ($length <= 0) ? CACHE_LENGTH : $length;
		$file = ROOT . 'cache/pages/' . md5( cache_url() ) . '.php';
		if((file_exists($file) && (TIME - filemtime($file)) >= $length) || !file_exists($file)) {
			$headers = headers_list();
			$content_type = '';
			foreach($headers as $header) {
				if(preg_match('/Content-Type: (.*)/i', $header, $matches)) {
					$content_type = $matches[1];
					break;
				}
			}
			$data = array('Content-Type'=>$content_type, 'content'=>ob_get_contents());
			file_put_contents($file, serialize($data));
			define('CACHED_PAGE', true);
			return $file;
		}
	}
	return false;
}

function get_cached_page($length=0) {
	if(CACHE_PAGE && empty($_POST)) {
		if(intval($length) <= 0) $length = CACHE_LENGTH;
		$file = ROOT . 'cache/pages/' . md5( cache_url() ) . '.php';
		if(file_exists($file) && (TIME - filemtime($file)) <= $length) {
			$data = unserialize(file_get_contents($file));
			$content = $data['content'];
			$content .= "\n<!-- From Cache: ".serve_time()." seconds -->";
			if($data['Content-Type'] != '') {
				header('Content-Type: '.$data['Content-Type']);
			}
		#	header('HTTP/1.1 304 Not Modified');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT');
			header('Content-Length: ' . strlen($content));
			echo $content;
			exit;
		}
	}
	return false;
}

function cache_var($filename, $var) {
	$file = ROOT . 'cache/vars/' . md5($filename) . '.php';
	file_put_contents($file, serialize($var));
	return $file;
}

function get_cached_var($filename, $length=0) {
	$length = (int) (intval($length) <= 0) ? CACHE_LENGTH : $length;
	$file = ROOT . 'cache/vars/' . md5($filename) . '.php';
	if(file_exists($file) && (TIME - filemtime($file)) <= $length) {
		return unserialize(file_get_contents($file));
	}
	return false;
}

function clear_cache($types='') {
	$allowed = array('pages', 'vars', 'sql');
	if('' == $types) {
		$types = $allowed;
	}
	elseif(is_string($types)) {
		$types = array($types);
	}
	foreach($types as $type) {
		if(!in_array($type, $allowed)) continue;
		foreach(glob(ROOT."cache/$type/*.php") as $file) {
			@unlink($file);
		}
	}
}

// checks is it's a Natural number; basically an integer > 0
function is_nat($num) {
	if(!is_numeric($num) || $num <= 0) {
		return false;
	}
	return (int) $num;
}

function link_regex() {
	global $_LINK_VARS, $_LINK_REGEX;
	return str_replace($_LINK_VARS, $_LINK_REGEX, LINK_STRUCTURE);
}

function refresh_facebook() {
	require(ROOT.'other/facebook/new-client/facebook.php');
	$api_key = '07456bccc0913897468fa45686700326';
	$secret  = 'c21bebb6c40c66ec8cdccdac073b3ac4';
	$fb = new Facebook($api_key, $secret);
	$fb->api_client->fbml_refreshRefUrl('http://thespiffylife.com/other/facebook/fbml-ref.php');
	$fb->api_client->fbml_refreshRefUrl('http://thespiffylife.com/other/facebook/fbml-ref.php?handle=wall');
}

function generate_sitemap() {
	global $scdb, $comic;
	$map = '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="http://thespiffylife.com/content/sitemap.xsl"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	foreach($comic->get_archives() as $c) {
		$map .= '
	<url>
		<loc>'.$comic->get_filtered_link(array('title'=>$c->title, 'ID'=>$c->ID, 'PID'=>$c->PID, 'cat'=>$c->cat)).'</loc>
		<lastmod>'.get_date('Y-m-d\TH:i:sP', $c->time).'</lastmod>
		<changefreq>daily</changefreq>
		<priority>0.5</priority>
	</url>';
	}
	$indexmod = 0;
	foreach($comic->categories as $category) {
		$stamp = $scdb->get_var("SELECT `time` FROM `$scdb->comics` WHERE `cat` = '".$category['ID']."' ORDER BY `PID` DESC LIMIT 1");
		if($scdb->num_rows == 0)
			continue;
		$mod = mysql_to_unix($stamp);
		if($mod > $indexmod) $indexmod = $mod;
		$map .= '
	<url>
		<loc>'.DOMAIN.$category['nicename'].'/</loc>
		<lastmod>'.date('Y-m-d\TH:i:sP', $mod).'</lastmod>
		<changefreq>daily</changefreq>
		<priority>0.7</priority>
	</url>';
	}
	$map .= '
	<url>
		<loc>'.DOMAIN.'</loc>
		<lastmod>'.date('Y-m-d\TH:i:sP', $indexmod).'</lastmod>
		<changefreq>daily</changefreq>
		<priority>0.9</priority>
	</url>
	<url>
		<loc>'.DOMAIN.'archive/</loc>
		<lastmod>'.date('Y-m-d\TH:i:sP', $indexmod).'</lastmod>
		<changefreq>daily</changefreq>
		<priority>0.9</priority>
	</url>
</urlset>';
	file_put_contents(ROOT.'sitemap.xml', $map);
	sitemap_ping();
}

function sitemap_ping() {
	$sitemap = urlencode(DOMAIN.'sitemap.xml');
	$urls = array(
			'google'=>'http://www.google.com/webmasters/sitemaps/ping?sitemap=',
			'yahoo'=>'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=sTOPJqzV34F0Ux3ja8.KQDm2AbLLH5lvFW8f4rz6gyPfPiqMQ2kYasqvhKFGEv0&url=',
			'ask'=>'http://submissions.ask.com/ping?sitemap=',
			'msn'=>'http://webmaster.live.com/ping.aspx?siteMap='
		);
	foreach($urls as $url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.$sitemap);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
		$response = curl_exec($ch);
		curl_close($ch);
		$response = preg_replace('!<script[^>]*>.*</script>!isU', '', $response);
		echo '<p class="message">',strip_tags($response),'</p><br />';
	}
}

function highlight_text($term, $text, $class = false) {
	$span = ($class === false) ? '<span>$0</span>' : '<span class="'.$class.'">$0</span>';
	return preg_replace("/$term/i", $span, $text);
}

function smalltext($term, $string, $length=150) {
	$string_len = strlen($string);
	if($length >= $string_len) {
		return $string;
	}
	$term_len = strlen($term);
	$pos = stripos($string, $term);
	$start = ($pos === false || $length > $pos + $term_len) ? 0 : $pos - floor($pos/3);
	$small = substr($string, $start, $length);
	return $small;
}

function int_user_level($level='') {
	$level = (string) ('' == $level && isset($_SESSION['level'])) ? $_SESSION['level'] : $level;
	switch($level) {
		default : // guest
			return 0;
			break;
		case 'user' :
			return 1;
			break;
		case 'comic_author' :
		case 'news_author' :
			return 2;
			break;
		case 'author' :
			return 3;
			break;
		case 'editor' :
			return 4;
			break;
		case 'admin' :
			return 5;
			break;
	}
}

function is_at_least($level) {
	if(!is_string($level) || strlen($level) === 0 || !isset($_SESSION['level'])) return false;
	return (bool) (int_user_level($_SESSION['level']) >= int_user_level($level)) ? true : false;
}

function user_is($level) {
	if(!isset($_SESSION['level']) || !is_string($_SESSION['level']) || strlen($_SESSION['level']) == 0) return false;
	return (bool) ($level == $_SESSION['level']) ? true : false;
}

?>