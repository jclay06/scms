<?php 
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}
?>

<h3>Choose the Default Category</h3>
<form action="index.php?page=admin&option=cat-edit" method="post" class="jNice">
<?php
if(isset($_POST['change_default'])) {
	$default = (int) $_POST['default'];
	$scadmin->set_default_category($default);
}
?>
<fieldset>
	<select name='default' id="default">
<?php
foreach($scdb->get_results("SELECT * FROM `$scdb->categories` ORDER BY `ID` ASC", ARRAY_A) as $row) {
	echo "\t\t<option value='" , $row['ID'] , "'";
	if($row['default'] == 1) echo " selected='selected'";
	echo ">" , $row['category'] , "</option>\n";
}
?>
	</select>
	<input type="submit" name="change_default" value="Update Default" /></p>
</fieldset>
</form>

<h3>Or, Select a Category</h3>
<form action="index.php" method="get" class="jNice" onsubmit="if(document.getElementById('edit_list').selectedIndex == 0){return false;}">
<fieldset>
	<input type="hidden" name="page" value="admin" />
	<input type="hidden" name="option" value="cat-edit" />
	<select name='id' id="edit_list">
		<option value='0' class='center'>Choose from the categories...</option>
<?php
foreach($scdb->get_results("SELECT category, ID FROM `$scdb->categories` ORDER BY `ID` ASC", ARRAY_A) as $row) {
	echo "\t\t<option value='" , $row['ID'] , "'";
	if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] == $row['ID']) echo " selected='selected'";
	echo ">" , $row['category'] , "</option>\n";
}
?>
	</select>
	<input type="submit" value="Select" /></p>
</fieldset>
</form>

<?php
if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
	$ID = (int) $_GET['id'];
	
	if(isset($_POST['edit'])) {
		$edit = array('category'=>$_POST['category'], 'nicename'=>$_POST['nicename'], 'info'=>$_POST['info']);
		$scadmin->edit_category($ID, $edit);
	}
	
	$info = $scdb->get_row("SELECT * FROM `$scdb->categories` WHERE `ID` = '$ID' LIMIT 1", ARRAY_A);
	if($scdb->num_rows == 1) {
?>

<script type="text/javascript" language="Javascript">
var d = document;
function nn() {
	var cat = d.getElementById('category');
	var nn = d.getElementById('nicename');
	if(cat.value.length > 0) {
		var nice = cat.value;
		nice = nice.toLowerCase();
		nice = nice.replace(/\s{2,}/, '_');
		nice = nice.replace(/\s/g, '_');
		nice = nice.replace(/\W/g, '');
		if(nice.length > 20) {
			nice = nice.substring(0,20);
		}
		nn.value = nice;
	}
}
</script>

<h3>Edit Category Info</h3>
<form action="index.php?page=admin&option=cat-edit&id=<?php echo $ID; ?>" method="post" class="jNice">
<fieldset>

	<p><label for="category">Category :</label><input type="text" name="category" id="category" value="<?php echo html_entity_decode($info['category']); ?>" maxlength="50" class="text-long" onkeyup="nn()" /></p>

	<p><label for="nicename">Category Nice-Name :</label><input type="text" name="nicename" id="nicename" value="<?php echo html_entity_decode($info['nicename']); ?>" maxlength="20" class="text-medium" /></p>
	
	<p><label for="info">Information :</label><input type="text" name="info" id="info" value="<?php echo $info['info']; ?>" maxlength="200" class="text-long" /></p>

	<p><input type="submit" name="edit" value="Edit Category" class="button-submit" /></p>
	
	<p><a href="index.php?page=admin&option=cat-del&id=<?php echo $ID; ?>" class="button-submit">Delete Category?</a></p>

</fieldset>
</form>
		
<?php
	}
}
?>