<?php
require('../config.php');
require(PHP_DIR.'functions.php');
require(PHP_DIR.'admin.class.php');
require(PHP_DIR.'comic.class.php');
require(PHP_DIR.'comments.class.php');

if($_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] || !isset($_SESSION['level'])) {
	$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
	exit(header('Location: login.php'));
}

if(isset($_GET['cedit']) && intval($_GET['cedit']) == $_GET['cedit']) {
	$ID = (int) $_GET['cedit'];
	if(isset($_POST['submit'])) {
		$comments->edit_comment($ID, array('name'=>$_POST['name'], 'comment'=>$_POST['comment'], 'website'=>$_POST['website'], 'time'=>$_POST['time']));
		echo '<p class="message">',$comments->message,'</p>';
		exit;
	}
	$comment = $scdb->get_row("SELECT * FROM `$scdb->comments` WHERE `ID` = '$ID' LIMIT 1");
	if($scdb->num_rows != 1) {
		die('No results for ID #'.$ID);
	}
?>

<h3>Edit Comment #<?php echo $comment->ID; ?></h3>
<form action="ajax.php?cedit=<?php echo $comment->ID; ?>" class="jNice" id="edit-form">
<fieldset>
	<p><label>Name:</label><input type="text" name="name" class="text-long" value="<?php echo $comment->name; ?>" /></p>
	<p><label>Website:</label><input type="text" name="website" class="text-long" value="<?php echo $comment->website; ?>" /></p>
	<p><label>Date-Time:</label><input type="text" name="time" class="text-long" value="<?php echo $comment->time; ?>" /></p>
	<p><label>Comment:</label><textarea name="comment"><?php echo htmlentities($comment->comment); ?></textarea></p>
	<input type="submit" name="submit" value="Edit Comment" class="button-submit" />
</fieldset>
</form>

<?php
	exit;
}

elseif(isset($_GET['email'])) {
	list($userName, $mailDomain) = explode('@', $_GET['email']);
	if(checkdnsrr($mailDomain, "MX"))
		return json_encode(array('valid'=>true));
	else
		return json_encode(array('valid'=>false));
}

?>