<?php
	function teamGetNumMatches($id, $body) {
        $sql = 'select count(id) as c from matches where final=1 and (team1=' . $id . ' or team2=' . $id . ')'; // echo "$sql<br><br>";
        $res = $body->db->query($sql);
        $row = $res->fetch_assoc();
        $c = $row['c'];
        return $c;
    }
?>