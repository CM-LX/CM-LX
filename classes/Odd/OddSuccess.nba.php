<?php
class OddSuccess extends Odd {
    public $body;

    function __construct($body) {
        $this->body = $body;
        // $this->build();
    }

    function get($odd) {
        $sql = 'select success from odds where odd=' . $odd; // echo "OddSuccess::get $sql<br>";
        $res = $this->body->db->query($sql);
		$row = $res->fetch_assoc();
		$success = $row['success'];

        return $success;
    }

    function update($odd) {
        $matches = $this->getNumMatches($odd);
        
        $wins = $this->getWins($odd); 
        
        $success = floor(100 * $wins / $matches);

        $sql = 'select id from odds where odd=' . $odd;
        $this->body->db->query($sql);
        $num = $this->body->db->num_rows();

        if ($num) {
            $sql = 'update odds set matches=' . $matches . ', wins=' . $wins . ', success=' . $success . ' where odd=' . $odd;
        } else {
            $sql = 'insert into odds set odd=' . $odd . ', matches=1, wins=' . $wins . ', success=' . $success;
        }
        $this->body->db->query($sql);
        // DEBUG
        // echo "OddSuccess::update $sql<br>";
    }

    function build() {
        $sql = 'select odd from odds';
        $res = $this->body->db->query($sql);
        while($row = $res->fetch_assoc()) {
			$odd = $row['odd'];
			$this->update($odd);
		}
	}
}
?>