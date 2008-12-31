<center>
<?php
if('comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) die('Please do not load this page directly. Thanks!');

global $comic, $comments;
?>

<div id='wrapper'>
<div class='commentbox'>
<a href="http://feeds.feedburner.com/thespiffylife-comments" title="grab the RSS feed for ALL of the comments"><img src="/content/img/feed-icon16x16.png" alt="" style="float:left;"/></a>
<h3 id='comments'>What People Said About This Comic ...</h3>
<?php echo '<p>',$comments->message,'</p>'; ?>
<ol id='commentlist'>

<?php
$alt = 'odd';

if($comments->num === 0) echo "<center>No one has commented on this comic yet, be the first!</center><br /><br />";

else foreach($comments->return_comments() as $comment) {

$date = get_date('F jS, Y @ g:i a', $comment->time); 

if($comment->website == '') $name = $comment->name;
else $name = "<a href='" . $comment->website . "' rel='nofollow' class='author' target='_blank'>" . $comment->name . "</a>";

$alt = ($alt == 'odd') ? 'even' : 'odd';

echo "<li id='comment-",$comment->ID,"' class='bubble'><blockquote class='",$alt,"'><p>",replace_smilies(nl2br($comment->comment)),"</p></blockquote><div class='author_line_",$alt,"'><span>",$name,"</span> on <a href='#comment-",$comment->ID,"' title='Permalink to Comment'>",$date,"</a></div></li>\n\r";

}
?>
</ol>
<div id="ajax-loader"></div>
</div>

<br /><br />
<div id="hideme">
<font color="#CC6600" size="+1">What Do You Have to Say?</font>
<form action="#comments" method="post" name="commentform" id="commentform">
<input type="text" name="name" id="name" maxlength="30" value="<?php
	if(isset($_COOKIE['name'])) echo $_COOKIE['name'];
	elseif(isset($_POST['name'])) echo $_POST['name'];
?>"/><label for="name"> Name (required)</label><br />
<input type="text" name="website" id="website" maxlength="200" value="<?php
	if(isset($_COOKIE['website'])) echo $_COOKIE['website'];
	elseif(isset($_POST['website'])) echo $_POST['website']; 
	else echo 'http://';
?>"/><label for="website"> Website (optional)</label><br />
<textarea rows="12" cols="45" name="comment" id="comment"><?php
	if(isset($_POST['submit']) && $comments->error == 1) echo $_POST['comment'];
?></textarea><br /><br />

<?php if(REQ_CAPTCHA) { ?>
<input id="security_code" name="security_code" type="text" maxlength="4" style="text-align:center;"/>&nbsp;<img src="<?php echo DOMAIN; ?>php/captcha.php" style="margin-bottom:-10px;" alt="" id="captcha" /><br />
<?php } ?>

<br /><br />
<input type="submit" id="submit-comment" name="submit-comment" tabindex="5" value="Submit Comment" />
</form>

<div class="smiliebox" id="smiliebox">
<h3>Add a Smilie!</h3>
<img src="<?php echo DOMAIN; ?>content/smilies/icon_arrow.gif" title="arrow" alt=":arrow:" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_biggrin.gif" title="Big Grin" alt=":D" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_confused.gif" title="confused" alt=":-?" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_cool.gif" title="cool" alt="8-)" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_cry.gif" title="cry" alt=":'(" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_eek.gif" title="eek" alt="o.O" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_evil.gif" title="evil" alt=">:[" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_exclaim.gif" title="exclaim" alt=":!:" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_idea.gif" title="idea" alt=":idea:" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_lol.gif" title="LOL" alt="LOL" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_mad.gif" title="mad" alt=":x" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_mrgreen.gif" title="Mr. Green" alt=":mrgreen:" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_neutral.gif" title="neutral" alt=":-/" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_question.gif" title="question" alt=":?:" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_razz.gif" title="razz" alt=":P" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_redface.gif" title="redface" alt=":oops:" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_rolleyes.gif" title="roll eyes" alt=":roll:" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_sad.gif" title="sad" alt=":(" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_smile.gif" title="smile" alt=":)" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_surprised.gif" title="surprised" alt=":O" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_twisted.gif" title="twisted" alt=">:]" />
<img src="<?php echo DOMAIN; ?>content/smilies/icon_wink.gif" title="wink" alt=";)" />
</div>
</div>
</div>
</center>
