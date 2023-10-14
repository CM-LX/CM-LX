<?php
	function teamGetMatchesByMonth($id, $month, $body) {
		$matches = 0;
		$wins = 0;
		$gains = 0;
		$gainsBetMin = 0;
		$gainsBetMax = 0;

		$html = '<div class="third-level-title py-5" onClick="toggleVisibility(\'' . $month . '\')">
			<div class="grid left py-5">
				<span class="span2"></span>
				<span class="span4"></span>
				<span class="span2 right">Team gains | acc</span>
				<span class="span2 right">MIN gains | acc</span>
				<span class="span2 right">MAX gains | acc</span>
			</div>
		</div>';		
		
		$sql = 'select id from matches where final=1 && date_format(date, "%Y-%m")="' . $month . '" && (team1=' . $id . ' || team2=' . $id . ')'; // echo "Team::getPlayedMonths $sql2<br>";
		$res = $body->db->query($sql);
		while($row = $res->fetch_assoc()) {
			$matchId = $row['id'];
			
			$date = $body->match->get($matchId, 'date');
			$team1 = $body->match->get($matchId, 'team1');
			$team2 = $body->match->get($matchId, 'team2');
			$odd1 = $body->match->get($matchId, 'odd1');
			$odd2 = $body->match->get($matchId, 'odd2');
			$winner = $body->match->get($matchId, 'winner');
			$betMin1 = $body->match->get($matchId, 'betMin1');
			$betMin2 = $body->match->get($matchId, 'betMin2');
			$betMax1 = $body->match->get($matchId, 'betMax1');
			$betMax2 = $body->match->get($matchId, 'betMax2');

			
			$team1name = $body->team->getField($team1, 'abName'); //echo "Team1 $team1name<br>";
			$team2name = $body->team->getField($team2, 'abName'); // echo "Team2 $team2name<br>";
			
			$pro = (($odd1 < $odd2) && $winner == 1) || (($odd1 > $odd2) && $winner == 2);
			
			$styleColor = $pro ? 'green' : 'red';
			
			$spanTeam1 = $winner == 1 ? 'bold' : '';
			$spanTeam2 = $winner == 2 ? 'bold' : '';
			
			$matches++;
			$wins += ($team1 == $id && $winner == 1) || ($team2 == $id && $winner == 2);
			$gain = ($team1 == $id && $winner == 1) ? $odd1 - 1 : (($team2 == $id && $winner == 2) ? $odd2 - 1 : -1);
			$gains += $gain;
			
			$gainBetMin1 = ($betMin1 && $team1 == $id) ? ($winner == 1 ? $odd1 - 1 : -1) : 0;
			$gainBetMin2 = ($betMin2 && $team2 == $id) ? ($winner == 2 ? $odd2 - 1 : -1) : 0;
			$gainBetMin = $gainBetMin1 + $gainBetMin2;
			$gainsBetMin += $gainBetMin;

			$gainBetMax1 = ($betMax1 && $team1 == $id) ? ($winner == 1 ? $betMax1 * ($odd1 - 1) : -$betMax1) : 0;
			$gainBetMax2 = ($betMax2 && $team2 == $id) ? ($winner == 2 ? $betMax2 * ($odd2 - 1) : -$betMax2) : 0;

			$gainBetMax = $gainBetMax1 + $gainBetMax2;
			$gainsBetMax += $gainBetMax;

			$html .= '
				<div class="list-detail grid left">
					<span class="span2">' . $date . '</span>
					<span class="span4 ' . $styleColor . '">' . $matchId . ' ' . $odd1 . '-' . $odd2 . ' <span class="' . $spanTeam1 . '">' . $team1name . '</span>-<span class="' . $spanTeam2 . '">' . $team2name . '</span></span>
					<span class="span2 right">' . $body->colorFormat($gain) . ' | ' . $body->colorFormat($gains) . '</span>
					<span class="span2 right">bets: ' .  $betMin1 . ':' . $betMin2 . ' | ' . $body->colorFormat($gainBetMin) . ' | ' . $body->colorFormat($gainsBetMin) . '</span>
					<span class="span2 right">bets: ' .  $betMax1 . ':' . $betMax2 . ' | ' . $body->colorFormat($gainBetMax) . ' | ' . $body->colorFormat($gainsBetMax) . '</span>
				</div>';		
		}
			
		return $html;
	}
?>