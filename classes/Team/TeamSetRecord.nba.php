<?php
    function teamSetRecord($id, $date, $body) {
		$sql ='select team1, team2, winner from matches where date="' . $date . '" and (team1=' . $id . ' or team2=' . $id . ')';
		$res = $body->db->query($sql);
		$row = $res->fetch_assoc();
		$team1 = $row['team1'];
		$team2 = $row['team2'];
		$winner = $row['winner'];

		$sql = 'select seqWins, seqLosses from teamsRecord where team=' . $id . ' and date<"' . $date . '" order by date desc limit 1';
		$res = $body->db->query($sql);
		$num = $body->db->num_rows();

		if($num) {
			$row = $res->fetch_assoc();
			$seqWins = $row['seqWins'];
			$seqLosses = $row['seqLosses'];
		} else {
			$seqWins = 0;
			$seqLosses = 0;
		}

		$wins = ($team1 == $id) && ($winner == 1) ? ++$seqWins : 0;
		$wins = ($team2 == $id) && ($winner == 2) ? ++$seqWins : $wins;
		$losses = ($team1 == $id) && ($winner == 2) ? ++$seqLosses : 0;
		$losses = ($team2 == $id) && ($winner == 1) ? ++$seqLosses : $losses;

		$sql = 'select id from teamsRecord where team=' . $id . ' and date="' . $date . '"';
		$res = $body->db->query($sql);
		$num = $body->db->num_rows();

		if($num) {
			$sql = 'update teamsRecord set seqWins=' . $wins .', seqLosses=' . $losses. ' where team=' . $id . ' and date="' . $date . '"';
			$body->db->query($sql);
		} else {
			$sql = 'insert into teamsRecord set team=' . $id. ', date="'. $date .'", seqWins=' . $wins .', seqLosses=' . $losses;
			$body->db->query($sql);
		}
	}
?>