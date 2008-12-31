<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}

if(isset($_POST['add'])) {
	$newcat = array('category'=>$_POST['category'], 'nicename'=>$_POST['nicename'], 'info'=>$_POST['info'], 'default'=>(isset($_POST['default']))?$_POST['default']:0);
	$scadmin->add_category($newcat);
}
?>

<?php if(isset($scadmin->category->message)) echo '<p class="message">',$scadmin->category->message,'</p>'; ?>

<script type="text/javascript" language="Javascript">
window.addEvent('domready', function() {
	$('category').addEvent('keyup', function() {
		var cat = $('category');
		var nn = $('nicename');
		if(cat.value.length > 0) {
			var nice = cat.value.clean();
			nice = nice.toLowerCase();
			nice = nice.replace(/\s/g, '-');
			nice = nice.replace(/![A-Za-z0-9_-]/g, '');
			if(nice.length > 20) {
				nice = nice.substring(0,20);
			}
			nn.value = nice;
		}
	});
});
</script>

<h3>Add a New Category</h3>
<form action="index.php?page=admin&option=cat-new" method="post" class="jNice" name="new_cat" id="new_cat">
<fieldset>

	<p><label for="category">Category :</label><input type="text" name="category" id="category" class="text-long" value="" maxlength="50" /></p>
	
	<p><label for="nicename">Category Nice-Name :</label><input type="text" name="nicename" id="nicename" value="" class="text-medium" maxlength="15" /></p>
	
	<p><label for="info">Category Information :</label><input type="text" name="info" id="info" class="text-long" value="" maxlength="200" /></p>
	
	<p><label for="default">Make Default Category :</label><input type="checkbox" name="default" id="default" value="1" /></p>

	<input type="submit" name="add" value="Add Category" class="button-submit" />

</fieldset>
</form>
