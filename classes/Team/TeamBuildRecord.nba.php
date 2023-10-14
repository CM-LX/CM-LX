<?php
    function teamBuildRecord($body) {
		$sql = 'delete from teamsRecord';
		$body->db->query($sql);

		$sql = 'select date from matches where final=1 group by date order by date';
		$res = $body->db->query($sql);
		while($row = $res->fetch_assoc()) {
			$date = $row['date'];
			// echo "*********************************** DATE: $date<br>";

			$sql ='select id from matches where date="' . $date . '"';
			$res2 = $body->db->query($sql);
			while($row2 = $res2->fetch_assoc()) {
				$id = $row2['id'];
				
                $date = $body->match->getField($id, 'date');
                $team1 = $body->match->getField($id, 'team1');
                $team2 = $body->match->getField($id, 'team2');
                // $winner = $body->match->getField($id, 'winner');;

				// [$seqWins, $seqLosses] = $body->team->getSequence($team1, $date);
				// $wins = $winner == 1 ? ++$seqWins : 0;
				// $losses = $winner == 2 ? ++$seqLosses : 0;
				$body->team->setRecord($team1, $date);

				// [$seqWins, $seqLosses] = $body->team->getSequence($team2, $date);
				// $wins = $winner == 2 ? ++$seqWins : 0;
				// $losses = $winner == 1 ? ++$seqLosses : 0;
				// $body->team->setRecord($team2, $date, $wins, $losses);
				$body->team->setRecord($team2, $date);
			}
		}
	}
?>