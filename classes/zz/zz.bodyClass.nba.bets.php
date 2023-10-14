<?
	global $db;

	$this->html = '
	<h1>BETS</h1>
	<style>
		#list {
			width: 100%;
		}
		.list-header {
			background-color: #eee;
			border-bottom: 1px solid #000;
			padding: 10px 0 9px 20px;
			margin: 20px 0;
			font-size: 130%;
			font-weight: bold;
			cursor: pointer;
		}
		.list-odd {
			display: grid;
			grid-template-columns: 2fr 4fr 2fr 2fr;
			font-weight: bold;
			margin: 20px 0;
			padding-bottom: 5px;
			border-bottom: 1px solid black;
			cursor: pointer;
		}


	</style>';

	$balance = 0;
	$balance2 = 0;
	$balance3 = 0;

	$sql = 'select date from matches where final=1 group by date order by date';
	$res = $db->query($sql);
	while($row = $res->fetch_assoc()) {
		$date = $row['date'];

		$matchesList = '';

		// $log = '';

		$dailyBalance = 0;
		$dailyBalance2 = 0;
		$dailyBalance3 = 0;

		$sql2 = 'select id, team1, team2, odd1, odd2, winner from matches where date="' . $date . '" order by id';
		$res2 = $db->query($sql2);
		while($row2 = $res2->fetch_assoc()) {
			$id = $row2['id'];
			$team1 = $row2['team1'];
			$team2 = $row2['team2'];
			$vodd1 = $row2['odd1'];
			$vodd2 = $row2['odd2'];
			$odd1 = floor(10 * $row2['odd1']);
			$odd2 = floor(10 * $row2['odd2']);
			$winner = $row2['winner'];

			// skip matches with the same odds
			$sql3 = 'select id, odd1, odd2 from matches where id!=' . $id . ' and date="' . $date . '" and (floor(round(100 * odd1) / 10)=' . $odd1 . ' or floor(round(100 * odd2) / 10)=' . $odd1 . ' or floor(round(100 * odd1) / 10)=' . $odd2 . ' or floor(round(100 * odd2) / 10)=' . $odd2 . ')'; 
			$res3 = $db->query($sql3);
			$row3 = $res3->fetch_assoc();
			// $id = $row3['id'];
			// $rodd1 = $row3['odd1'];
			// $rodd2 = $row3['odd2']; 
			$num = $db->num_rows(); 
			// $matchesList .= '<br><br><div>ID: ' . $id . ' ODD1: ' . $odd1 . ' RODD1: ' . $rodd1 . ' ODD2: ' . $odd2 . ' RODD2: ' . $rodd2 . ' ' . $sql3 . ': ' . $num . '</div>';
			if($num) continue;

			$matchStyle = (($odd1 < $odd2) && $winner == 1) || (($odd2 < $odd1) && $winner == 2) ? 'green' : 'red';
			$team1style = $winner == 1 ? 'bold' : '';
			$team2style = $winner == 2 ? 'bold' : '';

			$sql3 = 'select name, success, trend from teams where id=' . $team1;
			$res3 = $db->query($sql3);
			$row3 = $res3->fetch_assoc();
			$name1 = $row3['name'];
			$teamSuccess1 = $row3['success'];
			$trend1 = $row3['trend'];

			$sql3 = 'select name, success, trend from teams where id=' . $team2;
			$res3 = $db->query($sql3);
			$row3 = $res3->fetch_assoc();
			$name2 = $row3['name'];
			$teamSuccess2 = $row3['success'];
			$trend2 = $row3['trend'];

			$sql3 = 'select success, gainsPro, gainsCon from oddsSuccess where odd=' . $odd1; 
			$res3 = $db->query($sql3);
			$row3 = $res3->fetch_assoc();
			$oddSuccess1 = $row3['success'];
			$oddGainsPro1 = $row3['gainsPro'];
			$oddGainsCon1 = $row3['gainsCon'];

			$sql3 = 'select success, gainsPro, gainsCon from oddsSuccess where odd=' . $odd2; 
			$res3 = $db->query($sql3);
			$row3 = $res3->fetch_assoc();
			$oddSuccess2 = $row3['success'];
			$oddGainsPro2 = $row3['gainsPro'];
			$oddGainsCon2 = $row3['gainsCon'];
			
			$probWin1 = 0;
			$probWin2 = 0;

			$sql3 = 'select seqWins, seqLosses from odds where odd=' . $odd1 . ' and date<"' . $date . '" order by date desc limit 1';
			// $matchesList .= "<div>$sql3</div>";
			$res3 = $db->query($sql3);
			$num = $db->num_rows();
			if($num) {
				$row3 = $res3->fetch_assoc();
				$seqWins1 = $row3['seqWins'];
				$seqLosses1 = $row3['seqLosses'];
			} else {
				$seqWins1 = 0;
				$seqLosses1 = 0;
			}

			if($seqWins1) {
				$probLoss1 = $seqWins1 * (100 - $oddSuccess1);
				$probWin1 = 100 - $probLoss1;
			} else {
				$probWin1 = $seqLosses1 * $oddSuccess1;
				$probLoss1 = $probWin1 ? 100 - $probWin1 : 0;
			}

			$sql3 = 'select seqWins, seqLosses from odds where odd=' . $odd2 . ' and date<"' . $date . '" order by date desc limit 1'; 
			$res3 = $db->query($sql3);
			$num = $db->num_rows();
			if($num) {
				$row3 = $res3->fetch_assoc();
				$seqWins2 = $row3['seqWins'];
				$seqLosses2 = $row3['seqLosses'];
			} else {
				$seqWins2 = 0;
				$seqLosses2 = 0;
			}

			if($seqWins2) {
				$probLoss2 = $seqWins2 * (100 - $oddSuccess2);
				$probWin2 = 100 - $probLoss2;
			} else {
				$probWin2 = $seqLosses2 * $oddSuccess2;
				$probLoss2 = $probWin2 ? 100 - $probWin2 : 0;
			}

			$sumProbWin1 = $probWin1 + $probLoss2;
			$sumProbWin2 = $probWin2 + $probLoss1;

			$probWin1WithTeamSuccess = $sumProbWin1 * $teamSuccess1 / 100;
			$probWin2WithTeamSuccess = $sumProbWin2 * $teamSuccess2 / 100;

			$threshold = 90;

			$gain = 0;
			$betWithTeamSuccess = '';
			if($probWin1WithTeamSuccess > $threshold && $probWin1WithTeamSuccess > $probWin2WithTeamSuccess) {
				$bet = floor($probWin1WithTeamSuccess / 100);
				$betWithTeamSuccess = "Bet $bet on 1";
				$gain = $winner == 1 ? $vodd1 - $bet : -$bet;
			} elseif($probWin2WithTeamSuccess > $threshold) {
				$bet = floor($probWin2WithTeamSuccess / 100);
				$betWithTeamSuccess = "Bet $bet on 2";
				$gain = $winner == 2 ? $vodd2 - $bet : -$bet;
			}
			$dailyBalance += $gain;

			$probWin1WithTeamTrend = $probWin1WithTeamSuccess * (100 + $trend1) / 100;
			$probWin2WithTeamTrend = $probWin2WithTeamSuccess * (100 + $trend2) / 100;
			
			$gain2 = 0;
			$betWithTeamTrend = '';
			if($probWin1WithTeamTrend > $threshold && $probWin1WithTeamTrend > $probWin2WithTeamTrend) {
				$bet = floor($probWin1WithTeamTrend / 100);
				$betWithTeamTrend = "Bet $bet on 1";
				$gain2 = $winner == 1 ? $vodd1 - $bet : -$bet;
			} elseif($probWin2WithTeamTrend > $threshold) {
				$bet = floor($probWin2WithTeamTrend / 100);
				$betWithTeamTrend = "Bet $bet on 2";
				$gain2 = $winner == 2 ? $vodd2 - $bet : -$bet;
			}
			$dailyBalance2 += $gain2;

			$probWin1WithOddGains = $probWin1WithTeamTrend * (100 + $oddGainsPro1) / 100;
			$probWin2WithOddGains = $probWin2WithTeamTrend * (100 + $oddGainsPro2) / 100;
			
			$gain3 = 0;
			$betWithOddGains = '';
			if($probWin1WithOddGains > $threshold && $probWin1WithOddGains > $probWin2WithOddGains) {
				$bet = floor($probWin1WithOddGains / 100);
				$betWithOddGains = "Bet $bet on 1";
				$gain3 = $winner == 1 ? $vodd1 - $bet : -$bet;
			} elseif($probWin2WithOddGains > $threshold) {
				$bet = floor($probWin2WithOddGains / 100);
				$betWithOddGains = "Bet $bet on 2";
				$gain3 = $winner == 2 ? $vodd2 - $bet : -$bet;
			}
			$dailyBalance3 += $gain3;

			$matchesList .= '
			<div class="list-detail grid5">
				<div class="' . $matchStyle. '"><span class="' . $team1style . '">' . $name1 . '</span>-<span class="' . $team2style . '">' . $name2 . '</span></div>
				<div>' . $odd1 . '-' . $odd2 . '</div>
				<div>TeamsSuccess: ' . $teamSuccess1 . '-' . $teamSuccess2 . '</div>
				<div>TeamsTrend: ' . $trend1 . '-' . $trend2 . '</div>
			</div>
			<div class="list-detail grid5">
				<div>SuccessOdd1: ' . $oddSuccess1 . '</div>
				<div>SeqWinsOdd1: ' . $seqWins1 . '</div>
				<div>SeqLossesOdd1: ' . $seqLosses1 . '</div>
				<div>ProbWin1: ' . $probWin1 . '</div>
				<div>ProbLoss1: ' . $probLoss1 . '</div>
				<div>SuccessOdd2: ' . $oddSuccess2 . '</div>
				<div>SeqWinsOdd2: ' . $seqWins2 . '</div>
				<div>SeqLossesOdd2: ' . $seqLosses2 . '</div>
				<div>ProbWin2: ' . $probWin2 . '</div>
				<div>ProbLoss2: ' . $probLoss2 . '</div>
				<div>SumProbWin1: ' . $sumProbWin1 . '</div>
				<div>SumProbWin2: ' . $sumProbWin2 . '</div>
				<div></div>
				<div></div>
				<div></div>
				<div>ProbWin1WithSuccess: ' . $probWin1WithTeamSuccess . '</div>
				<div>ProbWin2WithSuccess: ' . $probWin2WithTeamSuccess . '</div>
				<div class="red"><b>' . $betWithTeamSuccess . '</b></div>
				<div>' . $gain . '</div>
				<div>' . $dailyBalance . '</div>
				<div>ProbWin1WithTrend: ' . $probWin1WithTeamTrend . '</div>
				<div>ProbWin2WithTrend: ' . $probWin2WithTeamTrend . '</div>
				<div class="red"><b>' . $betWithTeamTrend . '</b></div>
				<div>' . $gain2 . '</div>
				<div>' . $dailyBalance2 . '</div>
				<div>ProbWin1WithGains: ' . $probWin1WithOddGains . '</div>
				<div>ProbWin2WithGains: ' . $probWin2WithOddGains . '</div>
				<div class="red"><b>' . $betWithOddGains . '</b></div>
				<div>' . $gain3 . '</div>
				<div>' . $dailyBalance3 . '</div>

			</div>
			<hr style="margin:10px 0">
			';
		}

		$balance += $gain ? $dailyBalance : 0;
		$balance2 += $gain2 ? $dailyBalance2 : 0;
		$balance3 += $gain2 ? $dailyBalance3 : 0;
		
		$this->html .= '
		<section id="list">
			<div class="list-header" onClick="toggleShowHide(\'' . $date . '\')">' . $date . ' : ' . $balance . ' : ' . $balance2 . ' : ' . $balance3 . '</div>
			<div id="' . $date . '" style="display:none">
			' . $matchesList . '
			</div>
		</section>';

	}
	


	$this->html .= '
	<script>
		function toggleShowHide(date) {
			const el = document.getElementById(date);
			const elDisplay = el.style.display;
			if(elDisplay == "none") {
				el.style.display = "block";
			} else {
				el.style.display = "none";
			}
		}
	</script>';
?>