<?php
	function teamGetSequence($team, $date, $body) {
        $seqWins = 0;
        $seqLosses = 0;

        $sql = 'select seqWins, seqLosses from teamsRecord where team=' . $team . ' and date<"' . $date . '" order by date desc limit 1'; // echo "$sql<br>";
        $res = $body->db->query($sql);
        $num = $body->db->num_rows();
        
        if ($num) {
            $row = $res->fetch_assoc();
            $seqWins = $row['seqWins'];
            $seqLosses = $row['seqLosses'];
        }  

        return [$seqWins, $seqLosses]; 
    }
?>