<?php
    function teamBuildGains($body) {
		// TODO verificar esta função

		$sql = 'select id from teams';
        $res = $body->db->query($sql);
        while($row = $res->fetch_assoc()) {
			$id = $row['id'];
			$body->team->updateGains($id);
		}
	}
?>