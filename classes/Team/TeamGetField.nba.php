<?php
	function teamGetField($id, $field, $body) { 
        $sql = 'select ' . $field . ' from teams where id=' . $id; // echo "Team::get $sql<br>";
        $res = $body->db->query($sql);
        $row = $res->fetch_assoc();
        $data = $row[$field];

		return $data;
	}
?>