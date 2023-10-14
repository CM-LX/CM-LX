<?php
class Db {
	private $connection;
	private $result;
	private $script = 'Db.nba.php';
	private $class = null;
	private $function = null;

	function __construct() {
		$this->connect();
		$this->select_db();
	}

	function connect() {
		$this->connection = new mysqli('localhost', 'sxhd01ne_nba', 'BetanoNba!2023');
		if(mysqli_connect_errno()) {
			$error = new MyError;
			$error->script = $this->script;
			$error->line = 19;
			$error->msg = 'Error connecting to database
class: ' . $this->class . '
function: ' . $this->function;
			$error->send(true);
			exit();
		}
	}

	function select_db() {
		$dbSelect = $this->connection->select_db('sxhd01ne_nba');
		if(!$dbSelect) {
			$error = new MyError;
			$error->script = $this->script;
			$error->line = 31;
			$error->msg = 'Error selecting the database
class: ' . $this->class . '
function: ' . $this->function;
			$error->send(true);
			exit();
		}
	}

	function query($sql) {
		$result = $this->connection->query($sql);

		$bt = debug_backtrace();
		$class = $bt[0]['object']->class;
		// $function = count($bt) > 0 ? $bt[1]['function'] : '';

		// $btj = count($bt) > 0 ? json_encode($bt[1]['function']) : '';

		if(!$result) {
			$error = new MyError;
			$error->script = $this->script;
			$error->line = 44;
			$error->msg = 'Error executing query
****************
' . $sql . '
****************

class: ' . $class . '
function: ' . $function;
			$error->send(true);
			exit();
		}

		$this->result = $result;

		return $result;
	}

	function num_rows() {
		return $this->result->num_rows;
	}

	function last_id() {
		return $this->connection->insert_id;
	}

	function __deconstruct()
	{
		$this->result->free();
		$disconnect	= $this->connection->close();
		if(!$disconnect) {
			$error = new MyError;
			$error->script = $this->script;
			$error->line = 72;
			$error->msg = 'Error closing database connection';
			exit();
		}
	}
}
?>