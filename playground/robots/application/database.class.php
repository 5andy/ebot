<?php
// 2016 Dave Williams. All rights reserved.
// Use of this code without permission from the owner will result in us getting a bit shirty.
// Created By: Dave Williams | d4v3w

class database {

	var $config;
	var $link;
	var $result;
	var $debug = false;

	function __construct($debug = false) {
		$this->debug = $debug;
		$config = array();
		require_once('config.inc.php');
		if (is_array($config) and count($config) >= 4) {
			$this->config = $config;
			$this->link = mysql_connect($this->config['HOST'], $this->config['USERNAME'], $this->config['PASSWORD']);
		} else {
			if ($this->debug) echo('Please enter database credentials.<br />');
		}
	}

	public function query($query) {
		if ($this->result = mysql_db_query($this->config['DBNAME'], $query, $this->link)) {
			if ($this->debug) echo($query.'<br />');
			return true;
		} else {
			if ($this->debug) echo('MySQL query failed: '.$query.', MySQL said: '.mysql_error().'<br />');
			return false;
		}
	}

	public function fetchArray() {
		if ($Row = mysql_fetch_assoc($this->result)) {
			return $Row;
		} else {
			if ($this->debug) echo('MySQL fetchArray() failed, MySQL said: '.mysql_error().'<br />');
			return false;
		}
	}

	public function numRows() {
		$numRows = 0;
		if (mysql_num_rows($this->result)) {
			$numRows = mysql_num_rows($this->result);
			if ($numRows == NULL || $numRows == '' || !is_numeric($numRows)) $numRows = 0;
		}
		return $numRows;
	}

	public function affectedRows() {
		if (($affectedRows = mysql_affected_rows($this->link)) >= 0) {
			return $affectedRows;
		} else {
			if ($this->debug) echo('MySQL affectedRows() failed, MySQL said: '.mysql_error().'<br />');
			return false;
		}
	}
}
?>