<?
	global $db;

	$this->html = '
	<h1>ODDS</h1>';

	$proEarnings = 0;
	$conEarnings = 0;

	$oddsHeading = '
	<div class="list-title bg-white grid6">
		<div></div>
		<div class="right">Matches</div>
		<div></div>
		<div class="right">Gains pro</div>
		<div></div>
		<div class="right">Gains con</div>
	</div>';

	$odds = [];
	$winners = '';
	$winnersValues = [];
	$winnersListings = [];
	$winnersPercent = [];

	$sql = 'select odd from oddsSuccess';
	$res = $db->query($sql);
	while($row = $res->fetch_assoc()) {
		$odd = $row['odd'] / 10;
		$odds[] = $odd;
	}

	asort($odds);
	
	$oddsAll = '';

	foreach($odds as $odd) {
		$proEarnings = 0;
		$conEarnings = 0;

		// vai ser usado na tabela: MATCHES		
		$sql = 'select matches from oddsSuccess where odd=' . 10 * $odd;
		$res = $db->query($sql);
		$row = $res->fetch_assoc();
		$matches = $row['matches'];

		$wins = 0;

		$oddDetail = '
		<div class="list-subtitle grid13211">
			<div>Date</div>
			<div>Teams</div>
			<div>Odds</div>
			<div class="right">Gains Pro</div>
			<div class="right">Gains Con</div>
		</div>';

		$sql = 'select id, date, team1, team2, odd1, odd2, winner from matches where final=1 && ((odd1>' . ($odd - 0.001) . ' && odd1<' . ($odd + 0.091) . ') || (odd2>' . ($odd - 0.001) . ' && odd2<' . ($odd + 0.091) . ')) order by id'; // echo "$sql<br><br>";
		$res = $db->query($sql);

		while($row = $res->fetch_assoc()) {
			$id = $row['id'];
			$date = $row['date'];
			$team1 = $row['team1'];
			$team2 = $row['team2'];
			$odd1 = number_format($row['odd1'], 2);
			$odd2 = number_format($row['odd2'], 2);
			$winner = $row['winner'];
			$sql2 = 'select name from teams where id=' . $team1;
			$res2 = $db->query($sql2);
			$row2 = $res2->fetch_assoc();
			$team1name = $row2['name'];
			$sql2 = 'select name from teams where id=' . $team2;
			$res2 = $db->query($sql2);
			$row2 = $res2->fetch_assoc();
			$team2name = $row2['name'];

			$pro = (($odd1 < $odd2) && $winner == 1) || (($odd1 > $odd2) && $winner == 2);
			$con = (($odd1 < $odd2) && $winner == 2) || (($odd1 > $odd2) && $winner == 1);

			if($pro && $winner == 1) {
				$proEarnings += $odd1 - 1;
				$conEarnings -= 1;
			} 
			if($pro && $winner == 2) {
				$proEarnings += $odd2 - 1;
				$conEarnings -= 1;
			} 
			if($con && $winner == 1) {
				$conEarnings += $odd1 - 1;
				$proEarnings -= 1;
			} 
			if($con && $winner == 2) {
				$conEarnings += $odd2 - 1;
				$proEarnings -= 1;
			}

			$styleColor = $pro ? 'green' : 'red';

			$balance = $proEarnings > $conEarnings ? $proEarnings : $conEarnings;

			$proEarnings = number_format($proEarnings, 2);
			$conEarnings = number_format($conEarnings, 2);

			$formattedOdd = 10 * $odd;

			$spanTeam1 = $winner == 1 ? 'bold' : '';
			$spanTeam2 = $winner == 2 ? 'bold' : '';

			$oddDetail .= '
				<div class="list-detail grid13211" style="color:' . $styleColor . '">
					<div>' . $date . '</div>
					<div><span class="' . $spanTeam1 . '">' . $team1name . '</span>-<span class="' . $spanTeam2 . '">' . $team2name . '</span></div>
					<div>' . $odd1 . '-' . $odd2 . '</div>
					<div class="pro-balance right">' . $proEarnings . '</div>
					<div class="con-balance right">' . $conEarnings . '</div>
				</div>';
		}

		$proEarnings = number_format($proEarnings, 2);
		$conEarnings = number_format($conEarnings, 2);

		$proPercent = floor(100 * $proEarnings / $matches);
		$conPercent = floor(100 * $conEarnings / $matches);

		$stylePro = $proEarnings > 0 ? 'green' : 'red';
		$styleCon = $conEarnings > 0 ? 'green' : 'red';

		$formattedId = 'all-' . (10 * $odd);

		$listing = '
			<div class="list-title bg-white grid6" onClick="toggleShowHide(\'' . $formattedId . '\')">
				<div>' . number_format($odd, 1) . '</div>
				<div class="right">' . $matches . '</div>
				<div class="right ' . $stylePro . '">' . $proEarnings . '</div>
				<div class="right ' . $stylePro . '">' . $proPercent . '%</div>
				<div class="right ' . $styleCon . '">' . $conEarnings . '</div>
				<div class="right ' . $styleCon . '">' . $conPercent . '%</div>
			</div>
			<div id="' . $formattedId . '" style="display:none">' . $oddDetail . '</div>';

		$oddsAll .= $listing;

		if($matches > 5) {
			if($proEarnings > 0) {
				$winners .= $listing;
				$winnersValues[100 * $odd] = $proEarnings;
				$winnersPercent[100 * $odd] = $proPercent;
				$winnersListings[100 * $odd] = $listing;
			}
			if($conEarnings > 0) {
				$winners .= $listing;
				$winnersValues[100 * $odd] = $conEarnings;
				$winnersPercent[100 * $odd] = $conPercent;
				$winnersListings[100 * $odd] = $listing;
			}
		}

		$sql2 = 'update oddsSuccess set gainsPro=' . $proPercent . ', gainsCon=' . $conPercent . ' where odd=' . 10 * $odd;  echo "$sql2<br>";
		$db->query($sql2);
	}

	arsort($winnersPercent);

	$oddsWinners = '';

	foreach ($winnersPercent as $key => $value) {
		$listing = $winnersListings[$key];
		$listing = str_replace('all-', 'winners-', $listing);
		$oddsWinners .= $listing;
	}

	$this->html .= '
	<section id="list">
		<div class="list-title" onClick="toggleShowHide(\'winners\')">WINNERS</div>
		<div id="winners" style="display:block">
		' . $oddsHeading . '
		' . $oddsWinners . '
		</div>
		<div class="list-title" onClick="toggleShowHide(\'all\')">ALL</div>
		<div id="all" style="display:none">
		' . $oddsHeading . '
		' . $oddsAll . '
		</div>
	</section>
	<script>
		function toggleShowHide(id) {
			const el = document.getElementById(id);
			const elDisplay = el.style.display;
			if(elDisplay == "none") {
				el.style.display = "block";
			} else {
				el.style.display = "none";
			}
		}
	</script>';
?>