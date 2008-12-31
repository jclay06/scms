<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}
?>

<h3>Choose a Category</h3>
<form action="index.php" method="get" class="jNice">
<fieldset>

	<input type="hidden" name="page" value="admin" />
	<input type="hidden" name="option" value="comic-new" />
	
	<label for="cat">Category:</label>
	<select name="cat" id="cat">
<?php
	foreach($comic->categories as $cat) {
		$cat = (object) $cat;
?>
		<option value="<?php echo $cat->ID; ?>"<?php if(isset($_GET['cat']) && $cat->ID == $_GET['cat']) { ?> selected="selected"<?php } ?>><?php echo $cat->category; ?> - "<?php echo substr($cat->info, 0, 35); ?>"</option>
<?php } ?>
	</select>

	<input type="submit" value="Choose" />
	
</fieldset>
</form>

<?php
$CAT = (isset($_GET['cat']) && is_numeric($_GET['cat']) && $_GET['cat'] > 0) ? intval($_GET['cat']) : false;
if($scadmin->get_cat_info($CAT)) {

if(isset($_POST['upload'])) {
	$scadmin->upload_comic();
	echo '<p class="message">',$scadmin->upload->message,'</p>';
}

elseif(isset($_POST['add'])) {
	$scadmin->add_comic($_POST);
	echo '<p class="message">',$scadmin->comic->message,'</p>';
}
?>

<h3>Upload a Comic</h3>
<form action="index.php?page=admin&option=comic-new&cat=<?php echo $CAT; ?>" method="post" class="jNice" enctype="multipart/form-data">
<fieldset>

	<p><label for="file">Upload Comic Image (jpeg / gif / png):</label><input type="file" name="file" /></p>
	
	<input type="submit" name="upload" value="Upload" class="button-submit" />
	
</fieldset>
</form>

<style type="text/css">
#comic-preview {
	display: none;
	border: 1px solid #CCC;
	position: relative;
	width:320px;
	height:310px;
	margin-top: -360px;
	margin-left: 350px;
	margin-bottom: 35px;
	padding: 0px;
	text-align: center;
}
#comic-preview img {
	max-width: 200px;
	max-height: 200px;
}
</style>

<script type="text/javascript">
function comic_preview() {
	var title = document.getElementById('title').value;
	var info = document.getElementById('info').value;
	var date = document.getElementById('date').value;
	var time = document.getElementById('time').value;
	var image = document.getElementById('image').options[document.getElementById('image').selectedIndex].value;
	document.getElementById('comic-preview').innerHTML = '<h3>'+title+'</h3><br /><img src="<?php echo IMAGES, $scadmin->category->nicename; ?>/'+image+'" alt="oops?" title="'+info+'" />';
	document.getElementById('comic-preview').style.display = "block";
}
</script>

<h3>Add a New Comic</h3>
<form action="index.php?page=admin&option=comic-new&cat=<?php echo $CAT; ?>" method="post" class="jNice" name="new_comic" id="new_comic">
<fieldset>

	<p><label for="title">Comic Title :</label><input type="text" name="title" id="title" class="text-long" onblur="comic_preview()" /></p>
	
	<p><label for="info">Info <span>(Alt Text)</span> :</label><input type="text" name="info" id="info" class="text-long" onblur="comic_preview()" /></p>
	
	<p>
		<label for="image">Image :</label>
		<select name="image" id="image" onchange="comic_preview()">
<?php
	$files = glob(IMG_FOLDER.$scadmin->category->nicename."/*.*");
	usort($files, 'sort_by_mtime');
	foreach($files as $file) { 
		if(is_image($file)) { 
			// consider only showing images that aren't being used (ones that aren't in the DB yet)
			?>
			<option value="<?php echo basename($file); ?>"><?php echo basename($file); ?></option>
<?php } } ?>
		</select>
	</p>
	
	<p><label for="date">Date :</label><input type="text" name="date" id="date" value="<?php echo substr(NOW, 0, 10); ?>" class="text-medium" onblur="comic_preview()" /></p>
		
	<p><label for="time">Time :</label><input type="text" name="time" id="time" value="<?php echo substr(NOW, 11); ?>" class="text-small" onblur="comic_preview()" /></p>
	
	<p>
		<label for="author">Author :</label>
		<select name="author" id="author">
<?php foreach($scdb->get_results("SELECT * FROM `$scdb->users` ORDER BY `ID` ASC") as $author) { ?>
			<option value="<?php echo $author->ID; ?>" <?php if($author->ID == $_SESSION['uid']) echo 'selected="selected"'; ?>><?php echo $author->nicename; ?></option>
<?php } ?>
		</select>
	</p>
	
	<button href="#comic-preview" onclick="comic_preview();return false;" class="button-submit">Preview</button>	
	<input type="submit" name="add" value="Add Comic" class="button-submit" />

</fieldset>
</form>

<div id="comic-preview"></div>


<?php } ?>