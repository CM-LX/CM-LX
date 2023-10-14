<?
class dbClass {
	public function __construct() {
		$this->notified = false;
		$this->script = 'dbClass.nba.php';
		$this->connection = new mysqli('localhost', 'sxhd01ne_nba', 'BetanoNba!2023');
		if(mysqli_connect_errno() && !$this->notified) {
			$error = new errorClass;
			$error->script = $this->script;
			$error->line = 11;
			$error->msg = 'Error connecting to database';
			$error->send(true);
			$this->notified = true;
		} else {
			$dbSelect = $this->connection->select_db('sxhd01ne_nba');
			if(!$dbSelect && !$this->notified) {
				$error = new errorClass;
				$error->script = $this->script;
				$error->line = 20;
				$error->msg = 'Error selecting the database';
				$error->send(true);
				$this->notified = true;
			} else {
				return $this->connection;
			}
		}
	}

	public function query($sql) {
		if($sql) {
			$this->result = $this->connection->query($sql);
		} else {
			$error = new errorClass;
			$error->script = $this->script;
			$error->line = 36;
			$error->msg = 'DB: empty query: |' . $sql . '|';
			$error->send(true);
			$this->notified = true;
		}

		if($this->result) {
			return $this->result;
		} else {
			$error = new errorClass;
			$error->script = $this->script;
			$error->line = 47;
			$error->msg = 'Error executing query
****************
' . $sql . '
****************';
			$error->send(true);
			$this->notified = true;
		}
	}

	public function num_rows() {
		return $this->result->num_rows;
	}

	public function last_id() {
		return $this->connection->insert_id;
	}

	public function __deconstruct()
	{
		$this->result->free();
		$disconnect	= $this->connection->close();
		if(!$disconnect) {
			$error = new erroClass;
			$error->script = $this->script;
			$error->line = 72;
			$error->msg = 'Error closing database connection';
			$error->send(true);
		} else {
			return $disconnect;
		}
	}
}
?>