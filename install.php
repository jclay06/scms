<?php
require('config.php');

$error = '';

if(!isset($_GET['step']) || intval($_GET['step']) < 1) $_GET['step'] = 1;
if(!isset($_SESSION['step']) || intval($_SESSION['step']) < 1) $_SESSION['step'] = 1;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SCMS Installation</title>

<link href="admin/style/css/transdmin.css?<?php echo filemtime(ROOT.'admin/style/css/transdmin.css'); ?>" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" media="screen" href="admin/style/css/ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="admin/style/css/ie7.css" /><![endif]-->

<script type="text/javascript" src="admin/style/js/jquery.min.js?<?php echo filemtime(ROOT.'admin/style/js/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="admin/style/js/jNice.min.js?<?php echo filemtime(ROOT.'admin/style/js/jNice.min.js'); ?>"></script>
</head>

<body>
	<div id="wrapper">
    	<!-- h1 tag stays for the logo, you can use the a tag for linking the index page -->
    	<h1><a href="index.php"><span>Comic Admin</span></a></h1>

    	<span id="returnto">Return to: <a href="<?php echo DOMAIN; ?>" title="">&ldquo;<?php echo SITE_NAME; ?>&rdquo;</a></span>

        <div id="containerHolder">
			<div id="container">
        		<div id="sidebar">
                	<ul class="sideNav">
                    	<?php if(isset($side_menu)) echo $side_menu; ?>
                    </ul>
                    <!-- // .sideNav -->
                </div>    
                <!-- // #sidebar -->

                <!-- h2 stays for breadcrumbs -->
                <h2><a href="#">SCMS Setup</a> &raquo; <a href="#" class="active">Step <?php echo $_GET['step']; ?></a></h2>

				<div id="main">

<?php
switch(intval($_GET['step'])) {

	// check versions / functions
	default :
	
		$extensions = get_loaded_extensions();
	
		if(!version_compare(PHP_VERSION, '5.1.0', '>='))
			$error .= '<p><strong>Error:</strong> You&#8217;re PHP version ('.PHP_VERSION.') must be at least 5.1.0</p>';
		
		if(!in_array('mysql', $extensions) && !in_array('mysqli', $extensions))
			$error .= '<p><strong>Error:</strong> SCMS requires you have MySQL installed!</p>';
		
		if(!function_exists('mysql_real_escape_string') && !function_exists('addcslashes'))
			$error .= '<p><strong>Error:</strong> SCMS requires you to have access to either mysql_real_escape_string() or addcslashes() for escaping functions!</p>';
		
		if(!in_array('curl', $extensions) && !function_exists('curl_init'))
			$error .= '<p><strong>Warning:</strong> some of the functions used by SCMS need the curl library to work. (this is not necessary, but it reduces some functionality)</p>';
			
		if(!in_array('zlib', $extensions))
			$error .= '<p><strong>Warning:</strong> having zlib / gzip available will make SCMS faster!</p>';

		if('' == $error) { 
			$_SESSION['step'] = 2;
		?>
		
	<p>Everything seems to be okay with your server setup!</p>
	<p><a href="install.php?step=2" title="continue the installation">Next Step</a></p>
	
		<?php }
	
		break;
		
	// grab inital config info
	case 2 :
		
		if(!isset($_SESSION['step']) || $_SESSION['step'] != 2);


if(isset($_POST['submit'])) {
	$conf = file_get_contents('../config.php');
	
	$name = safe_text($_POST['name']);
	$domain = safe_text($_POST['domain']);
	$root = safe_text($_POST['doc_root']);
	$images = safe_text($_POST['images']);
	$img_folder = safe_text($_POST['img_folder']);
	$themes_url = safe_text($_POST['themes_url']);
	$theme = safe_text($_POST['theme']);
	
	$conf = preg_replace("/define\('SITE_NAME', '(.+)'\);/", "define('SITE_NAME', '".$name."');", $conf, 1);
	$conf = preg_replace("/define\('DOMAIN', '(.+)'\);/", "define('DOMAIN', '".$domain."');", $conf, 1);
	$conf = preg_replace("/define\('ROOT', '(.+)'\);/", "define('ROOT', '".$root."');", $conf, 1);
	$conf = preg_replace("/define\('IMAGES', '(.+)'\);/", "define('IMAGES', '".$images."');", $conf, 1);
	$conf = preg_replace("/define\('IMG_FOLDER', '(.+)'\);/", "define('IMG_FOLDER', '".$img_folder."');", $conf, 1);
	$conf = preg_replace("/define\('THEMES_URL', '(.+)'\);/", "define('THEMES_URL', '".$themes_url."');", $conf, 1);
	$conf = preg_replace("/define\('THEME', '(.+)'\);/", "define('THEME', '".$theme."');", $conf, 1);
	
	file_put_contents('../config.php', $conf);
	exit(header('Location: '.$_SERVER['REQUEST_URI']));
}

?>

<h3>Setup Options</h3>
<form action="install.php?step=2" method="post" class="jNice">
<fieldset>

	<p><label for="name">Site Name:</label><input type="text" name="name" class="text-long" value="<?php echo SITE_NAME; ?>" /></p>

	<p><label for="domain">Website:</label><input type="text" name="domain" class="text-long" value="<?php echo DOMAIN; ?>" /></p>
	
	<p><label for="doc_root">Directory:</label><input type="text" name="doc_root" class="text-long" value="<?php echo ROOT; ?>" /></p>
	
	<p><label for="images">Images URL:</label><input type="text" name="images" class="text-long" value="<?php echo IMAGES; ?>" /></p>
	
	<p><label for="img_folder">Images Folder:</label><input type="text" name="img_folder" class="text-long" value="<?php echo IMG_FOLDER; ?>" /></p>
	
	<p><label for="theme_url">Themes URL:</label><input type="text" name="themes_url" class="text-long" value="<?php echo THEMES_URL; ?>" /></p>
	
	<p>
		<label for="theme">Theme:</label>
		<select name="theme" id="theme">
		<?php
		foreach(glob(THEMES.'*', GLOB_ONLYDIR) as $folder) {
			$folder = basename($folder);
			echo '<option';
			if(THEME == $folder) echo ' selected="selected"';
			echo '>',$folder,'</option>';
		}
		?>
			<option></option>
		</select>
	</p>
	
	<input type="submit" name="submit" value="Submit" class="button-submit" />

</fieldset>
</form>

<?php		
	
		break;
		
	// create first user
	case 3 :
	
		break;
		
	// finished?
	case 4 :
	
		break;
		
}

?>

				</div>
                <!-- // #main -->
                
                <div class="clear"></div>
            </div>
            <!-- // #container -->
        </div>	
        <!-- // #containerHolder -->
        
        <p id="footer"><a href="http://thespiffylife.com/">TheSpiffyLife.com</a></p>
    </div>
    <!-- // #wrapper -->
</body>
</html>