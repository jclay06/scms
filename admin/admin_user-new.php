<?php
if(basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) || !user_is('admin')) {
	exit(header('Location: index.php'));
}

if(isset($_POST['new_user'])) {
	if($error = $scadmin->add_user($_POST)) {
		echo '<p class="message">',$error,'</p>';
	}
}

?>

<?php if(isset($message)) echo '<br /><p class="message">',$message,'</p>'; ?>

<script type="text/javascript" language="Javascript">
window.addEvent('domready', function() {
	var name = $('name');
	var login = $('login');
	var email = $('email');
	var pass = $('pass');
	var pass2 = $('pass2');
	name.addEvent('keyup', function() {
		(function() {
		$('nerror').empty();
		if(this.value.length < 3) {
			$('nerror').set('text', 'User Name is too short!');
			return false;
		}
		}).delay(500);
	});
	login.addEvent('keyup', function() {
		(function() {
		$('lerror').empty();
		if(this.value.length < 3) {
			$('lerror').set('text', 'Login Name is too short!');
			return false;
		}
		}).delay(500);
	});
	email.addEvent('keyup', function() {
		(function() {
		$('eerror').empty();
		var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
		var ajax = new Request.JSON({
			url: 'ajax.php',
			onSuccess: function(result) {
				if(result.valid == false) {
					$('eerror').set('text', 'Invalid Email Address!');
					return false;
				}
				return true;
			},
			onFailure: function() {
				$('eerror').set('text', 'Failed to check Address Domain :-/');
				return false;
			}
		});
		if(ajax.get('email='+email.value) == false || email.value.search(emailRegEx) == -1) {
			$('eerror').set('text', 'Invalid Email Address!');
			return false;
		}
		}).delay(1000);
	});
	var passCheck = function() {
		(function() {
		$('perror1').empty();
		$('perror2').empty();
		if(pass.value.length < 6) {
			$('perror1').set('text', 'Your password is too short!');
			return false;
		}
		else if(pass.value != pass2.value) {
			$('perror2').set('text', 'Your passwords don\'t match!');
			return false;
		}
		return true;
		}).delay(500);
	};
	pass.addEvent('keyup', passCheck);
	pass2.addEvent('keyup', passCheck);
	var verify_all = function() {
		name.fireEvent('keyup');
		login.fireEvent('keyup');
		email.fireEvent('keyup');
		pass.fireEvent('keyup');
		$$('span.error').each(function(input, index) {
			if(input.get('text').length > 0) {
				return false;
			}
		});
		return true;
	};
});
</script>

<style type="text/css" media="screen">
.error {
	color:red;
	font-size:13px;
}
</style>

<h3>Add a New User</h3>
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
	
	<p>
	<label for="level">User Level :</label>
	<select name="level" id="level">
		<option value="user">User</option>
		<option value="comic_author">Comic Author</option>
		<option value="news_author">News Author</option>
		<option value="author">Author</option>
		<option value="editor">Editor</option>
		<option value="admin">Administrator</option>
	</select>
	</p>

	<p><input type="submit" name="new_user" value="Add User" class="button-submit" /></p>

</fieldset>
</form>
