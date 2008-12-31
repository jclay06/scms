<?php

class scadmin {

	function __construct() {

	}
	
	function add_user($values) {
		
		if(!isset($values['name'], $values['login'], $values['email'], $values['level'])) {
			return '$scadmin->add_user() must be called with an array containing the name, login, email, and level values!';
		}
		
		$name = safe_text($values['name'], 150);
		$login = safe_text($values['login'], 50);

		if(strlen($name) > 1 && strlen($login) > 4) {
			$pass = ($values['pass'] == $values['pass2']) ? pwhash($values['pass']) : false;
			if($pass === false) return 'Your passwords don&#39;t match!';
			else {
				$datetime = NOW;
				$insert = $scdb->query("INSERT INTO `$scdb->users` (login, pass, name, level, join_date) VALUES ('$login','$pass','$name','1','$datetime')");
				if(!$insert) return 'Failed to add the user to the DB!<br />'.mysql_error();
				else return 'Added User: <em>'.$login.'</em> !';
			}
		}
		else {
			return 'Your name or login is too short!';
		}
		
		return false; // this is actually means all went okay

	}
	
	function get_cat_info($cat) {
		global $scdb;
		if(intval($cat) != $cat || $cat < 1) return false;
		$cat = (int) $cat;
		$this->category = $scdb->get_row("SELECT * FROM `$scdb->categories` WHERE ID = '$cat' LIMIT 1");
		if($scdb->num_rows !== 1) {
			$this->category = false;
			return false;
		}
		return true;
	}
	
	function upload_comic($file = 'file') {
	
		$this->upload->message = '';

		if(!isset($_FILES[$file]))
			return false;
			
		$_FILE = $_FILES[$file];
		
		if($_FILE['size'] > 524288) {
			$this->upload->message = 'File size is larger than the maximum allowed (0.5 MB)';
			return false;
		}
		$allowed_types = array('image/gif', 'image/pjpeg', 'image/jpeg', 'image/jpg', 'image/png');
		if(!in_array($_FILE['type'], $allowed_types)) {
			$this->upload->message = 'Invalid image type ('.$_FILE['type'].')';
			return false;
		}
		
		$image = safe_text($_FILE['name'], 100, 'image');
		$file = IMG_FOLDER . $this->category->nicename . '/' . $image;
		
		if($image === false) {
			$this->upload->message = 'The file extension on your image is not valid';
			return false;
		}
		
		if($_FILE['error'] > 0) {
    		$this->upload->message = 'FILE ERROR: Return Code: ' . $_FILE['error'];
    		return false;
    	}
    	
		if(file_exists('upload/' . $_FILE['name'])) {
			$this->upload->message = $_FILE['name'] . ' already exists in ' . ROOT . 'admin/upload/';
			return false;
		}
		
		if(!isset($this->category->nicename) || $this->category->nicename == '') {
			$this->upload->message = 'Somehow you didn&#8217;t choose a category.';
			return false;
		}
		
		if(file_exists($file)) {
			$this->upload->message = $image . ' already exists.';
			return false;
		}
		
		if(!move_uploaded_file($_FILE['tmp_name'], $file)) {
			$this->upload->message = 'Error moving file!';
			return false;
		}
		else {
			$this->upload->message = 'Successfully Uploaded: &quot;' . $image . '&quot;';
			return true;
		}
		
	}
	
	function add_comic($comic) {
	
		global $scdb;
	
		if(!is_array($comic) || !isset($comic['title']) || !isset($comic['image'])) {
			$this->comic->message = 'You must call $scadmin->add_comic() with an associative array of values as the argument setting at least the "title" and "image" values.';
			return false;
		}
		
		$title = safe_text($comic['title']);
	
		$info = isset($comic['info']) ? safe_text($comic['info'], 200) : '';
	
		$image = safe_text($comic['image'], 100, 'image');
		if($image === false) {
			$this->comic->message = 'Invalid Image File/Name';
			return false;
		}
		
		$datetime = (isset($comic['date']) && isset($comic['time'])) ? $comic['date'].' '.$comic['time']: '';
		if(!preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", $datetime)) {
			$datetime = NOW;
		}
		
		# next ID for this category
		$ID = (int) $scdb->get_var("SELECT `ID` + 1 FROM `$scdb->comics` WHERE `cat` = '".$this->category->ID."' ORDER BY `PID` DESC LIMIT 1");
	
		if(!$scdb->query("INSERT INTO `$scdb->comics` (title, image, info, cat, time, ID) VALUES ('$title','$image','$info','".$this->category->ID."','$datetime','$ID')")) {
			$this->comic->message = 'Failed to add comic to the DB!';
			$scdb->debug();
			return false;
		}
		
		$this->comic->message = 'Added Comic!';
		refresh_facebook();
		clear_cache();
		generate_sitemap();
		return true;
			
	}
	
	function edit_comic($ID, $comic) {
	
		global $scdb;
		
		$PID = (int) $ID;
		
		if($PID != $ID || $ID < 1) {
			$this->comic->message = 'You must call $scadmin->edit_comic() with a valid permalink ID at the first agrument!';
			return false;
		}
	
		if(!is_array($comic) || !isset($comic['title']) || !isset($comic['image'])) {
			$this->comic->message = 'You must call $scadmin->edit_comic() with an associative array of values as the argument setting at least the "title" and "image" values.';
			return false;
		}
		
		$title = safe_text($comic['title']);
	
		$info = isset($comic['info']) ? safe_text($comic['info'], 200) : '';
	
		$image = safe_text($comic['image'], 100, 'image');
		if($image === false) {
			$this->comic->message = 'Invalid Image File/Name';
			return false;
		}
		
		// consider leaving the `time` field alone if these aren't set instead of using NOW()
		$datetime = (isset($comic['date']) && isset($comic['time'])) ? $comic['date'].' '.$comic['time']: '';
		if(!preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", $datetime)) {
			$datetime = NOW;
		}
	
		if(!$scdb->query("UPDATE `$scdb->comics` SET title='$title', info='$info', image='$image', time='$datetime' WHERE `PID` = '$PID' LIMIT 1")) {
			$this->comic->message = 'Failed to edit comic!';
			$scdb->debug();
			return false;
		}
		
		$this->comic->message = 'Succesfully Edited!';
		refresh_facebook();
		clear_cache();
		generate_sitemap();
		return true;
	
	}
	
	function add_category($cat) {
	
		global $scdb;
		
		if(!isset($cat['category']{2})) {
			$this->category->message = 'You must call $scadmin->add_category() with an associative array, with at least the "category" value being set.';
			return false;
		}
	
		$category = safe_text($cat['category'], 50);
		
		$nicename = nicename($cat['nicename']);
		
		$info = (isset($cat['info'])) ? safe_text($cat['info'], 200) : '';
	
		if(!$scdb->query("INSERT INTO `$scdb->categories` (category, nicename, info) VALUES ('$category','$nicename','$info')")) {
			$this->category->message = 'Failed to add category to the DB!';
			$scdb->debug();
			return false;
		}
	
		$default = (int) (isset($cat['default'])) ? $cat['default'] : 0;
		if($default !== 0) $this->set_default_category($default);

		if(!is_dir(IMG_FOLDER.$nicename)) {
			mkdir(IMG_FOLDER.$nicename, 0755);
		}
		
		$this->category->message = 'Added Category!';
		return true;
	
	}
	
	function edit_category($ID, $cat) {
	
		global $scdb;
		
		if(intval($ID) != $ID || $ID < 1 || !isset($cat['category']{2})) {
			$this->category->message = 'Error: You must call $scadmin->edit_category() with an associative array, with at least the "category" value being set.';
			return false;
		}
		
		$ID = (int) $ID;
	
		$category = safe_text($cat['category'], 50);
		
		$nicename = nicename($cat['nicename']);
		
		$info = (isset($cat['info'])) ? safe_text($cat['info'], 200) : '';
		
		$oldcat = $scdb->get_row("SELECT * FROM `$scdb->categories` WHERE `ID` = '$ID' LIMIT 1");
		if($scdb->num_rows != 1) {
			$this->category->message = "Can't find category with `ID` = '$ID'";
			$scdb->debug();
			return false;
		}
		
		if(!$scdb->query("UPDATE `$scdb->categories` SET `category`='$category', `nicename`='$nicename', `info`='$info' WHERE `ID` = '$ID' LIMIT 1")) {
			$this->category->message = 'Error Updating Category!';
			$scdb->debug();
			return false;
		}

		$folder = IMG_FOLDER.$oldcat->nicename;
		if(is_dir($folder)) {
			rename($folder, IMG_FOLDER.$nicename);
		}
		else {
			$this->category->message = 'Error: Category Folder '.IMG_FOLDER.$oldcat->nicename.' can&#39;t be found!';
			return false;
		}
		
		$this->category->message = 'Succesfully Edited Category!';
		return true;
	}
	
	function set_default_category($ID) {
		if(intval($ID) != $ID || $ID < 1) {
			return false;
		}
		$scdb->query("UPDATE `$scdb->categories` SET `default` = '0' WHERE `default` != '0'");
		$scdb->query("UPDATE `$scdb->categories` SET `default` = '1' WHERE `ID` = '$ID' LIMIT 1");
		
	}

}

$scadmin = new scadmin;


?>