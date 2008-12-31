<?php 
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}
?>

<h3>Choose a Category</h3>
<form action="index.php" method="get" class="jNice">
<fieldset>

	<input type="hidden" name="page" value="admin" />
	<input type="hidden" name="option" value="comic-edit" />
	
	<label for="cat">Category:</label>
	<select name="cat" id="cat">
<?php
	foreach($scdb->get_results("SELECT category, ID, info FROM `$scdb->categories` ORDER BY `ID` ASC") as $cat) {
?>
		<option value="<?php echo $cat->ID; ?>"<?php if(isset($_GET['cat']) && $cat->ID == $_GET['cat']) { ?> selected="selected"<?php } ?>><?php echo $cat->category; ?> - "<?php echo substr($cat->info, 0, 35); ?>"</option>
<?php } ?>
	</select>
			
	<input type="submit" value="Choose" />
	
</fieldset>
</form>

<?php
$CAT = (isset($_GET['cat']) && is_numeric($_GET['cat']) && $_GET['cat'] > 0) ? intval($_GET['cat']) : false;
if($CAT !== false && $scadmin->get_cat_info($CAT)) {
?>

<h3>Select a <?php echo $scadmin->category->category; ?></h3>
<form action="index.php" method="get" class="jNice" onsubmit="if(document.getElementById('edit_list').selectedIndex == 0){return false;}">
<fieldset>
	<input type="hidden" name="page" value="admin" />
	<input type="hidden" name="option" value="comic-edit" />
	<input type="hidden" name="cat" value="<?php echo $scadmin->category->ID; ?>" />
	
	<select name='id' id="edit_list">
		<option value='0' class='center'>Choose from the comics...</option>
<?php
foreach($scdb->get_results("SELECT title, image, unix_timestamp(time) as time, PID FROM `$scdb->comics` WHERE `cat` = '".$scadmin->category->ID."' ORDER BY `PID` DESC") as $row) {
	echo "\t\t<option value='" , $row->PID , "'";
	if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] == $row->PID) echo " selected='selected'";
	echo ">" , date("m/d/Y", $row->time) , " - " , $row->title , "</option>\n";
}
?>
	</select>
	<input type="submit" value="Select" />
</fieldset>
</form>

<?php
if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
	$PID = (int) $_GET['id'];
	
	if(isset($_POST['edit'])) {
		$scadmin->edit_comic($PID);
	}
	
	$comic->get_comic_by_permalink($PID);
	
?>
		
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

<h3>Edit Comic Info</h3>
<form action="index.php?page=admin&option=comic-edit&cat=<?php echo $scadmin->category->ID; ?>&id=<?php echo $comic->PID; ?>" method="post" class="jNice">
<fieldset>

	<p><label for="title">Comic Title :</label><input type="text" name="title" id="title" value="<?php echo html_entity_decode($comic->title); ?>" maxlength="100" class="text-long" onblur="comic_preview()" /></p>

	<p><label for="info">Info <span>(Alt Text)</span> :</label><input type="text" name="info" id="info" value="<?php echo html_entity_decode($comic->info); ?>" maxlength="250" class="text-long" onblur="comic_preview()" /></p>

	<p>
		<label for="image">Image :</label>
		<select name="image" id="image" onchange="comic_preview()">
<?php
	$files = glob(IMG_FOLDER.$comic->type."/*.*");
	usort($files, 'sort_by_mtime');
	foreach($files as $file) { 
		if(is_image($file)) { 
			$file = basename($file); ?>
			<option value="<?php echo $file; ?>" <?php if($comic->image == $file) echo 'selected="selected"'; ?>><?php echo $file; ?></option>
<?php } } ?>
		</select>
	</p>
	
	<p><label for="date">Date <abbr title="(YYYY-MM-DD)">?</abbr> :</label><input type="text" name="date" id="date" value="<?php echo substr($comic->time, 0, 10); ?>" maxlength="10" class="text-medium" onblur="comic_preview()" /></p>

	<p><label for="time">Time <abbr title="(HH:MM:SS)">?</abbr> :</label><input type="text" name="time" id="time" value="<?php echo substr($comic->time, 11); ?>" maxlength="8" class="text-small" onblur="comic_preview()" /></p>
	
	<button href="#comic-preview" onclick="comic_preview();return false" class="button-submit">Preview</button>
	<input type="submit" name="edit" value="Edit Comic" class="button-submit" />

</fieldset>
</form>

<div id="comic-preview"><img src="<?php echo IMAGES, $comic->type, '/', $comic->info; ?>" alt="you should fix the image link!" style="max-width:300px;" class="comic" /></div>

<script type="text/javascript">comic_preview();</script>
		
<?php
	}
}
?>