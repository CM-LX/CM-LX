<?php
    function teamSetSuccess($id, $body) {
        $matches = $body->team->getNumMatches($id);

		$sql = 'select count(id) as c from matches where (team1=' . $id . ' and winner=1) or (team2=' . $id . ' and winner=2)';
		$res = $body->db->query($sql);
		$row = $res->fetch_assoc();
		$wins = $row['c'];

		$success = round(100 * $wins / $matches);

		$sql = 'update teams set matches=' . $matches . ', wins=' . $wins . ', success=' . $success . ' where id=' . $id; // echo "$sql<br>";
		$body->db->query($sql);
	}
?>