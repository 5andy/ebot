<?php
// 2016 Dave Williams. All rights reserved.
// Use of this code without permission from the owner will result in us getting a bit shirty.
// Created By: Dave Williams | d4v3w

class database {

	var $link;
	var $result;
	var $debug = true;

	public function database($debug = false) {
		$this->debug = $debug;
		require_once('config.inc.php');
		$this->link = mysqli_connect($GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_NAME'], $GLOBALS['DB_PORT']);
	}

	public function query($query) {
		if ($this->result = mysqli_query($this->link, $query)) {
			if ($this->debug) echo($query.'<br />');
			return true;
		} else {
			if ($this->debug) echo('MySQL query failed: '.$query.', MySQL said: '.mysqli_error().'<br />');
			return false;
		}
	}

	public function fetchArray() {
		if ($Row = mysqli_fetch_assoc($this->result)) {
			return $Row;
		} else {
			if ($this->debug) echo('MySQL fetchArray() failed, MySQL said: '.mysqli_error().'<br />');
			return false;
		}
	}

	public function numRows() {
		$numRows = 0;
		if (mysqli_num_rows($this->result)) {
			$numRows = mysqli_num_rows($this->result);
			if ($numRows == NULL || $numRows == '' || !is_numeric($numRows)) $numRows = 0;
		}
		return $numRows;
	}

	public function affectedRows() {
		if (($affectedRows = mysqli_affected_rows($this->link)) >= 0) {
			return $affectedRows;
		} else {
			if ($this->debug) echo('MySQL affectedRows() failed, MySQL said: '.mysqli_error().'<br />');
			return false;
		}
	}

	public function closeConnection() {
		mysqli_close($this->link);
	}
}
?>