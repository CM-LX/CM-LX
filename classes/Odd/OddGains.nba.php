<?php
class OddGains extends Odd {
    public $body;

    function __construct($body) {
        $this->body = $body;
    }

    function get($todd) {
        $matches = $this->getNumMatches($todd);

        $netGainsAsPro = $this->getGrossGainsAsPro($todd) - $matches;
        $netGainsAsCon = $this->getGrossGainsAsCon($todd) - $matches;

        return [$netGainsAsPro, $netGainsAsCon];
    }

    function getGrossGainsAsPro($todd) {

        // total gross gains if always betting for this odd

        $sql = 'select sum(odd1) as s from matches where todd1=' . $todd . ' and winner=1'; // echo "OddGains::getGrossGainsAsPro $sql<br>";
        $res = $this->body->db->query($sql);
        $row = $res->fetch_assoc();
        $s1 = $row['s'];

        $sql = 'select sum(odd2) as s from matches where todd2=' . $todd . ' and winner=2'; // echo "OddGains::getGrossGainsAsPro $sql<br>";
        $res = $this->body->db->query($sql);
        $row = $res->fetch_assoc();
        $s2 = $row['s'];

        $gains = $s1 + $s2;

        return $gains;
    }

    function getGrossGainsAsCon($todd) {

        // total gross gains if always betting against this odd

        $sql = 'select sum(odd2) as s from matches where todd1=' . $todd . ' and winner=2'; // echo "OddGains::getGrossGainsAsCon $sql<br>";
        $res = $this->body->db->query($sql);
        $row = $res->fetch_assoc();
        $s1 = $row['s'];

        $sql = 'select sum(odd1) as s from matches where todd2=' . $todd . ' and winner=1'; // echo "OddGains::getGrossGainsAsCon $sql<br>";
        $res = $this->body->db->query($sql);
        $row = $res->fetch_assoc();
        $s2 = $row['s'];

        $gains = $s1 + $s2;

        return $gains;
    }

    function update($todd) {

        // DEBUG
        // echo "OddGaind::update<br>";

        $sql = 'select id from odds where odd=' . $todd; // echo "$sql<br>";
		$this->body->db->query($sql);
        $num = $this->body->db->num_rows();

        [$netGainsAsPro, $netGainsAsCon] = $this->get($todd);

        if ($num) {
            $sql = 'update odds set gainsPro=' . $netGainsAsPro . ', gainsCon=' . $netGainsAsCon . ' where odd=' . $todd; 
        } else {
            $sql = 'insert into odds set odd=' . $todd . ', gainsPro=' . $netGainsAsPro . ', gainsCon=' . $netGainsAsCon;
        }
		$this->body->db->query($sql);
        echo "OddGains::update $sql<br>";

        // DEBUG
        // echo "OddGains::update $sql<br>";
	}

    function build() {

        // DEBUG
        // echo "OddGains::build<br>";

        $sql = 'select odd from odds';
        $res = $this->body->db->query($sql);
        while($row = $res->fetch_assoc()) {
			$odd = $row['odd'];
			$this->update($odd);
		}

        // DEBUG
        // echo "OddGains::build<br>";
    }
}
?>