<?php

class news {

	public function __construct() {
		global $comic;
		if(is_object($comic)) {
			if(isset($comic->users)) $this->users = & $comic->users;
			else $this->users = $comic->get_users();
		}
		#$this->categories = $this->get_categories();
	}
	
	public function get_post($ID=0, $cat=false) {
		global $scdb;
		$sql = "SELECT * FROM `$scdb->news` WHERE";
		if($ID = is_nat($ID)) {
			$sql .= " `ID`='$ID' AND ";
		}
		if($cat = is_nat($cat)) {
			$sql .= " `cat`='$cat' AND ";
		}
		$sql .= " `time`<='".NOW."' ORDER BY `ID` DESC LIMIT 1";
		$post = $scdb->get_row($sql);
		if($scdb->num_rows !== 1) return false;
		return $post;
	}
	
	public function get_post_on_comic($PID) {
		global $scdb;
		if(!$PID = is_nat($PID)) return false;
		$post = $scdb->get_row("SELECT * FROM `$scdb->news` WHERE `comicPID`='$PID' ORDER BY `ID` DESC LIMIT 1");
		if($scdb->num_rows !== 1) return false;
		return $post;
	}
	
	public function get_archives($num=3, $cat='all', $page=1) {
		global $scdb;
		$this->archives->num = (int) is_nat($num) ? $num : 3;
		$this->archives->page = (int) is_nat($num) ? $page : 1;
		$this->archives->start = (int) ($page - 1) * $num;
		$this->archives->cat = $cat;
		$sql = "SELECT * FROM `$scdb->news` ";
		if('all' != $cat && $cat = is_nat($cat)) {
			$this->archives->cat = $cat;
			$sql .= "WHERE `cat` = $cat ";
		}
		$sql .= ' ORDER BY `ID` DESC LIMIT '.$this->archives->start.','. $this->archives->num;
		$this->archives->data = $scdb->get_results($sql);
		$this->archives->last = $this->archives->data[end( array_keys($this->archives->data) )];
		return $this->archives->data;
	}
	
	public function get_categories($type=OBJECT) {
		global $scdb;
		$categories = get_cached_var('news-categories');
		if($categories === false) {
			$categories = $scdb->get_results("SELECT * FROM `$scdb->news_categories` ORDER BY `default` DESC, `ID` ASC", $type);
			cache_var('news-categories', $categories);
		}
		return $categories;
	}
	
	public function user_info($ID) {
		foreach($this->users as $user) {
			if($user->ID == $ID) return $user;
		}
		return false;
	}
	
	public function cat_info($ID) {
		foreach($this->categories as $cat) {
			if($cat->ID == $ID) return $cat;
		}
		return false;
	}
	
	public function publish($values) {
		if(!is_array($values)) return false;
		if(!isset($values['title'], $values['cat'], $values['content'], $values['author'])) return false;
		$title = safe_text($values['title'], 150);
		$nicetitle = nicename($title);
		$content = safe_text($values['content']);
		$cat = (int) $values['cat'];
		if($this->cat_info($cat) === false) {
			return false;
		}
		$comicPID = (int) isset($values['comicPID']) ? $values['comicPID'] : 0;
		$author = (int) abs($values['author']);
		if($this->user_info($author) === false) {
			return false;
		}
		$scdb->query("INSERT INTO `$scdb->news` (title, nicetitle, content, cat, comicPID, time, author) VALUES ('$title', '$nicetitle', '$content', $cat, $comicPID, '".NOW."', $author)");
		if($scdb->rows_affected !== 1) {
			return false;
		}
		return true;
	}
	
	public function previous_post_link() {
		global $post, $scdb;
		if(!is_object($post) || !isset($post->ID)) return false;
		$prev = $scdb->get_row("SELECT * FROM `$scdb->news` WHERE `ID` < '$post->ID' LIMIT 1");
		if($scdb->num_rows !== 1) return false;
		// should probably make nice URLs but i'll handle that later
		echo rtrim(DOMAIN, '/'), '/news/?p=', $prev->ID;
	}
	
	public function next_post_link() {
		global $post, $scdb;
		if(!is_object($post) || !isset($post->ID)) return false;
		$next = $scdb->get_row("SELECT * FROM `$scdb->news` WHERE `ID` > '$post->ID' LIMIT 1");
		if($scdb->num_rows !== 1) return false;
		// should probably make nice URLs but i'll handle that later
		echo rtrim(DOMAIN, '/'), '/news/?p=', $next->ID;
	}
	
	public function previous_page_link() {
		global $scdb;
		if(!isset($this->archives) || $this->archives->page === 1) return false;
		echo '<a href="', rtrim(DOMAIN, '/'), '/news/page/', $this->archives->page - 1, '">Newer Posts</a>';
	}
	
	public function next_page_link() {
		global $scdb;
		if(!isset($this->archives)) return false;
		$next = $scdb->get_var("SELECT `ID` FROM `$scdb->news` WHERE `ID` > '".$this->archives->last->ID."' LIMIT 1");
		if($scdb->num_rows !== 1) return false;
		echo '<a href="', rtrim(DOMAIN, '/'), '/news/page/', $this->archives->page + 1, '">Older Posts</a>';
	}

}

$news = new news;

?>