<?php
class OddRecord extends Odd {
    public $body;

    function __construct($body) {
        $this->body = $body;
        // $this->build();
    }

    function update($todd, $date, $winner) {
        $sql = 'select id from oddsRecord where odd=' . $todd . ' and date="' . $date . '"'; // echo "OddRecord::update $sql<br>";
        $res = $this->body->db->query($sql);
        $num = $this->body->db->num_rows();
        
        if(!$num) {
            $winLoss = $winner ? 'seqWins=1, seqLosses=0' : 'seqWins=0, seqLosses=1';
            $sql = 'insert into oddsRecord set odd=' . $todd . ', ' . $winLoss . ', date="' . $date . '"'; // echo "OddRecord::update $sql<br>";
            $this->body->db->query($sql);	
        } else {
            $row = $res->fetch_assoc();
            $id = $row['id'];

            $sql = 'select seqWins, seqLosses from oddsRecord where odd=' . $todd . ' and date<"' . $date . '" order by date desc limit 1';
            $res = $this->body->db->query($sql);
            $num = $this->body->db->num_rows();

            if ($num) {
                $row = $res->fetch_assoc();
                $seqWins = ++$row['seqWins'];
                $seqLosses = ++$row['seqLosses'];
            } else {
                $seqWins = 1;
                $seqLosses = 1;
            }

            $winLoss = $winner ? 'seqWins=' . $seqWins . ', seqLosses=0' : 'seqWins=0, seqLosses=' . $seqLosses; 
            $sql = 'update oddsRecord set ' . $winLoss . ' where id=' . $id; // echo "OddRecord::updateRecord $sql<br>";
            $this->body->db->query($sql);
        }
    }

    function build() {
        $sql = 'delete from oddsRecord';
        $this->body->db->query($sql);

        $sql = 'select date, todd1, todd2, winner from matches where final=1 order by id'; // echo "OddRecord::build $sql<br><br>";
        $res = $this->body->db->query($sql);
        while($row = $res->fetch_assoc()) {
            $date = $row['date'];
            $todd1 = $row['todd1'];
            $todd2 = $row['todd2'];
            $winner = $row['winner'];
            
            $this->update($todd1, $date, $winner == 1);
            $this->update($todd2, $date, $winner == 2);
        }
    }
}
?>