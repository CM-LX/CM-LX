<?php
	function teamSetGains($id, $body) {
        [$netGainsAsPro, $netGainsAsCon] = $body->team->getGains($id);
		$sql = 'update teams set gainsPro=' . $netGainsAsPro . ', gainsCon=' . $netGainsAsCon . ' where id=' . $id; // echo "$sql<br>";
		$body->db->query($sql);
	}
?>