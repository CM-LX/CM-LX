<?php
    function teamGetSelectList($team, $body) {
        $list = '';
        $sql = 'select id, name from teams order by name';
        $res = $body->db->query($sql);
        while($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $name = $row['name'];
            $selected = ($id == $team) ? ' selected' : '';
            $list .= '
                <option value="' . $id . '"' . $selected . '>' . $name . '</option>';
            // echo "$team $id $selected<br>";
        };

		return $list;
    }

?>