<?php
function teamGetGainsMinMax($id, $body, $minMax) {
    $res = [];

    $sql = 'select count(id) as matches from matches where final=1 && ((bet' . $minMax . '1>0 && team1=' . $id . ') || (bet' . $minMax . '2>0 && team2=' . $id . '))';
    $res = $body->db->query($sql);
    $row = $res->fetch_assoc();
    $matches = $row['matches'];
    
    $sql = 'select count(id) as wins1 from matches where final=1 && bet' . $minMax . '1>0 && team1=' . $id . ' && winner=1';
    $res = $body->db->query($sql);
    $row = $res->fetch_assoc();
    $wins1 = $row['wins1'];
    
    $sql = 'select count(id) as wins2 from matches where final=1 && bet' . $minMax . '2>0 && team2=' . $id . ' && winner=2';
    $res = $body->db->query($sql);
    $row = $res->fetch_assoc();
    $wins2 = $row['wins2'];
    
    $sql = 'select sum(odd1) as s1 from matches where bet' . $minMax . '1>0 && final=1 && team1=' . $id . ' && winner=1';
    $res = $body->db->query($sql);
    $row = $res->fetch_assoc();
    $s1 = $row['s1'];

    $sql = 'select sum(odd2) as s2 from matches where bet' . $minMax . '2>0 && final=1 && team2=' . $id . ' && winner=2';
    $res = $body->db->query($sql);
    $row = $res->fetch_assoc();
    $s2 = $row['s2'];
    
    $wins = $wins1 + $wins2;
    $success = floor(100 * $wins / $matches);
    $gains = $s1 + $s2;

    $data['matches'] = $matches;
    $data['wins'] = $wins;
    $data['success'] = $success;
    $data['gains'] = $gains;

    return $data;
}
?>