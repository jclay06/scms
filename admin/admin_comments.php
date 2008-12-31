<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}

if(isset($_GET['del'])) {
	$ID = (int) $_GET['del'];
	$comments->del_comment($ID);
	echo '<p class="message">',$comments->message,'</p>';
}

if(isset($_GET['edit']) && intval($_GET['edit']) == $_GET['edit']) {
	$ID = (int) $_GET['edit'];
	$comment = $scdb->get_row("SELECT * FROM `$scdb->comments` WHERE `ID` = '$ID' LIMIT 1");
	if($scdb->num_rows != 1) {
		echo '<p class="message">Can\'t locate a comment with that ID</p>';
	}
	elseif(isset($_POST['submit'])) {
		$edit = array('name'=>$_POST['name'], 'comment'=>$_POST['comment'], 'website'=>$_POST['website'], 'time'=>$_POST['time']);
		$comments->edit_comment($ID, $edit);
		echo '<p class="message">',$comments->message,'</p>';
	}
	else {
?>

<div id="edit">
<h3>Edit Comment #<?php echo $ID; ?></h3>
<form action="index.php?page=admin&option=comments&edit=<?php echo $ID; ?>" method="post" class="jNice" id="edit-form">
<fieldset>
	<p><label>Name:</label><input type="text" name="name" class="text-long" value="<?php echo $comment->name; ?>" /></p>
	<p><label>Website:</label><input type="text" name="website" class="text-long" value="<?php echo $comment->website; ?>" /></p>
	<p><label>Date-Time:</label><input type="text" name="time" class="text-long" value="<?php echo $comment->time; ?>" /></p>
	<p><label>Comment:</label><textarea name="comment"><?php echo htmlentities($comment->comment); ?></textarea></p>
	<input type="submit" name="submit" value="Edit Comment" />
</fieldset>
</form>
</div>

<?php
	}
}
else { ?>

<div id="edit"></div>

<?php }

$page = isset($_GET['p']) ? $_GET['p'] : 1;
$num = isset($_GET['num']) ? intval($_GET['num']) : 10;
?>

<style type="text/css">
.comm_name {
	color: #000;
	font-weight: bold;
}
.comm_name span {
	margin-left: 3px;
	font-size: 0.9em;
	color: gray;
	font-weight: normal;
}
.comm_name a, .comm_name a:visited {
	color: gray;
	text-decoration: underline;
	font-style: italic;
	margin-right: 3px;
}
.comm_name a:hover {
	text-decoration: underline;
}
.comm_text {
	width: 90%;
	margin: 0 auto;
}
</style>

<script type="text/javascript">
window.addEvent('domready', function() {

	var myFx = new Fx.Scroll(document.body);
	
//	var hide = new Fx.Slide($('edit'), {
//		duration: 500,
//		transition: Fx.Transitions.linear
//	});

	$$('a.edit').addEvent('click', function(e) {
		e.stop();
		var req = new Request.HTML({
			url:'ajax.php?cedit='+this.parentNode.id,
			onSuccess: function(html) {
		//		hide.toggle();
				$('edit').empty().adopt(html);
		//		hide.toggle();
				$('edit-form').addEvent('submit', function(e) {
					e.stop();
					this.set('send', {
						onSuccess: function(html) {
							$('edit').empty().set('html', html);
							myFx.toElement('edit');
						},
						onFailure: function() {
							$('edit').empty().set('html', '<p class="message">Failed to submit form!</p>');
						}
					});
					this.send();
				});
				myFx.toElement('edit');
			},
			onFailure: function() {
				$('edit').set('html', '<p class="message">The request failed.</p>');
			}
		}).send();
	});
	
	$$('a.delete').addEvent('click', function(e) {
		e.stop();
		var ask = confirm('are you sure you wanna do that?');
		if(ask == false) {return false;}
		document.location = 'index.php?page=admin&option=comments&p=<?php echo $page; ?>&del='+this.parentNode.id;
	});

});
</script>

<form action="" class="jNice">
<h3>Recent Comments</h3>

<?php 
$alt = 'odd';
if($comments->get_archives($num, $page) !== false) { ?>

<div style="text-align:center;">Showing comments <?php echo $comments->archives->start; ?> - <?php echo $comments->archives->end; ?> of <?php echo $comments->total_comments(); ?></div><br />

<table cellpadding="0" cellspacing="0">

<?php foreach($comments->get_archives($num, $page) as $comment) { ?>
	
	<tr class="<?php echo $alt; ?>">
		<td>
			<div class="comm_name"><?php echo $comment->name; ?> <span>on: <a href="<?php echo $comment->url; ?>"><?php echo $comment->title; ?></a> <?php echo get_date('m/d/Y \a\t g:i a', $comment->time); ?></span></div>
			<div class="comm_text"><?php echo replace_smilies($comment->comment); ?></div>
		</td>
		<td class="action" id="<?php echo $comment->ID; ?>"><a href="<?php echo $comment->url; ?>" class="view">View</a><a href="index.php?page=admin&option=comments&edit=<?php echo $comment->ID; ?>" class="edit">Edit</a><a href="#" class="delete">Delete</a></td>
	</tr>
	
<?php
	$alt = ($alt == 'odd') ? '' : 'odd';
}
?>

</table>

<?php
$pages = ceil($comments->total_comments() / $num);
?>

	<div id="comment_nav">
		<?php if($page > 3) { ?><a href="index.php?page=admin&option=comments&p=1">1</a> ... <?php } ?>
		<?php if($page > 2) { ?><a href="index.php?page=admin&option=comments&p=<?php echo $page - 2; ?>"><?php echo $page - 2; ?></a><?php } ?>
		<?php if($page > 1) { ?><a href="index.php?page=admin&option=comments&p=<?php echo $page - 1; ?>"><?php echo $page - 1; ?></a><?php } ?>
		<span class="current"><?php echo $page; ?></span>
		<?php if($pages > $page) { ?><a href="index.php?page=admin&option=comments&p=<?php echo $page + 1; ?>"><?php echo $page + 1; ?></a><?php } ?>
		<?php if($pages > ($page + 1)) { ?><a href="index.php?page=admin&option=comments&p=<?php echo $page + 2; ?>"><?php echo $page + 2;?></a><?php } ?>
		<?php if($pages > ($page + 2)) { ?> ... <a href="index.php?page=admin&option=comments&p=<?php echo $pages; ?>"><?php echo $pages; ?></a><?php } ?>
	</div>

<?php
}
else {
	echo '<p style="text-align:center;margin:10px;">No Comments</p>';
}
?>

</fieldset>
</form>

<br />
