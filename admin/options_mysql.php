<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}

if(isset($_POST['submit'])) {
	$conf = file_get_contents('../config.php');
	
	$host = safe_text($_POST['host']);
	$user = safe_text($_POST['user']);
	$pass = safe_text($_POST['pass']);
	$name = safe_text($_POST['name']);
	$pref = safe_text($_POST['prefix']);

	$conf = preg_replace("/define\('DB_HOST', '(.+)?'\);/", "define('DB_HOST', '".$host."');", $conf, 1);
	
	$conf = preg_replace("/define\('DB_USER', '(.+)?'\);/", "define('DB_USER', '".$user."');", $conf, 1);
	
	if(isset($_POST['change'])) {
		if($_POST['pass'] != $_POST['pass2']) {
			echo "<p class=\"message\">You're passwords do not match!</p>";
		}
		else {
			$conf = preg_replace("/define\('DB_PASS', '(.+)?'\);/", "define('DB_PASS', '".$_POST['pass']."');", $conf, 1);
		}
	}
	
	$conf = preg_replace("/define\('DB_NAME', '(.+)?'\);/", "define('DB_NAME', '".$name."');", $conf, 1);
	
	$conf = preg_replace("/define\('DB_TABLE_PREFIX', '(.+)?'\);/", "define('DB_NAME', '".$pref."');", $conf, 1);

	
	echo '<pre>', strtr($conf, array('<?php'=>'&lt;php','?>'=>'?&gt;')) ,'</pre>';
}

?>

<h3>Setup Options</h3>
<form action="index.php?page=options&option=mysql" method="post" class="jNice">
<fieldset>

	<p><label for="host">Host:</label><input type="text" name="host" class="text-long" value="<?php echo DB_HOST; ?>" /></p>
	
	<p><label for="user">User:</label><input type="text" name="user" class="text-long" value="<?php echo DB_USER; ?>" /></p>
	
	<p><label for="change">Change Password ?</label><input type="checkbox" name="change" value="1" /></p>
	<p><label for="pass">New Password:</label><input type="password" name="pass" class="text-long" /></p>
	<p><label for="pass2">Verify Password:</label><input type="password" name="pass2" class="text-long" /></p>
	
	<p><label for="name">Database:</label><input type="text" name="name" class="text-long" value="<?php echo DB_NAME; ?>" /></p>
	
	<p><label for="prefix">Database Table Prefix:</label><input type="text" name="prefix" class="text-long" value="<?php echo DB_TABLE_PREFIX; ?>" /></p>
	
	<input type="submit" name="submit" value="Submit" class="button-submit" />

</fieldset>
</form>
