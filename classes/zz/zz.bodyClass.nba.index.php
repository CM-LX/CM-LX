<?
	global $db;
	
	$this->html = '
<style>
.bg-green {
	background-color: green;
}
.bg-red {
	background-color: red;
}
#bets { 
}
/* #bets div {
 	padding: 3px 0;
} */
#bets .match {
	display: flex;
	justify-content: space-between;
	background-color: black;
	color: white;
	font-weight: bold;
	padding: 5px;
	margin: 10px 0;
}
.odd-stats {
	font-weight: bold;
}
.odd-history {
	font-size: .8em;
	border-bottom: 1px solid #ccc;
	align-items: center;
}
.odd-stats, .odd-history {
	display: flex;
	justify-content: space-between;
}
.odd-history-graph {
	width: 3px; 
	height: 12px;
	display:inline-block;
}
</style>
<section id="bets">';

	$sql = 'select id, date, team1, team2, odd1, odd2 from matches where final=0 order by date desc, id';
	$res = $db->query($sql);
	while($row = $res->fetch_assoc())
	{
		$id = $row['id'];
		$team1 = $row['team1'];
		$team2 = $row['team2'];
		$odd1 = number_format(floor($row['odd1'] * 10) / 10, 2);
		$odd2 = number_format(floor($row['odd2'] * 10) / 10, 2);
		$sql2 = 'select name from teams where id=' . $team1;
		$res2 = $db->query($sql2);
		$row2 = $res2->fetch_assoc();
		$team1name = $row2['name'];
		$sql2 = 'select name from teams where id=' . $team2;
		$res2 = $db->query($sql2);
		$row2 = $res2->fetch_assoc();
		$team2name = $row2['name'];
		$this->html .= '
			<div class="list-title">
				<div>' . $team1name . ' - ' . $team2name . '</div><div>ODDS: ' . $odd1 . ' - ' . $odd2 . '</div>
			</div>';

			
		$oddNumber = 0;
		$odds = [ $odd1, $odd2 ];
		foreach($odds as $odd) {
			$graphLine = '';
			$acc = 0;
			$num = 0;
			$wins = 0;
			$losses = 0;
			$last = 'NONE';
			$currentOdd = $odd; 
			$oddNumber++;

			$res2 = $db->query($sql2);
			$sql2 = 'select id, date, team1, team2, odd1, odd2, winner 
				from matches where final=1 && 
				((odd1>=' . ($odd - 0.001) . ' && odd1<' . ($odd + 0.091) . ') || (odd2>=' . ($odd - 0.001) . ' && odd2<' . ($odd + 0.091) . ')) 
				order by date'; // echo "<li>$sql2</li>";
			$res2 = $db->query($sql2);
			$numRows = $db->num_rows();
			$seqWins = 0;
			$seqLosses = 0;
			while($row2 = $res2->fetch_assoc())
			{
				$num++;
				$bgClass1 = '';
				$bgClass2 = '';

				$rid = $row2['id'];
				$date = $row2['date'];
				$rteam1 = $row2['team1'];
				$rteam2 = $row2['team2'];
				$rodd1 = number_format(round($row2['odd1'], 2), 2);
				$rodd2 = number_format(round($row2['odd2'], 2), 2);
				$winner = $row2['winner'];
				$sql3 = 'select name from teams where id=' . $rteam1;
				$res3 = $db->query($sql3);
				$row3 = $res3->fetch_assoc();
				$rteam1name = $row3['name'];
				$sql3 = 'select name from teams where id=' . $rteam2;
				$res3 = $db->query($sql3);
				$row3 = $res3->fetch_assoc();
				$rteam2name = $row3['name'];

				$classWinner1 = $winner == 1 ? 'winner' : '';
				$classWinner2 = $winner == 2 ? 'winner' : '';


				$winBlock = "<span class='odd-history-graph bg-green'></span>";
				$lossBlock = "<span class='odd-history-graph bg-red'></span>";

				// ODD WINS AS 1 AS PREDICTED
				if($rodd1 >= $odd - 0.001 && $rodd1 < $odd + 0.091 && $rodd1 < $rodd2 && $winner == 1) {
					$wins++;
					$acc += $rodd1 - 1;
					$bgClass1 = "green";
					$graphLine .= $winBlock;
					$last = 'WIN';
					$seqLosses = 0;
					$seqWins++;
				}
				// ODD WINS AS 1 AGAINST THE ODDS
				if($rodd1 >= $odd - 0.001 && $rodd1 < $odd + 0.091 && $rodd1 > $rodd2 && $winner == 1) {
					$wins++;
					$acc += $rodd1 - 1;
					$bgClass1 = "green";
					$graphLine .= $winBlock;
					$last = 'WIN';
					$seqLosses = 0;
					$seqWins++;
				}
				// ODD WINS AS 2 AS PREDICTED
				if($rodd2 >= $odd - 0.001 && $rodd2 < $odd + 0.091 && $rodd2 < $rodd1 && $winner == 2) {
					$wins++;
					$acc += $rodd2 - 1;
					$bgClass2 = "green";
					$graphLine .= $winBlock;
					$last = 'WIN';
					$seqLosses = 0;
					$seqWins++;
				}
				// ODD WINS AS 2 AGAINST THE ODDS
				if($rodd2 >= $odd - 0.001 && $rodd2 < $odd + 0.091 && $rodd2 > $rodd1 && $winner == 2) {
					$wins++;
					$acc += $rodd2 - 1;
					$bgClass2 = "green";
					$graphLine .= $winBlock;
					$last = 'WIN';
					$seqLosses = 0;
					$seqWins++;
				}

				// ODD LOOSES AS 1 AS PREDICTED
				if($rodd1 >= $odd - 0.001 && $rodd1 < $odd + 0.091 && $rodd1 < $rodd2 && $winner == 2) {
					$losses++;
					$acc += -1;
					$bgClass1 = "red";
					$graphLine .= $lossBlock;
					$last = 'LOSS';
					$seqWins = 0;
					$seqLosses++;
				}
				// ODD LOOSES AS 1 AGAINST THE ODDS
				if($rodd1 >= $odd - 0.001 && $rodd1 < $odd + 0.091 && $rodd1 > $rodd2 && $winner == 2) {
					$losses++;
					$acc += -1;
					$bgClass1 = "red";
					$graphLine .= $lossBlock;
					$last = 'LOSS';
					$seqWins = 0;
					$seqLosses++;
				}
				// ODD LOOSES AS 2 AS PREDICTED
				if(($rodd2 >= $odd - 0.001) && ($rodd2 < $odd + 0.091) && ($rodd2 < $rodd1) && $winner == 1) {	
					$losses++;
					$acc += -1;
					$bgClass2 = "red";
					$graphLine .= $lossBlock;
					$last = 'LOSS';
					$seqWins = 0;
					$seqLosses++;
				}
				// ODD LOOSES AS 2 AGAINST THE ODDS
				if(($rodd2 >= $odd - 0.001) && ($rodd2 < $odd + 0.091) && ($rodd2 > $rodd1) && $winner == 1) {
					$losses++;
					$acc += -1;
					$bgClass2 = "red";
					$graphLine .= $lossBlock;
					$last = 'LOSS';
					$seqWins = 0;
					$seqLosses++;
				}
				
				$percentWin = round( 100 * $wins / $num);
				$percentLoss = round( 100 * $losses / $num);
				$bgPro = 256 - floor(2.56 * $percentWin);
				$bgCon = 256 - floor(2.56 * $percentLoss);
				
				$this->html .= "</div>";
			}
				
			$notifyWins = $last == 'LOSS' ? "Wins $percentWin% SeqLosses: $seqLosses" : '';
			$notifyLosses = $last == 'WIN' ? "Looses $percentLoss% SeqWins: $seqWins" : '';

			$bet = $percentWin * $seqLosses + $percentLoss * $seqWins;
			$notifyBet = $bet > 100 ? 'BET ' . pow(2, floor($bet / 100 - 1)) : '';
			$chance = $last == 'LOSS' ? 'WIN' : 'LOSS';
			$this->html .= "
			<div class='odd-stats'>
				<div>$currentOdd</div>
				<div style='color:red'><b>$notifyBet</b></div>
				<div>PROB $chance: $bet </div>
			</div>";

			$this->html .= "
			<div class='odd-history'>
				<div>$graphLine</div>
				<div>Wins:$wins Losses:$losses $notifyWins $notifyLosses</div>
			</div>";
			

		}
		
	}
	
	$this->html .= '</section>';


?>