<?php
	function teamGetHistoryByDate($id, $date, $titleLevel, $body) {
		$html = '
<!-- Team::getHistoryByDate -->
';
        $betsMax1 = 0;
        $betsMax2 = 0;

        $sql2 = 'select count(id) as c from matches where final=1 && date like "' . $date . '%" && (team1=' . $id . ' || team2=' . $id . ')'; // echo "Team::getHistory $sql2<br>";
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $matches = $row2['c'];
            
        $sql2 = 'select count(id) as c from matches where final=1 && date like "' . $date . '%" && ((team1=' . $id . ' && winner=1 ) || (team2=' . $id . ') && winner=2)'; // echo "Team::getHistory $sql2<br>";
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $wins = $row2['c'];

        $success = floor(100 * $wins / $matches);

        $sql2 = 'select sum(odd1) as s1 from matches where final=1 && date like "' . $date . '%" && team1=' . $id . ' && winner=1';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $s1 = $row2['s1'];

        $sql2 = 'select sum(odd2) as s2 from matches where final=1 && date like "' . $date . '%" && team2=' . $id . ' && winner=2';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $s2 = $row2['s2'];

        $gains = $s1 + $s2 - $matches;
        $roi = round(100 * $gains / $matches);
        
        $sql2 = 'select count(id) as matchesMin from matches where final=1 && date like "' . $date . '%" && ((betMin1>0 && team1=' . $id . ') || (betMin2>0 && team2=' . $id . '))';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $matchesMin = $row2['matchesMin'];

        $sql2 = 'select count(id) as winsMin1 from matches where final=1 && date like "' . $date . '%" && betMin1>0 && team1=' . $id . ' && winner=1';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $winsMin1 = $row2['winsMin1'];

        $sql2 = 'select count(id) as winsMin2 from matches where final=1 && date like "' . $date . '%" && betMin2>0 && team2=' . $id . ' && winner=2';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $winsMin2 = $row2['winsMin2'];

        $sql2 = 'select sum(odd1) as sMin1 from matches where betMin1>0 && final=1 && date like "' . $date . '%" && team1=' . $id . ' && winner=1';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $sMin1 = $row2['sMin1'];

        $sql2 = 'select sum(odd2) as sMin2 from matches where betMin2>0 && final=1 && date like "' . $date . '%" && team2=' . $id . ' && winner=2';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $sMin2 = $row2['sMin2'];

        $winsMin = $winsMin1 + $winsMin2;
        $successMin = floor(100 *$winsMin / $matchesMin);

        $gainsMin = $sMin1 + $sMin2 - $matchesMin;
        $roiMin = round(100 * $gainsMin / $matchesMin);

        $sql2 = 'select count(id) as matchesMax from matches where final=1 && date like "' . $date . '%" && ((betMax1>0 && team1=' . $id . ') || (betMax2>0 && team2=' . $id . '))';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $matchesMax = $row2['matchesMax'];

        $sql2 = 'select count(id) as winsMax1 from matches where final=1 && date like "' . $date . '%" && betMax1>0 && team1=' . $id . ' && winner=1';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $winsMax1 = $row2['winsMax1'];

        $sql2 = 'select count(id) as winsMax2 from matches where final=1 && date like "' . $date . '%" && betMax2>0 && team2=' . $id . ' && winner=2';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $winsMax2 = $row2['winsMax2'];

        $sql2 = 'select sum(betMax1) as betsMax1 from matches where final=1 && date like "' . $date . '%" && betMax1>0 && team1=' . $id;
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $betsMax1 = $row2['betsMax1'] ? $row2['betsMax1'] : 0;

        $sql2 = 'select sum(betMax2) as betsMax2 from matches where final=1 && date like "' . $date . '%" && betMax2>0 && team2=' . $id;
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $betsMax2 = $row2['betsMax2'] ? $row2['betsMax2'] : 0; // echo "$sql2<br>BETSMAX2: $betsMax2<br>";

        $sql2 = 'select sum(odd1 * betMax1) as sMax1 from matches where betMax1>0 && final=1 && date like "' . $date . '%" && team1=' . $id . ' && winner=1';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $sMax1 = 0 + $row2['sMax1'];

        $sql2 = 'select sum(odd2 * betMax2) as sMax2 from matches where betMax2>0 && final=1 && date like "' . $date . '%" && team2=' . $id . ' && winner=2';
        $res2 = $body->db->query($sql2);
        $row2 = $res2->fetch_assoc();
        $sMax2 = 0 + $row2['sMax2'];

        $winsMax = $winsMax1 + $winsMax2;
        $successMax = floor(100 * $winsMax / $matchesMax);

        $betsMax = $betsMax1 + $betsMax2; 
        $gainsMax = $sMax1 + $sMax2 - $betsMax;
        $roiMax = round(100 * $gainsMax / $betsMax);

        $html .= '<div class="' . $titleLevel . ' bold py-5" onClick="toggleVisibility(\'' . $date . '\')">
        <div class="grid right py-5">
            <span class="span2 left">' . $date . '</span>
            <span class="span2">' . $matches . '</span>
            <span class="span2">' . $wins . '</span>
            <span class="span2">' . $success . '%</span>
            <span class="span2">' . number_format($gains, 2) . '</span>
            <span class="span2">' . $roi . '%</span>
        </div>
            
        <div class="grid right normal py-5">
            <span class="span2 left">MIN bets</span>
            <span class="span2">' . $matchesMin . '</span>
            <span class="span2">' . $winsMin . '</span>
            <span class="span2">' . $successMin . '%</span>
            <span class="span2">' . number_format($gainsMin, 2) . '</span>
            <span class="span2">' . $roiMin . '%</span>
        </div>

        <div class="grid right normal py-5">
            <span class="span2 left">MAX bets</span>
            <span class="span2">' . $matchesMax . '</span>
            <span class="span2">' . $winsMax . '</span>
            <span class="span2">' . $successMax . '%</span>
            <span class="span2">X ' . number_format($gainsMax, 2) . '</span>
            <span class="span2">' . $roiMax . '%</span>
        </div>
    </div>';

    	return $html;
	}
?>