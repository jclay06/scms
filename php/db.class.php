<?php

define('EZSQL_VERSION','2.03');
define('OBJECT','OBJECT',true);
define('ARRAY_A','ARRAY_A',true);
define('ARRAY_N','ARRAY_N',true);
define('EZSQL_CORE_ERROR','ezSQLcore can not be used by itself (it is designed for use by database specific modules).');
	
$ezsql_mysql_str = array(
		1 => 'Require $dbuser and $dbpassword to connect to a database server',
		2 => 'Error establishing mySQL database connection. Correct user/password? Correct hostname? Database server running?',
		3 => 'Require $dbname to select a database',
		4 => 'mySQL database connection is not active',
		5 => 'Unexpected error while trying to select database'
	);

class scdb {

	public $trace				= false;  // same as $debug_all
	public $debug_all			= false;  // same as $trace
	public $debug_called		= false;
	public $vardump_called		= false;
	public $show_errors			= true;
	public $num_queries			= 0;
	public $last_query			= null;
	public $last_error			= null;
	public $col_info			= null;
	public $captured_errors		= array();

	public $cache_dir			= false;
	public $cache_queries		= false;
	public $cache_inserts		= false;
	public $use_disk_cache		= false;
	public $cache_timeout		= 24; // hours
		
	public $dbuser				= false;
	public $dbpassword			= false;
	public $dbname				= false;
	public $dbhost				= false;

	// == TJH == default now needed for echo of debug function
	public $debug_echo_is_on 	= true;
	
	public $tables				= array();
	public $table_prefix		= '';

	/**********************************************************************
	*  Constructor
	*/

	public function scdb($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost') {
		$this->__contruct($dbuser, $dbpassword, $dbname, $dbhost);
	}
	
	public function __construct($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost') {
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;
	}

	/**********************************************************************
	*  Short hand way to connect to mySQL database server
	*  and select a mySQL database at the same time
	*/

	public function quick_connect($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost') {
		$return_val = false;
		if ( ! $this->connect($dbuser, $dbpassword, $dbhost,true) ) ;
		elseif ( ! $this->select($dbname) ) ;
		else $return_val = true;
		return $return_val;
	}

	/**********************************************************************
	*  Try to connect to mySQL database server
	*/

	public function connect($dbuser='', $dbpassword='', $dbhost='localhost') {
		global $ezsql_mysql_str; $return_val = false;

		// Must have a user and a password
		if ( ! $dbuser ) {
			$this->register_error($ezsql_mysql_str[1].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[1],E_USER_WARNING) : null;
		}
		// Try to establish the server database handle
		else if ( ! $this->dbh = @mysql_connect($dbhost,$dbuser,$dbpassword,true) ) {
			$this->register_error($ezsql_mysql_str[2].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[2],E_USER_WARNING) : null;
		}
		else {
			$this->dbuser = $dbuser;
			$this->dbpassword = $dbpassword;
			$this->dbhost = $dbhost;
			$return_val = true;
		}

		return $return_val;
	}

	/**********************************************************************
	*  Try to select a mySQL database
	*/

	public function select($dbname='') {
		global $ezsql_mysql_str; $return_val = false;

		// Must have a database name
		if ( ! $dbname ) {
			$this->register_error($ezsql_mysql_str[3].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[3],E_USER_WARNING) : null;
		}

		// Must have an active database connection
		else if ( ! $this->dbh ) {
			$this->register_error($ezsql_mysql_str[4].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[4],E_USER_WARNING) : null;
		}

		// Try to connect to the database
		else if ( !@mysql_select_db($dbname,$this->dbh) ) {
			// Try to get error supplied by mysql if not use our own
			if ( !$str = @mysql_error($this->dbh))
				  $str = $ezsql_mysql_str[5];

			$this->register_error($str.' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
		}
		else {
			$this->dbname = $dbname;
			$return_val = true;
		}

		return $return_val;
	}
		
	public function set_table_prefix($prefix) {
		if( $prefix && strlen($prefix) > 0 ) {
			if ( preg_match('|[^a-z0-9_]|i', $prefix) )
				die('Invalid database prefix');
		}
		else $prefix = '';

		$old_prefix = $this->table_prefix;
		$this->table_prefix = $prefix;

		$this->set_tables($this->tables);

		return $old_prefix;
	}
	
	public function set_tables($tables) {
		if(!is_array($tables)) {
			die('$scdb->set_tables() should be called with an array of table names!');
		}
		$this->tables = $tables;
		foreach($this->tables as $table) {
			$this->$table = $this->table_prefix . $table;
		}
	}

	/**********************************************************************
	*  Format a mySQL string correctly for safe mySQL insert
	*  (no mater if magic quotes are on or not)
	*/

	public function escape($str) {
		return mysql_escape_string(stripslashes($str));
	}

	/**********************************************************************
	*  Return mySQL specific system date syntax
	*  i.e. Oracle: SYSDATE Mysql: NOW()
	*/

	public function sysdate() {
		return 'NOW()';
	}

	/**********************************************************************
	*  Perform mySQL query and try to detirmin result value
	*/

	public function query($query) {

		// Initialise return
		$return_val = 0;

		// Flush cached values..
		$this->flush();

		// For reg expressions
		$query = trim($query);

		// Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;

		// Count how many queries there have been
		$this->num_queries++;

		// Use core file cache function
		if ( $cache = $this->get_cache($query) ) {
			return $cache;
		}

		// If there is no existing database connection then try to connect
		if ( ! isset($this->dbh) || ! $this->dbh ) {
			$this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
			$this->select($this->dbname);
		}

		// Perform the query via std mysql_query function..
		$this->result = @mysql_query($query,$this->dbh);

		// If there is an error then take note of it..
		if ( $str = @mysql_error($this->dbh) ) {
			$is_insert = true;
			$this->register_error($str);
			$this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
			return false;
		}

		// Query was an insert, delete, update, replace
		$is_insert = false;
		if ( preg_match("/^(insert|delete|update|replace)\s+/i",$query) ) {
			$this->rows_affected = @mysql_affected_rows();

			// Take note of the insert_id
			if ( preg_match("/^(insert|replace)\s+/i",$query) ) {
				$this->insert_id = @mysql_insert_id($this->dbh);
			}

			// Return number of rows affected
			$return_val = $this->rows_affected;
		}
		// Query was a select
		else {

			// Take note of column info
			$i=0;
			while ($i < @mysql_num_fields($this->result)) {
				$this->col_info[$i] = @mysql_fetch_field($this->result);
				$i++;
			}

			// Store Query Results
			$num_rows=0;
			while ( $row = @mysql_fetch_object($this->result) ) {
				// Store relults as an objects within main array
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}

			@mysql_free_result($this->result);

			// Log number of rows the query returned
			$this->num_rows = $num_rows;

			// Return number of rows selected
			$return_val = $this->num_rows;
		}

		// disk caching of queries
		$this->store_cache($query,$is_insert);

		// If debug ALL queries
		$this->trace || $this->debug_all ? $this->debug() : null ;

		return $return_val;

	}

	/**********************************************************************
	*  Print SQL/DB error - over-ridden by specific DB class
	*/

	public function register_error($err_str) {
		// Keep track of last error
		$this->last_error = $err_str;

		// Capture all errors to an error array no matter what happens
		$this->captured_errors[] = array(
			'error_str' => $err_str,
			'query'     => $this->last_query);
	}

	/**********************************************************************
	*  Turn error handling on or off..
	*/

	public function show_errors() {
		$this->show_errors = true;
	}

	public function hide_errors() {
		$this->show_errors = false;
	}

	/**********************************************************************
	*  Kill cached query results
	*/

	public function flush() {
		// Get rid of these
		$this->last_result = null;
		$this->col_info = null;
		$this->last_query = null;
		$this->from_disk_cache = false;
	}

	/**********************************************************************
	*  Get one variable from the DB - see docs for more detail
	*/

	public function get_var($query=null,$x=0,$y=0) {

		// Log how the function was called
		$this->func_call = "\$db->get_var(\"$query\",$x,$y)";

		// If there is a query then perform it if not then use cached results..
		if ( $query ) {
			$this->query($query);
		}

		// Extract var out of cached results based x,y vals
		if ( $this->last_result[$y] ) {
			$values = array_values(get_object_vars($this->last_result[$y]));
		}

		// If there is a value return it else return null
		return (isset($values[$x]) && $values[$x]!=='')?$values[$x]:null;
	}

	/**********************************************************************
	*  Get one row from the DB - see docs for more detail
	*/

	public function get_row($query=null,$output=OBJECT,$y=0) {

		// Log how the function was called
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";

		// If there is a query then perform it if not then use cached results..
		if ( $query ) {
			$this->query($query);
		}

		// If the output is an object then return object using the row offset..
		if ( $output == OBJECT ) {
			return $this->last_result[$y]?$this->last_result[$y]:null;
		}
		// If the output is an associative array then return row as such..
		elseif ( $output == ARRAY_A ) {
			return $this->last_result[$y]?get_object_vars($this->last_result[$y]):null;
		}
		// If the output is an numerical array then return row as such..
		elseif ( $output == ARRAY_N ) {
			return $this->last_result[$y]?array_values(get_object_vars($this->last_result[$y])):null;
		}
		// If invalid output type was specified..
		else {
			$this->print_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
		}

	}

	/**********************************************************************
	*  Function to get 1 column from the cached result set based in X index
	*  see docs for usage and info
	*/

	public function get_col($query=null,$x=0) {

		// If there is a query then perform it if not then use cached results..
		if ( $query ) {
			$this->query($query);
		}

		// Extract the column values
		for ( $i=0; $i < count($this->last_result); $i++ ) {
			$new_array[$i] = $this->get_var(null,$x,$i);
		}

		return $new_array;
	}


	/**********************************************************************
	*  Return the the query as a result set - see docs for more details
	*/

	public function get_results($query=null, $output = OBJECT) {

		// Log how the function was called
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		// If there is a query then perform it if not then use cached results..
		if ( $query ) {
			$this->query($query);
		}

		// Send back array of objects. Each row is an object
		if ( $output == OBJECT ) {
			return $this->last_result;
		}
		elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
			if ( $this->last_result ) {
				$i=0;
				foreach( $this->last_result as $row ) {

					$new_array[$i] = get_object_vars($row);

					if ( $output == ARRAY_N ) {
						$new_array[$i] = array_values($new_array[$i]);
					}

					$i++;
				}

				return $new_array;
			}
			else {
				return null;
			}
		}
	}


	/**********************************************************************
	*  Function to get column meta data info pertaining to the last query
	* see docs for more info and usage
	*/

	public function get_col_info($info_type="name",$col_offset=-1) {

		if ( $this->col_info ) {
			if ( $col_offset == -1 ) {
				$i=0;
				foreach($this->col_info as $col ) {
					$new_array[$i] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			}
			else {
				return $this->col_info[$col_offset]->{$info_type};
			}

		}

	}

	/**********************************************************************
	*  store_cache
	*/

	public function store_cache($query,$is_insert) {

		// The would be cache file for this query
		$cache_file = $this->cache_dir.'/'.md5($query).'.sql.php';

		// disk caching of queries
		if ( $this->use_disk_cache && ( $this->cache_queries && ! $is_insert ) || ( $this->cache_inserts && $is_insert )) {
			if ( ! is_dir($this->cache_dir) ) {
				$this->register_error("Could not open cache dir: $this->cache_dir");
				$this->show_errors ? trigger_error("Could not open cache dir: $this->cache_dir",E_USER_WARNING) : null;
			}
			else {
				// Cache all result values
				$result_cache = array(
					'col_info' => $this->col_info,
					'last_result' => $this->last_result,
					'num_rows' => $this->num_rows,
					'return_value' => $this->num_rows);
				error_log ( serialize($result_cache), 3, $cache_file);
			}
		}

	}

	/**********************************************************************
	*  get_cache
	*/

	public function get_cache($query) {

		// The would be cache file for this query
		$cache_file = $this->cache_dir.'/'.md5($query).'.sql.php';

		// Try to get previously cached version
		if ( $this->use_disk_cache && file_exists($cache_file) ) {
			// Only use this cache file if less than 'cache_timeout' (hours)
			if ( (time() - filemtime($cache_file)) > ($this->cache_timeout*3600) ) {
				unlink($cache_file);
			}
			else {
				$result_cache = unserialize(file_get_contents($cache_file));

				$this->col_info = $result_cache['col_info'];
				$this->last_result = $result_cache['last_result'];
				$this->num_rows = $result_cache['num_rows'];

				$this->from_disk_cache = true;

				// If debug ALL queries
				$this->trace || $this->debug_all ? $this->debug() : null ;

				return $result_cache['return_value'];
			}
		}

	}

	/**********************************************************************
	*  Dumps the contents of any input variable to screen in a nicely
	*  formatted and easy to understand way - any type: Object, Var or Array
	*/

	public function vardump($mixed='') {

		// Start outup buffering
		ob_start();

		echo "<p><table><tr><td bgcolor=ffffff><blockquote><font color=000090>";
		echo "<pre><font face=arial>";

		if ( ! $this->vardump_called ) {
			echo "<font color=800080><b>ezSQL</b> (v",EZSQL_VERSION,") <b>Variable Dump..</b></font>\n\n";
		}

		$var_type = gettype ($mixed);
		print_r(($mixed?$mixed:"<font color=red>No Value / False</font>"));
		echo "\n\n<b>Type:</b> ",ucfirst($var_type),"\n";
		echo "<b>Last Query</b> [",$this->num_queries,"]<b>:</b> ",($this->last_query?$this->last_query:"NULL"),"\n";
		echo "<b>Last Function Call:</b> ",($this->func_call?$this->func_call:"None"),"\n";
		echo "<b>Last Rows Returned:</b> ",count($this->last_result),"\n";
		echo "</font></pre></font></blockquote></td></tr></table>";
		echo "\n<hr size=1 noshade color=dddddd>";

		// Stop output buffering and capture debug HTML
		$html = ob_get_contents();
		ob_end_clean();

		// Only echo output if it is turned on
		if ( $this->debug_echo_is_on ) {
			echo $html;
		}

		$this->vardump_called = true;

		return $html;

	}

	/**********************************************************************
	*  Displays the last query string that was sent to the database & a
	* table listing results (if there were any).
	* (abstracted into a seperate file to save server overhead).
	*/

	public function debug() {

		// Start outup buffering
		ob_start();

		echo "<blockquote>";

		// Only show ezSQL credits once..
		if ( ! $this->debug_called ) {
			echo "<font color=800080 face=arial size=2><b>ezSQL</b> (v",EZSQL_VERSION,") <b>Debug..</b></font><p>\n";
		}

		if ( $this->last_error ) {
			echo "<font face=arial size=2 color=000099><b>Last Error --</b> [<font color=000000><b>",$this->last_error,"</b></font>]<p>";
		}

		if ( $this->from_disk_cache ) {
			echo "<font face=arial size=2 color=000099><b>Results retrieved from disk cache</b></font><p>";
		}

		echo "<font face=arial size=2 color=000099><b>Query</b> [$this->num_queries] <b>--</b> ";
		echo "[<font color=000000><b>$this->last_query</b></font>]</font><p>";

		echo "<font face=arial size=2 color=000099><b>Query Result..</b></font>";
		echo "<blockquote>";

		if ( $this->col_info ) {

			// =====================================================
			// Results top rows

			echo "<table cellpadding=5 cellspacing=1 bgcolor=555555>";
			echo "<tr bgcolor=eeeeee><td nowrap valign=bottom><font color=555599 face=arial size=2><b>(row)</b></font></td>";


			for ( $i=0; $i < count($this->col_info); $i++ ) {
				echo "<td nowrap align=left valign=top><font size=1 color=555599 face=arial>{$this->col_info[$i]->type} {$this->col_info[$i]->max_length}</font><br><span style='font-family: arial; font-size: 10pt; font-weight: bold;'>{$this->col_info[$i]->name}</span></td>";
			}

			echo "</tr>";

			// ======================================================
			// print main results

			if ( $this->last_result ) {

				$i=0;
				foreach ( $this->get_results(null,ARRAY_N) as $one_row ) {
					$i++;
					echo "<tr bgcolor=ffffff><td bgcolor=eeeeee nowrap align=middle><font size=2 color=555599 face=arial>$i</font></td>";

					foreach ( $one_row as $item ) {
						echo "<td nowrap><font face=arial size=2>$item</font></td>";
					}

					echo "</tr>";
				}

			} // if last result
			else {
				echo "<tr bgcolor=ffffff><td colspan=",(count($this->col_info)+1),"><font face=arial size=2>No Results</font></td></tr>";
			}

			echo "</table>";

		} // if col_info
		else {
			echo "<font face=arial size=2>No Results</font>";
		}

		echo "</blockquote></blockquote><hr noshade color=dddddd size=1>";

		// Stop output buffering and capture debug HTML
		$html = ob_get_contents();
		ob_end_clean();

		// Only echo output if it is turned on
		if ( $this->debug_echo_is_on ) {
			echo $html;
		}

		$this->debug_called = true;

		return $html;

	}

}


$scdb = new scdb(DB_USER, DB_PASS, DB_NAME, DB_HOST);

?>