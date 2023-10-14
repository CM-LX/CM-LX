<?php
    function teamSetName($id, $name, $body) {
		if(!$body->cm) return;

		$sql = 'update teams set name="' . $name . '" where id=' . $id;
		$body->db->query($sql);
    }
?>