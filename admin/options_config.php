<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}

if(isset($_POST['submit'])) {
	$conf = file_get_contents('../config.php');
	
	$name		= safe_text($_POST['name']);
	$domain		= safe_text($_POST['domain']);
	$root		= safe_text($_POST['doc_root']);
	$images		= safe_text($_POST['images']);
	$img_folder	= safe_text($_POST['img_folder']);
	$themes_url	= safe_text($_POST['themes_url']);
	$theme		= safe_text($_POST['theme']);
	
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
<form action="index.php?page=options&option=locations" method="post" class="jNice">
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
