<?php

class SCMS_Config {

	public function __construct($file='config.ini') {
		$this->parse($file);
	}
	
	public function parse($file) {
		$file = realpath(ROOT . $file);
		if(!file_exists($file)) {
			return false;
		}
		$this->file = basename($file);
		if(!$this->config = parse_ini_file($file, true)) {
			return false;
		}
	}
	
	public function save($config) {
		if(!is_array($config) || count($config) === 0) {
			return false;
		}
		
	}

}

$_CONFIG = new SCMS_Config;

?>