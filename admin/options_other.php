<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}

if(isset($_POST['nocache'])) {
	clear_cache();
	echo '<p class="message">All cached files <em>should</em> be deleted now (comic pages, archives page, and saved comments, etc)</p>';
}

elseif(isset($_POST['links'])) {
	$conf = file_get_contents(ROOT.'options.php');
	$link_structure = trim( str_replace(array('"', "'"), '', $_POST['link_structure']) );
	$conf = preg_replace("/define\('LINK_STRUCTURE', '(.+)'\);/", "define('LINK_STRUCTURE', '".$link_structure."');", $conf, 1);
	file_put_contents(ROOT.'options.php', $conf);
	exit(header('Location: '.$_SERVER['REQUEST_URI']));
}

elseif(isset($_POST['sitemap'])) {
	generate_sitemap();
}

?>

<style type="text/css">
.link_domain {
	float:left;
	margin:7px 5px 0 10px;
	font-size:1.3em;
}
</style>

<h3>Other Random Things</h3>

<fieldset>
<form action="/admin/index.php?page=options&option=other" method="post" class="jNice">
	<p><label>Regenerate Sitemap?</label><input type="submit" name="sitemap" value="Yes!" /></p>
</form>
</fieldset>

<fieldset>
<form action="/admin/index.php?page=options&option=other" method="post" class="jNice">
	<p>
		<label>Link Structure :</label>
		<span class="link_domain"><?php echo rtrim(DOMAIN, '/'); ?></span>
		<input type="text" name="link_structure" value="<?php echo LINK_STRUCTURE; ?>" class="text-long" style="font-size:1.2em;" />
	</p>
	<p><input type="submit" name="links" value="Submit" /></p>
</form>
</fieldset>

<fieldset>
<form action="/admin/index.php?page=options&option=other" method="post" class="jNice">
	<p><label>Clear cache?</label><input type="submit" name="nocache" value="Yes!" /></p>
</form>
</fieldset>