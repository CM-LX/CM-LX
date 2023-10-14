<?php
    function teamSetMatches($id, $body) {
        $matches = $body->team->getNumMatches($id);

        $sql = 'update teams set matches=' . $matches . ' where id=' . $id;
        $body->db->query($sql);
    }
?>