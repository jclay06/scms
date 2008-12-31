<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}

if(isset($_GET['id']) && $ID = is_nat($_GET['id']) && $cat = get_category($ID)) {

	$category = $category->category;
	$nicename = $category->nicename;

	if(isset($_POST['del']) && isset($_POST['option'])) {
		echo '<br /><fieldset>';
		$message = '';
		switch($_POST['option']) {
			default :
				exit(header('Location: index.php?page=admin&option=cat-edit&id='.$ID));
				break;
			case 'cat' :
				$scdb->query("DELETE FROM `$scdb->categories` WHERE `ID` = '$ID' LIMIT 1");
				$message = '<p class="message">Removed <strong>&ldquo;'.$category.'&rdquo;</strong> from the categories.</p>';
				break;
			case 'db' :
				$scdb->query("DELETE FROM `$scdb->categories` WHERE `ID` = '$ID' LIMIT 1");
				$scdb->query("DELETE FROM `$scdb->comics` WHERE `cat` = '$ID'");
				$scdb->query("DELETE FROM `$scdb->comments` WHERE `cat` = '$ID'");
				$message = '<p class="message">Removed anything connected to <strong>&ldquo;'.$category.'&rdquo;</strong> from the database.</p>';
				break;
			case 'all' :
				$scdb->query("DELETE FROM `$scdb->categories` WHERE `ID` = '$ID' LIMIT 1");
				$scdb->query("DELETE FROM `$scdb->comics` WHERE `cat` = '$ID'");
				$scdb->query("DELETE FROM `$scdb->comments` WHERE `cat` = '$ID'");
				$message = '<p class="message">Removed anything connected to <strong>&ldquo;'.$category.'&rdquo;</strong> from the database and deleted the image files.</p>';
				rm_recursive(IMG_FOLDER.$nicename.'/');
				break;
		}
		if(mysql_errno()) echo mysql_error();
		else echo $message;
		echo '</fieldset>';
	}
	else {
?>

<h3>Confirm Category Deletion</h3>
<form action="index.php?page=admin&option=cat-del&id=<?php echo $ID; ?>" method="post" class="jNice">
<fieldset>

	<p>Are you sure you want to remove the category <strong>&ldquo;<?php echo $category; ?>&rdquo;</strong> along with all the entries in the database and the image files?</p>
	
	<p>
		<p><input type="radio" name="option" value="cat" checked="checked" /> No, Just the Category itself.</p>
		<p><input type="radio" name="option" value="db" /> No, Just the Category and Linked Comics & Comments (keep the image files)</p>
		<p><input type="radio" name="option" value="all" /> Yes, Remove Everything.</p>
	</p>

	<p><a href="index.php?page=admin&option=cat-edit&id=<?php echo $ID; ?>" title="">I changed my mind!</a></p>
	
	<p><input type="submit" name="del" value="Delete Category" class="button-submit" /></p>

</fieldset>
</form>

<?php 	
	}
}

else {
	header('Location: index.php?page=admin&option=cat-edit');
	exit;
}

?>