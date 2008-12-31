<?php

error_reporting(E_ALL);

ob_start('ob_gzhandler');
session_start();

define('DOMAIN', 'http://localhost/');
define('ROOT', realpath(dirname(__FILE__)).'/');

define('IMAGES',  'http://img.thespiffylife.com/');
define('IMG_FOLDER',  '/home/joscla/img.thespiffylife.com/');

define('THEMES_URL', DOMAIN . 'content/themes/');
define('THEMES', ROOT . 'content/themes/');

define('PHP_DIR', ROOT . 'php/');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'comics_dev');
define('DB_TABLE_PREFIX', '');

define('SECOND', 1);
define('MINUTE', SECOND * 60);
define('HOUR', MINUTE * 60);
define('DAY', HOUR * 24);
define('WEEK', DAY * 7);
define('MONTH', DAY * 30);
define('YEAR', DAY * 365);

require(ROOT . 'options.php');

define('TIME', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
define('NOW', date('Y-m-d H:i:s', TIME));
define('START', microtime(true));

require(PHP_DIR . 'db.class.php');
$scdb->set_tables(array('categories', 'comics', 'comments', 'news', 'users'));
$scdb->cache_dir = ROOT.'cache/sql/';
$scdb->use_disk_cache = true;
$scdb->cache_queries = true;
$scdb->cache_timeout = 24;

$_LINK_VARS = array('%id%', '%pid%', '%title%', '%cat%');
$_LINK_REGEX = array('(\d+)', '(\d+)', '([A-Za-z0-9_-]+)', '([A-Za-z0-9_-]+)');

?>