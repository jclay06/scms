<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
	exit(header('Location: index.php'));
}
?>

<h3>Your Profile</h3>
<form action="index.php?page=admin&option=user-new" method="post" class="jNice" onsubmit="return verify_all.run()">
<fieldset>

	<p><label for="name">User's Name :</label><input type="text" name="name" id="name" class="text-long" maxlength="150" />
	<span id="nerror" class="error"></span></p>
	
	<p><label for="login">Login :</label><input type="text" name="login" id="login" class="text-long" maxlength="50" />
	<span id="lerror" class="error"></span></p>
	
	<p><label for="email">Email :</label><input type="text" name="email" id="email" class="text-long" maxlength="150" />
	<span id="eerror" class="error"></span></p>
	
	<p><label for="pass">Password :</label><input type="password" name="pass" id="pass" value="" class="text-medium" />
	<span id="perror1" class="error"></span></p>
		
	<p><label for="pass2">Verify Password :</label><input type="password" name="pass2" id="pass2" value="" class="text-medium" />
	<span id="perror2" class="error"></span></p>

	<p><input type="submit" name="new_user" value="Add User" class="button-submit" /></p>

</fieldset>
</form>
