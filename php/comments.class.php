<?php

class comments {

	private $query			= false;
	public $message			= '';
	public $archives;
	public $archives_cat	= 0;
	public $total_comments	= false;
	public $comments		= false;
	public $num				= 0;
	
	public function comments() {
		$this->archives = (object) array('data'=>false, 'num'=>false, 'page'=>false, 'start'=>false, 'end'=>false);
	}
	
	public function get_comments($ID = false) {
		global $scdb, $comic;
		if($ID === false || !(is_numeric($ID) && $ID > 0)) {
			$ID =  $comic->ID;
		}
		if(isset($_POST['submit-comment'])) $this->post_comment($_POST);
		$this->comments = $scdb->get_results("SELECT * FROM `$scdb->comments` WHERE `comicPID` = '$ID' ORDER BY `ID` ASC");
		$this->num = (int) $scdb->num_rows;
	#	else echo '<p>You must call $comments->get_comments($ID) with a valid ComicID!</p>';
	}
	
	public function get_archives($num=10, $page=1) {
		global $scdb, $comic;

		$num = (int) is_nat($num) ? $num : 10;
		$page = (int) is_nat($page) ? $page : 1;
		$start = (int) (intval($page-1) * $num);
		
		// we already grabbed the results, just return them again
		if($this->archives->data !== false && $this->archives->cat == $this->archives_cat && $this->archives->num >= $num && $this->archives->page === $page && $this->archives->start === $start) {
			if($this->archives->num > $num) {
				return array_slice($this->archives->data, 0, $num);
			}
			return $this->archives->data;
		}
		
		$this->archives->cat = $this->archives_cat;
		$this->archives->page = $page;
		$this->archives->num = $num;
		$this->archives->start = $start + 1;
		$this->archives->end = $start + $num;
		
		// check if we've cached them, and return that if we have, return those
		if($archives = get_cached_var('recent-comments')) {
			if($archives->cat == $this->archives->cat && $archives->num >= $this->archives->num && $archives->page == $this->archives->page && $archives->start == $this->archives->start) {
				$this->archives = $archives;
				return array_slice($this->archives->data, 0, $num);
			}
		}
		
		$sql = "SELECT * FROM `$scdb->comments`";
		if($this->archives->cat > 0)
			$sql .= " WHERE `cat` = '".$this->archives->cat."'";
		$sql .= " ORDER BY `time` DESC LIMIT ".$start.", ".$num;
		
		$i = 0;
		foreach($scdb->get_results($sql) as $row) {
			$rows[$i] = $row;
			$rows[$i]->url = DOMAIN . $comic->cat_info($row->cat)->nicename . '/' . $row->comicID . '#comment-' . $row->ID;
			$rows[$i]->title = $scdb->get_var("SELECT `title` FROM `$scdb->comics` WHERE `PID` = '$row->comicPID' LIMIT 1");
			++$i;
		}
		if($i === 0) return false;
		
		$this->archives->data = $rows;

		// if we've made it this far we should cache the results
		cache_var('recent-comments', $this->archives);
		
		return $rows;
	}
	
	public function post_comment($values = false) {
		global $scdb, $comic;
		$this->post->error = true;
		if($values === false && $_POST) $values = $_POST;
		if(!isset($values['name']{1}) || !isset($values['comment']{2})) {
			$this->message = 'You forgot to enter your name or comment!';
			return false;
		}
		if(isset($values['comment']{1000})) {
			$this->message = "<br /><font size='+1'>Sorry, your message was WAY too long!<br /><smaller>(1000 character max)</smaller></font><br /><br />";
			return false;
			// handle error
		}
		elseif(REQ_CAPTCHA === true && (!isset($_SESSION['security_code'], $values['security_code']) || $_SESSION['security_code'] !== $values['security_code'] || empty($_SESSION['security_code']))) {
			// bad captcha code
				unset($_SESSION['security_code']);
				$this->message = '<p><font size="+3" color="red">f (_) c |(  u n00b.<br /><br /><br />Sp@m i$ 4 D4 l0s3rs</font>';
				return false;
		}
		else {
		// all is well, process the comment
		
			unset($_SESSION['security_code']);
			
			$_HOST = parse_url(DOMAIN, PHP_URL_HOST);
		
			$website = urltest($values['website']);
			if($website !== '') setcookie('website', $website, TIME + COOKIE_EXPIRES, '/', $_HOST);

			$name = safe_text($values['name'], 30);
			if(strlen($name) == 0) {
				$this->message = 'You forgot to enter your name!';
				return false;
			}
			setcookie('name', $name, TIME + COOKIE_EXPIRES, '/', $_HOST);

			$comment = url2awesome( safe_text($values['comment'], 1000) );
			if(strlen($comment) < 3) {
				$this->message = 'You forgot to enter your comment!';
				return false;
			}
			
		#	setcookie('email', $_SESSION['email'], TIME + COOKIE_EXPIRES, '/', $_HOST);

			$scdb->query("INSERT INTO `$scdb->comments` (name, comment, time, comicPID, comicID, cat) VALUES ('$name','$comment','".NOW."','$comic->PID','$comic->ID','$comic->cat')");
			if($scdb->rows_affected != 1) die('Error: '.mysql_error());
		
			$this->message = "<p><font size='+1'><em>Thanks for the comment!</em></font></p>";
			$this->post->error = false;
			
			$this->post->comment = $comment;
			$this->post->name = $name;
			$this->post->website = $website;
			$this->post->ID = (int) $scdb->get_var("SELECT `ID` FROM `$scdb->comments` WHERE `name` = '$name' AND `comment` = '$comment' ORDER BY `time` DESC LIMIT 1");
			
			$nicename = $comic->cat_info($comic->cat)->nicename;
			
			$body = "<strong><em>" . $this->post->name . "</strong></em> left a comment on the comic ---> <strong><em>" . $comic->title . "</strong></em> <br /><br />This is what <strong><em>" . $this->post->name . "</strong></em> had to say:<br /><br /><em><font size='+1'><blockquote>" . nl2br($this->post->comment) . "</blockquote></font></em><br /><br /><a href='" . DOMAIN . $nicename . '/' . $comic->ID . "#comment-" . $this->post->ID . "'><img src='" . IMAGES . $nicename . "/" . $comic->image . "' alt='" . $comic->title . "' width='300px'></img></a><br />(Click on the image to go straight to the comment)<br /><br /><br /><a href='" .DOMAIN . "'/admin/index.php?page=admin&option=comments&edit='" . $this->post->ID . "'>Is This Comment SPAM?</a>";
			$headers = "From: TheSpiffyLife.com <josh@thespiffylife.com>\r\n";
			$headers .= "MIME-Version: 1.0\r\n"; 
			$headers .= "Content-type: text/html";	
			mail(EMAIL, $name." has left a comment on a comic!", $body, $headers);
			
			// delete cached comic page & recent-comment cache
			clear_cache();
		}
	}
	
	public function del_comment($ID) {
		global $scdb;
		if(!is_numeric($ID) || intval($ID) != $ID || $ID < 1) {
			$this->message = '$comments->del_comment() must be called with a valid ID!';
			return false;
		}
		$scdb->query("DELETE FROM `$scdb->comments` WHERE `ID` = '$ID' LIMIT 1");
		if($scdb->rows_affected != 1) {
			$this->message = 'Error deleting comment with ID of '.$ID.'.';
			$scdb->debug();
			return false;
		}
		clear_cache();
		$this->message = 'Deleted comment with ID of '.$ID.'.';
		return true;
	}
	
	public function edit_comment($ID, $values) {
		global $scdb;
		if(!is_numeric($ID) || intval($ID) != $ID || $ID < 1) {
			$this->message = '$comments->edit_comment() must be called with a valid ID as the first parameter!';
			return false;
		}
		if(!isset($values['name']{1}) || !isset($values['comment']{1})) {
			$this->message = '$comments->edit_comment() must be called with an associative array as the second argument with the \'name\' and \'comment\' being set!';
			return false;
		}
		$query = "UPDATE `$scdb->comments` SET `name` = '".safe_text($values['name'], 100)."', `comment` = '".safe_text($values['comment'])."'";
		if(isset($values['website']) && urltest($values['website'])) {
			$query .= ", `website` = '".$values['website']."'";
		}
		if(isset($values['time']{18}) && preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", $values['time'])) {
			$query .= ", `time` = '".$values['time']."'";
		}
		$query .= " WHERE `ID` = '$ID' LIMIT 1";
		$scdb->query($query);
		if($scdb->rows_affected != 1 && mysql_errno()) {
			$this->message = 'Error editing the comment with ID of '.$ID.'.';
			$scdb->debug();
			return false;
		}
		$this->message = 'Updated comment with ID of '.$ID.'.';
		return true;
	}
	
	public function num_comments($zero=false, $one=false, $more=false) {
		if($zero && $one && $more) {
			if($this->num === 0) return $zero;
			elseif($this->num === 1) return $one;
			else return str_replace('%', $this->num, $more);
		}
		else {
			if($this->num === 0) return 'No Comments Yet';
			elseif($this->num === 1) return '1 Comment';
			else return $this->num . ' Comments';
		}
	}
	
	public function total_comments() {
		global $scdb;
		if($this->total_comments == false) {
			if($total = $scdb->get_var("SELECT COUNT(*) FROM `$scdb->comments`")) {
				$this->total_comments = (int) $total;
				return $this->total_comments;
			}
			else {
				// handle error
				return false;
			}
		}
		else {
			return $this->total_comments;
		}
	}
	
	public function comments_on($comicPID = false) {
		global $scdb, $comic;
		if($comicPID === false || intval($comicPID) != $comicPID || $comicPID < 1) {
			$comicPID = $comic->PID;
		}
		return (int) $scdb->get_var("SELECT COUNT(*) FROM `$scdb->comments` WHERE `comicPID` = '$comicPID' LIMIT 1");
	}
	
	public function comments_by_cat_on($cat, $ID) {
		global $scdb;
		return (int) $scdb->get_var("SELECT COUNT(*) FROM `$scdb->comments` WHERE `cat` = '$cat' AND `comicID` = '$ID' LIMIT 1");
	}
	
	public function return_comments() {
		if($this->num > 0)
			return $this->comments;
		return false;	
	}
	
	public function recent_comments($num = 10) {

		if($this->get_archives(5) !== false) { ?>

		<h2 class="sidebartitle">Recent Comments</h2>
		<ul class="recent_comments">

		<?php foreach($this->get_archives(5) as $comment) { ?>
			<li><?php echo $comment->name; ?> on <a href="<?php echo $comment->url; ?>" title="view this comment"><strong><em><?php echo $comment->title; ?></em></strong></a></li>
		<?php } ?>

		</ul>
<?php
		}
	}

}

$comments = new comments;

?>