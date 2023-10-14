<?php
    function teamBuildWins($body) {



		$sql = 'select id from teams';
        $res = $body->db->query($sql);
        while($row = $res->fetch_assoc()) {
			$id = $row['id'];
			$body->team->setWins($id);
		}
	}

?>