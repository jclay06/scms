<?php
if('comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) die('Please do not load this page directly. Thanks!');

$comments->get_comments($comic->ID);
?>
<div style="width:400px;margin:0 auto;text-align:left;">

<?php
echo $comments->message;

if($comments->num > 0) foreach($comments->comments() as $comment) {
?>

<p style="border:1px solid #000;" id="comment-<?php echo $comment->ID; ?>"><strong><?php echo $comment->name; ?></strong> on <a href="#comment-<?php echo $comment->ID; ?>" title="Permalink to Comment"><?php echo get_date('F jS, Y @ g:i a', $comment->time); ?></a><br /><span style="padding:5px 15px 10px 15px;"><?php echo $comment->comment; ?></span></p>

<?php } ?>

<br /><br />

<form action="#comments" method="post" name="commentform" id="commentform">

<input type="text" name="name" id="name" maxlength="50" value="<?php
	if(isset($_COOKIE['name'])) echo $_COOKIE['name'];
	elseif(isset($_POST['name'])) echo $_POST['name'];
?>"/> <label for="name">Name</label><br />

<textarea rows="12" cols="45" name="comment" id="comment"><?php
	if(isset($_POST['submit']) && $comments->error) echo $_POST['comment'];
?></textarea><br /><br />

<?php if(REQ_CAPTCHA) { ?>
<input id="security_code" name="security_code" type="text" maxlength="4"/>&nbsp;<img src="/php/captcha.php" style="vertical-align:middle;" alt=""/><br />
<?php } ?>

<input type="hidden" name="comicID" value="<?php echo $comic->ID; ?>" />

<input type="submit" name="submit" id="submit" value="Submit Comment" />

</form>

<br /><br />

</div>
