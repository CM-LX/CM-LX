<?php
function betBuild($body) {
    $sql = 'update matches set betMin1=0, betMin2=0, betMax1=0, betMax2=0'; echo "Bet::build $sql<br>";
    $body->db->query($sql);
    $sql = 'select date from matches group by date order by date'; // echo "Bet::build $sql<br>";
    $res = $body->db->query($sql);
    while($row = $res->fetch_assoc()) {
        $date = $row['date'];
        $sql = 'select id, todd1, todd2 from matches where date="' . $date . '"'; echo "Bet::build $sql<br>";
        $res2 = $body->db->query($sql);
        while($row2 = $res2->fetch_assoc()) {
            $id = $row2['id'];
            $todd1 = $row2['todd1'];
            $todd2 = $row2['todd2'];
            $sql = 'select id from matches where id!=' . $id . ' && date="' . $date . '" && (todd1=' . $todd1 . ' || todd1=' . $todd2 . ' || todd2=' . $todd1 . ' || todd2=' . $todd2 . ')'; echo "Bet::build $sql<br>";
            $body->db->query($sql);
            if ($body->db->num_rows()) continue;

            $body->betCalculate->calculate($id);
        }
    }
}
?>