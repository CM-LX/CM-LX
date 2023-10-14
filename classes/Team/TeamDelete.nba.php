<?php
    function teamDelete($id, $body) {
		if(!$body->cm) return;

        $sql = 'delete from teams where id=' . $id; // echo "$sql<br>";
        $body->db->query($sql);
    }
?>