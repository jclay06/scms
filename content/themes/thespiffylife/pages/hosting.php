<?php get_header(); ?>

<title>Spiffy Hosting</title>
</head>

<?php get_sidebar(); ?>

<div id="content">
<div class="post"> 
	  
	<div class="title"><h5>Need extremely cheap hosting?</h5></div>

	<div class="post-content">
	
<?php		
if(isset($_POST['submit']) && ($_SESSION['security_code'] == $_POST['security_code']) && (!empty($_SESSION['security_code']))) {

unset($_SESSION['security_code']);

date_default_timezone_set('America/Chicago');
$datetime = date("Y-m-d H:i:s",$_SERVER['REQUEST_TIME']);
    
include('/home/joscla/mysql.php');

$findterms = array("'",'"');
$replaceterms = array("&#8217;","&quot;");

$urltest = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
if (eregi($urltest, $_POST['website'])) {$website = $_POST['website'];} else {$website = 0;}

$comment = str_replace($findterms,$replaceterms,$_POST['comment']);

$to = "gjcomics@gmail.com";
$subject = stripslashes($_POST['name']) . " is asking about shared hosting!";
$headers = "From: TheSpiffyLife.com <questions@thespiffylife.com>\r\n";
$headers .= "MIME-Version: 1.0\r\n"; 
$headers .= "Content-type: text/html";

$body = "<br /><strong><em>" . stripslashes($_POST['name']) . "</strong></em> at the e-mail address <a href='mailto:" . $_POST['email'] . "'>" . $_POST['email'] . "</a> is thinking about sharing hosting with you! <br /><br />They also left this comment: <br /><em>" . nl2br($comment) . "</em><br /><br />";

if($website != 0) $body .= "Their current website is: <a href='" . $website . "'>" . $website . "</a>";
  
mail($to,$subject,$body,$headers);

echo "<br /><font size='+1' color='blue'><center>Thanks for the message! We&#8217;ll respond as fast as we can :-)</center></font><br /><br />";

}
elseif(isset($_POST['submit'])) {
	$message = "<br /><center><font size='+3' color='red'>f (_) c |(  u n00b.<br /><br /><br />Sp@m i$ 4 D4 l0s3rs</font></center><br /><br />";
}
?>
		
<br /><font size="2em"><center>
We&#8217;ve got a LOT of disk space and bandwidth available for sharing <br />(don&#8217;t worry we&#8217;re getting a good deal)...<br /><br />
So we&#8217;re willing to offer pretty much all the space / bandwidth you&#8217;ll need <br />(including e-mail addresses, MySQL databases, etc)<br /><br />

If you&#8217;re looking for hosting we can discuss how nearly <a title="i&#8217;m thinkin like $10 a year-ish">free</a> 
we&#8217;re willing to share our masses for <img src='/content/smilies/icon_wink.gif' title='wink' alt=';)'><br /><br />

Just fill out the form below and we'll get back to you spiff-tastically fast <img src='/content/smilies/icon_lol.gif' title='LOL' alt='lol'>

</center></font>
<br /><br /><br />

<?php if(isset($message)) echo $message; ?>

<form method="post" action="hosting.php" name="commentform" id="commentform">
<table cellspacing="10">

<input type="text" name="name" id="name" maxlength="50" /><label for="name"> Name</label><br />
<input type="text" name="email" id="email" maxlength="100"/><label for="email"> Email </label><br />
<input type="text" name="website" id="website" maxlength="200"/><label for="website"> Website (optional) </label><br />
<textarea rows="12" cols="45" name="comment" id="comment" value=""></textarea><br /><br />

<input id="security_code" name="security_code" type="text" maxlength="4"/>&nbsp;<img src="/php/captcha.php" style="margin-bottom:-10px;" alt=""/><br /><br />

</table>

<center><input type="submit" name="submit" id="submit" value="Send Message" /></center>
</form>

	
	</div>
	</div>
  	</div>
  
<?php get_footer(); ?>