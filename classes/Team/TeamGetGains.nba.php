<?php
    function teamGetGains($id, $body) {
        $matches = $body->team->getNumMatches($id);

        // total gross gains if always betting for this team
    
        $sql = 'select sum(odd1) as s from matches where team1=' . $id . ' and winner=1';
        $res = $body->team->body->db->query($sql);
        $row = $res->fetch_assoc();
        $s1 = $row['s'];
    
        $sql = 'select sum(odd2) as s from matches where team2=' . $id . ' and winner=2';
        $res = $body->team->body->db->query($sql);
        $row = $res->fetch_assoc();
        $s2 = $row['s'];

        $netGainsAsPro = $s1 + $s2 - $matches;

        // total gross gains if always betting against this team
    
        $sql = 'select sum(odd2) as s from matches where team1=' . $id . ' and winner=2';
        $res = $body->db->query($sql);
        $row = $res->fetch_assoc();
        $s1 = $row['s'];
    
        $sql = 'select sum(odd1) as s from matches where team2=' . $id . ' and winner=1';
        $res = $body->db->query($sql);
        $row = $res->fetch_assoc();
        $s2 = $row['s'];

        $netGainsAsCon = $s1 + $s2 - $matches;

        return [$netGainsAsPro, $netGainsAsCon];
    }
?>