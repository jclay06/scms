<?php

class comic {

	public $is_index		= true;
	public $has_ID			= false;
	public $got_comic		= false;

	private $archives		= false;
	private $archives_num	= 0;
	private $archives_cat	= 1;
	private $update			= false;

	public function __construct() {

		# grab and sort all categories
		$categories = $this->get_categories(ARRAY_A);
		$this->cat = (int) $categories[0]['ID'];
		$this->default_cat = (int) $categories[0]['ID'];
		usort($categories, 'sort_by_ID');
		$this->categories = $categories;
		
		# grab all users
		$this->users = $this->get_users();
	}

	public function get_comic($ID=0, $index=false) {
		global $scdb;

		$ID_query = '';
		if(is_numeric($ID) && $ID > 0 && $ID = (int) $ID) {
			$ID_query = " AND `ID` = '$ID'";
			$this->has_ID = true;
			$this->is_index = false;
		}
		
		$cat_query = (!$index) ? " AND `cat` = '$this->cat'" : '';

		$row = $scdb->get_row("SELECT * FROM `$scdb->comics` WHERE `time` <= '".NOW."' " . $ID_query . $cat_query . " ORDER BY `time` DESC LIMIT 1", ARRAY_A);
		if($scdb->num_rows === 0)
			return not_found();

		$this->set_vars($row);
		$scdb->query("UPDATE `$scdb->comics` SET `views` = `views` + 1 WHERE `PID` = '$this->PID' LIMIT 1");
		$this->got_comic = true;
	}
	
	public function get_comic_by_permalink($PID=0) {
		global $scdb;
		
		$PID_query = '';
		if(is_numeric($PID) && $PID > 0 && $PID = (int) $PID) {
			$PID_query = " AND `PID` = '$PID'";
			$this->has_PID = true;
			$this->is_index = false;
		}

		$row = $scdb->get_row("SELECT * FROM `$scdb->comics` WHERE `time` <= '".NOW."' " . $PID_query. " LIMIT 1", ARRAY_A);
		if($scdb->num_rows === 0)
			return not_found();
		
		$this->set_vars($row);
		$scdb->query("UPDATE `$scdb->comics` SET `views` = `views` + 1 WHERE `PID` = '$this->PID' LIMIT 1");
		$this->got_comic = true;
	}
	
	private function set_vars($row) {
		if(!is_array($row))
			return trigger_error('$comic->set_vars($row) must be called with an array as the argument!');
		
		global $scdb;
		
		$this->title		= $row['title'];
		$this->image		= $row['image'];
		$this->info			= $row['info'];
		$this->cat			= (int) $row['cat'];
		$this->category		= $this->cat_info($this->cat);
		$this->transcript	= $row['transcript'];
		$this->time			= $row['time'];
		$this->stamp		= (int) mysql_to_unix($this->time);
		$this->views		= (int) $row['views'] + 1;
		$this->ID			= (int) $row['ID'];
		$this->PID			= (int) $row['PID'];
				
		$user_info = $this->user_info($row['author']);
		$this->author = $user_info->nicename;
		$this->author_full = $user_info->name;
		
		// keep until i've changed all calls to $this->type to $this->category->nicename
		$this->type	= $this->category->nicename;
		
		$this->first = (int) $scdb->get_var("SELECT `ID` FROM `$scdb->comics` WHERE `cat` = '$this->cat' ORDER BY `ID` ASC LIMIT 1");
		$this->last = (int) $scdb->get_var("SELECT `ID` FROM `$scdb->comics` WHERE `cat` = '$this->cat' AND `time` <= '".NOW."' ORDER BY `ID` DESC LIMIT 1");

		if($this->ID !== $this->first)
			$this->prev = (int) $scdb->get_var("SELECT `ID` FROM `$scdb->comics` WHERE `ID` < '$this->ID' AND `cat` = '$this->cat' ORDER BY `ID` DESC LIMIT 1");
		if($this->ID !== $this->last)
			$this->next = (int) $scdb->get_var("SELECT `ID` FROM `$scdb->comics` WHERE `ID` > '$this->ID' AND `cat` = '$this->cat' ORDER BY `ID` ASC LIMIT 1");
	}
	
	public function cat_info($ID = false) {
		if(!$ID = is_nat($ID))
			$ID = $this->cat;
		foreach($this->categories as $category) {
			if($category['ID'] == $ID) return (object) $category;
		}
		return false;
	}
	
	public function cat_info_by_nicename($nicename) {
		foreach($this->categories as $category) {
			if($category['nicename'] == $nicename) return (object) $category;
		}
		return false;
	}
	
	public function user_info($ID) {
		foreach($this->users as $user) {
			if($user->ID == $ID) return $user;
		}
		return false;
	} 

	public function get_archives($num='all', $cat=0, $author='') {
		global $scdb;
		if(is_numeric($num) && $num > 0)
			$num = (int) $num;
		else $num = 'all';
		// consider allowing multiple categories to be return via an array ?
		$cat_query = '';
		$cat = (int) $cat;
		if($cat > 0)
			$cat_query = " AND `cat` = '$cat' ";
		$author_query = '';
		if(is_numeric($author) && $author > 0) {
			$author = (int) $author;
			$author_query = " AND `author` = '$author' ";
		}
		if($this->archives && ($this->archives_num === 'all' || intval($this->archives_num) > $num) && $num != 'all' && $this->archives_cat == $cat) {
		// we've grabbed the archives and just need to return a portion of it now
			return array_slice($this->archives, 0, $num);
		}
		elseif($this->archives && $this->archives_cat == $cat && $this->archives_num < $num) {
		// we've grabbed the archives and need to return more than what we grabbed
			$sql = "SELECT * FROM `$scdb->comics` WHERE `time` <= '".NOW."'".$cat_query.$author_query."ORDER BY `time` DESC";
			if($num != 'all' && $this->archives_num != 'all' && $this->archives_num < $num) {
				$extra_rows = $num - $this->archives_num;
				$sql .= ' LIMIT ' . $this->archives_num . ', ' . $extra_rows;
				$this->archives_num = $num;
			}
			$rows = $scdb->get_results($sql);
			$this->archives = array_merge($this->archives, $rows);
			return $this->archives;
		}
		else {
		// let's grab the archives
			$sql = "SELECT * FROM `$scdb->comics` WHERE `time` <= '".NOW."'".$cat_query.$author_query." ORDER BY `time` DESC";
			if($num != 'all')
				$sql .= " LIMIT " . $num;
			$rows = $scdb->get_results($sql);
			$this->archives = $rows;
			$this->archives_num = $num;
			$this->archives_cat = $cat;
			return $this->archives;
		}
	}
	
	public function get_categories($type=OBJECT) {
		global $scdb;
		// first one is the "default" category, then the rest in order by ID
		$categories = get_cached_var('categories', 0);
		if($categories === false) {
			$categories = $scdb->get_results("SELECT * FROM `$scdb->categories` ORDER BY `default` DESC, `ID` ASC", $type);
			cache_var('categories', $categories);
		}
		return $categories;
	}
	
	public function get_users($type=OBJECT) {
		global $scdb;
		$users = get_cached_var('users', 0);
		if($users === false) {
			$users = $scdb->get_results("SELECT login, pass, name, nicename, level, ID FROM `$scdb->users` ORDER BY `ID` DESC", $type);
			cache_var('users', $users);
		}
		return $users;
	}
	
	public function get_permalink($PID=false) {
		if($PID === false || intval($PID) != $PID) $PID = $this->PID;
		return DOMAIN . '?p=' . $PID;
	}
	
	public function get_filtered_link($vals=false, $input=false) {
		if($input === false) $input = LINK_STRUCTURE;
		$from = array('%id%', '%pid%', '%title%', '%cat%');
		if(!$vals) {
			$to = array($this->ID, $this->PID, $this->title, $this->cat_info($this->cat)->nicename);
		}
		elseif(is_array($vals)) {
			if(!isset($vals['title'], $vals['cat'], $vals['ID'], $vals['PID']))
				return trigger_error('$comic->get_filtered_link() must be called with an array setting the title, cat, ID, and PID values');
			$to = array($vals['ID'], $vals['PID'], $vals['title'], $this->cat_info($vals['cat'])->nicename);
		}
		else {
			global $scdb;
			$PID = (int) $PID;
			$c = $scdb->get_row("SELECT * FROM `$scdb->comics` WHERE `PID` = '$PID' LIMIT 1");
			$to = array($c->ID, $c->PID, $c->title, $this->cat_info($c->cat)->nicename);
		}
		$ret = str_replace($from, $to, $input);;
		return rtrim(DOMAIN, '/') . '/' . ltrim($ret, '/');
	}
	
	public function get_regex_link($values=false, $link_type='comic') {
		$input = LINK_STRUCTURE;
		if($values === false) {
			$to = array(	'%ID%' => $this->ID,
							'%PID%' => $this->PID,
							'%title%' => $this->title,
							'%cat%' => get_nicename($this->cat),
							'%cat_ID%' => $this->cat,
							'%year%' => substr($this->time, 0, 4),
							'%month%' => substr($this->time, 5, 2),
							'%day%' => substr($this->time, 8, 2)
						);
		}
		elseif(is_array($values)) {
			$to = array(	'%ID%' => $values['ID'],
							'%PID%' => $values['PID'],
							'%title%' => $values['title'],
							'%cat%' => get_nicename($values['cat']),
							'%cat_ID%' => $values['cat'],
							'%year%' => substr($values['time'], 0, 4),
							'%month%' => substr($values['time'], 5, 2),
							'%day%' => substr($values['time'], 8, 2)
						);
		}
		elseif(is_object($values)) {
			$to = array(	'%ID%' => $values->ID,
							'%PID%' => $values->PID,
							'%title%' => $values->title,
							'%cat%' => get_nicename($values->cat),
							'%cat_ID%' => $values->cat,
							'%year%' => substr($values->time, 0, 4),
							'%month%' => substr($values->time, 5, 2),
							'%day%' => substr($values->time, 8, 2)
						);
		}
		else {
			global $scdb;
			$PID = (int) $values;
			$c = $scdb->get_row("SELECT * FROM `$scdb->comics` WHERE `PID` = '$PID' LIMIT 1");
			$to = array(	'%ID%' => $c->ID,
							'%PID%' => $c->PID,
							'%title%' => $c->title,
							'%cat%' => get_nicename($c->cat),
							'%cat_ID%' => $c->cat,
							'%year%' => substr($c->time, 0, 4),
							'%month%' => substr($c->time, 5, 2),
							'%day%' => substr($c->time, 8, 2)
						);
		}
		$regexed = strtr($input, $to);
		return rtrim(DOMAIN, '/') . '/' . ltrim($regexed, '/');
	}

	public function get_link($ID=false, $cat='') {
		if($ID === false || !is_numeric($ID)) $ID = $this->ID;
		if($cat == '' || !is_numeric($cat)) $cat = $this->cat;
		return DOMAIN . $this->cat_info($cat)->nicename . '/' . $ID;
	}

	public function get_image($type=false) {
		if($type === false) $type = $this->type;
		return IMAGES . $type . '/' . $this->image;
	}
	
	public function get_image_by_permalink($PID=false) {
		if(($PID === false || !is_nat($PID)) && $comic->got_comic === true) {
			return $this->get_image();
		}
		$this->get_comic_by_permalink($PID);
		return $this->get_image();
	}
	
	public function not_default($cat='') {
		if(!$cat) return false;
		if($cat == '' || !is_numeric($cat)) $cat = $this->cat;
		if($cat != $this->default_cat) {
			return $this->cat_info($cat)->nicename;
		}
		return false;
	}
	
	public function permalink_image($width=false) {
		if($width && is_numeric($width)) $width = ' width="'.$width.'px"';
		$link = '<a href="' . $this->get_permalink() . '" title="' . $this->info . '"><img src="' . $this->get_image() . '" alt="' . $this->title . '"' . $width . '/></a>';
		return $link;
	}

	public function link_image($width=false) {
		if($width && is_numeric($width)) $width = ' width="'.$width.'px"';
		$link = '<a href="' . $this->get_link() . '" title="' . $this->info . '"><img src="' . $this->get_image() . '" alt="' . $this->title . '"' . $width . '/></a>';
		return $link;
	}

	public function get_date($format) {
		return date($format, $this->stamp);
	}

	public function random() {
		if(!$this->got_comic) $this->get_comic();
		$rand = mt_rand($this->first, $this->last);
		header('Location: ' . $this->get_link($rand));
		exit;
	}

	public function views() {
		return $this->views . ' Views';
	}

	public function filter_vars($format) {
		$from = array('%id%', '%title%', '%info%', '%image%');
		$to = array($this->ID, $this->title, $this->info, $this->image);
		return str_ireplace($from, $to, $format);
	}

	public function get_nav($before='', $after='', $between='') {
		$nav = array(); 
		if(isset($this->prev)) {
			$nav[] = $before . '<a href="'.$this->get_link($this->prev).'" title="Previous Comic">Back</a>' . $after;
		}
		if(isset($this->first) && $this->first !== $this->ID) {
			$nav[] = $before . '<a href="'.$this->get_link($this->first).'" title="First Comic">First</a>' . $after;
		}
		$nav[] = $before . '<a href="' . DOMAIN . $this->cat_info($this->cat)->nicename . '/random/" title="Be Spontaneous">Random</a>' . $after;
		if(isset($this->last) && $this->last !== $this->ID) {
			$nav[] = $before . '<a href="'.$this->get_link($this->last).'" title="Last Comic">Last</a>' . $after;
		}
		if(isset($this->next)) {
			$nav[] = $before . '<a href="'.$this->get_link($this->next).'" title="Next Comic">Next</a>' . $after;
		}
		$return = $nav[0] . $between;
		for($i=1, $num=count($nav)-1; $i<$num; ++$i) {
			$return .= $nav[$i] . $between;
		}
		$return .= $nav[$num];
		return $return;
	}
	
	public function alt_text() {
		return isset($this->transcript{1}) ? htmlentities($this->transcript) : htmlentities($this->info);
	}
	
	public function search($text, $page=1, $num=10, $format=true) {
		global $scdb;
		
		$this->search->page = (int) $page;
		$this->search->num = (int) $num;
		$this->search->start = (int) ($page - 1) * $num;
	
		$s = strtr($text, array("%"=>"&#37;"));
		$s = safe_text(strip_tags($s));
		$this->search->term = $s;
		$s = strtolower($s);

		$items = $scdb->get_results("
				SELECT transcript as text, PID, title FROM `$scdb->comics` WHERE `transcript` LIKE '%".$s."%'
				UNION SELECT title as text, PID, title FROM `$scdb->comics` WHERE `title` LIKE '%".$s."%'
				UNION SELECT info as text, PID, title FROM `$scdb->comics` WHERE `info` LIKE '%".$s."%'
				");
		
		$this->search->num_results = $scdb->num_rows;

		if($scdb->num_rows == 0) {
			return false;
		}
	
		$results = array();
		$i = 0;
		$max = 0;
		foreach($items as $item) {
			$n = substr_count(strtolower(strip_tags($item->text)), $s);
			$results[$i] = $item;
			$results[$i]->num = $n;
			$results[$i]->text = str_replace(array("\n", "\t", "\r"), ' ', $item->text);
			if($n > $max) {
				$max = $n;
			}
			++$i;
		}
	
		// an attempt at ordering by relevance
		function compare($x, $y) {
			$x = $x->num;
			$y = $y->num;
			if($x == $y) return 0;
			elseif($x > $y) return -1;
			else return 1;
		}
		usort($results, 'compare');
		
		$count = count($results);
		if($count > $num) {
			$results = array_slice($results, ($this->search->page * $num) - 1, $num, true);
		}
		
		if($format === true) {
			$i = 0;
			foreach($results as $result) {
				$small = smalltext($this->search->term, $result->text);
				$results[$i]->text = highlight_text($this->search->term, $small);
				++$i;
			}
		}
		
		$this->search->results = $results;
		return $results;
		
	}

}

$comic = new comic;


// FUNCTIONS

function search_prev() {
	global $comic;
	if($comic->search->page > 1) {
		echo '<span class="search-prev"><a href="',DOMAIN,'?s=',$comic->search->term,'&page=',$comic->search->page - 1,'">Previous Page</a></span>';
	}
}

function search_next() {
	global $comic;
	if($comic->search->num_results > $comic->search->start + $comic->search->num) {
		echo '<span class="search-next"><a href="',DOMAIN,'?s=',$comic->search->term,'&page=',$comic->search->page + 1,'">Next Page</a></span>';
	}
}

function is_category($nicename) {
	global $comic;
	if(!is_object($comic)) {
		return false;
	}
	return $comic->cat_info_by_nicename($nicename);
}

function get_nicename($cat_ID=false) {
	global $comic, $c;
	if($cat_ID !== false && $ID = is_nat($cat_ID)) {
		return $comic->cat_info($ID)->nicename;
	}
	if(is_object($c) && $ID = is_nat($c->cat)) {
		return $comic->cat_info($ID)->nicename;
	}
	return false;
}

function user_info($ID=false) {
	global $comic;
	return (object) $comic->user_info($ID);
}

function got_comic() {
	global $comic;
	if(isset($comic) && is_object($comic) && $comic->got_comic) return true;
	return false;
}

function is_index() {
	global $comic;
	return $comic->is_index;
}

?>