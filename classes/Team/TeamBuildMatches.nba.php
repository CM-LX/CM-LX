<?php
    function teamBuildMatches($body) {
		include_once('Team/TeamSetMatches.nba.php');

		$sql = 'select id from teams';
        $res = $body->db->query($sql);
        while($row = $res->fetch_assoc()) {
			$id = $row['id'];
			teamSetMatches($id, $body);
		}
	}
?>