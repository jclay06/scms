<?php
require('../config.php');
require(PHP_DIR.'functions.php');

$message = '';
if(isset($_POST['submit'])) {
	if(preg_match('/\W/', $_POST['form_login']) || isset($_POST['form_login']{99})) {
		$message .= '<p class="error">Invalid Username: Must be between 6 &amp; 100 alphanumeric characters!</p>';
	}
	if(preg_match('/\W/', $_POST['form_password']) || isset($_POST['form_password']{99})) {
		$message .= '<p class="error">Invalid Password: Must be between 6 &amp; 100 alphanumeric characters!</p>';
	}
	if('' == $message) {
		$user = safe_text($_POST['form_login']);
		$pass = pwhash($_POST['form_password']);
		$info = $scdb->get_row("SELECT level, email, nicename, ID FROM `$scdb->users` WHERE `login` = '$user' && `pass` = '".$pass."' LIMIT 1", ARRAY_A);
		if($scdb->num_rows == 1) {
			$_SESSION['user'] = $user;
			$_SESSION['uid'] = (int) $info['uid'];
			$_SESSION['email'] = $info['email'];
			$_SESSION['name'] = $info['nicename'];
			$_SESSION['level'] = (int) $info['level'];
			$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
			
			$_HOST = parse_url(DOMAIN, PHP_URL_HOST);
			setcookie('user', $_SESSION['user'], TIME + COOKIE_EXPIRES, '/', $_HOST);
			setcookie('email', $_SESSION['email'], TIME + COOKIE_EXPIRES, '/', $_HOST);
			setcookie('name', $_SESSION['name'], TIME + COOKIE_EXPIRES, '/', $_HOST);
			
			if(!isset($_SESSION['redirect'])) $_SESSION['redirect'] = 'index.php';
			header('Location: '.$_SESSION['redirect']);
			$_SESSION['redirect'] = '';
			exit;
		}
		else {
			$message .= '<p class="error">Invalid Username / Password!</p>';
		}
	}
	else $scdb->debug();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<title>Admin &raquo; Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript">
	function focusit() {
		var formLogin = document.getElementById('form_login');
		if(formLogin){
			formLogin.focus();
		}
	}
	window.onload = focusit;
</script>
<link rel="stylesheet" href="style/css/login.css" type="text/css" media="screen" />
</head>
<body class="login">
<!-- shamelessly taken from wordpress 2.5 - thank you guys!!! -->
<div style="margin-top:75px;margin-bottom:-75px;"><center><img src="style/img/scms-logo.gif" alt="" /></center></div>
<div id="logo">
<a href="http://code.thespiffylife.com/" title="Spiffy CMS"><span class="h1">SCMS <span class="description"># open source comic managment</span></span></a>
</div>

<?php if('' != $message) { ?>
<div style="width:290px;margin:10px auto;text-align:center;" id="login_error"><?php echo $message; ?></div>
<?php } ?>

<div id="login">
<form action="login.php" method="post" name="loginform" id="loginform">
<p>
<label>Username:<br />
<input type="text" name="form_login" id="form_login" class="input" value="" size="20" tabindex="10" /></label>
</p>
<p>
<label>Password:<br />
<input type="password" name="form_password" id="form_password" class="input" value="" size="20" tabindex="20" /></label>
</p>
<p class="submit">
<input type="submit" name="submit" value="Log in" tabindex="100" />
</p>

</form>
<p id="nav">
<a href="?action=lostpassword" title="Lost your password?">Lost your password?</a>
</p>
</div>
</body>
</html>