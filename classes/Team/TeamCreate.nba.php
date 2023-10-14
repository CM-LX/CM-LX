<?php
    function teamCreate($name, $body) {
		if(!$body->cm) return;

        $sql = 'insert into teams set name="' . $name . '"';
        $body->db->query($sql);	
    }
?>