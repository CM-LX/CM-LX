<?
	global $db;

	$get_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	$name = isset($_POST['name']) ? strip_tags($_POST['name']) : '';
	$create = isset($_POST['create']) ? 1 : 0;
	$sort = isset($_GET['s']) ? strip_tags($_GET['s']) : 'name';
	$dir = isset($_GET['d']) ? strip_tags($_GET['d']) : 'asc';

	$id = $get_id + $post_id;
	
	if($post_id && $name) {
		$sql = 'update teams set name="' . $name . '" where id=' . $post_id;
		$res = $db->query($sql);
	}
	
	if($create && $name) {
		$sql = 'insert into teams set name="' . $name . '"';
		$db->query($sql);	
	}

	function getMatches($id, $start, $end) {
		global $db;

		$sql = 'select id, date, team1, team2, odd1, odd2, winner from matches where (team1=' . $id . ' or team2=' . $id . ') and date>="' . $start . '" and date <="' . $end . '"'; // echo "$sql<br><br>";
		$res = $db->query($sql);
		$html = '';
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
	
			$styleColor = $pro ? 'green' : 'red';

			$spanTeam1 = $winner == 1 ? 'bold' : '';
			$spanTeam2 = $winner == 2 ? 'bold' : '';

			$html .= '
				<div class="list-detail grid121 ' . $styleColor . '">
					<div>' . $date . '</div>
					<div><span class="' . $spanTeam1 . '">' . $team1name . '</span>-<span class="' . $spanTeam2 . '">' . $team2name . '</span></div>
					<div class="right">' . $odd1 . '-' . $odd2 . '</div>
				</div>';
		}			
		return $html;
	}

	function quarters($id) {
		global $db;

		$html = '';
		$sql = 'select matches from teams where id=' . $id;
		$res = $db->query($sql);
		$row = $res->fetch_assoc();
		$matches = $row['matches'];
		
		$quarterGames = $matches / 4;

		$s = [];
		for ($i = 0; $i < 4; $i++) {
			$quarterStart = round($i * $quarterGames);
			$quarterEnd = round(($i + 1) * $quarterGames) - 1;

			$sql = 'select date from matches where final>0 and (team1=' . $id . ' or team2=' . $id . ') limit ' . $quarterStart . ', 1';
			$res = $db->query($sql);
			$row = $res->fetch_assoc();
			$quarterDateStart = $row['date'];

			$sql = 'select date from matches where final>0 and (team1=' . $id . ' or team2=' . $id . ') limit ' . $quarterEnd . ', 1';
			$res = $db->query($sql);
			$row = $res->fetch_assoc();
			$quarterDateEnd = $row['date'];
			
			$sql = 'select count(id) as c from matches where date>="' . $quarterDateStart . '" and date<="' . $quarterDateEnd . '" and (team1=' . $id . ' or team2=' . $id . ')';
			$res2 = $db->query($sql);
			$row2 = $res2->fetch_assoc();
			$quarterMatches = $row2['c'];
			
			$sql = 'select count(id) as c from matches where date>="' . $quarterDateStart . '" and date<="' . $quarterDateEnd . '" and ((team1=' . $id . ' and winner=1) or (team2=' . $id . ' and winner=2))';
			$res2 = $db->query($sql);
			$row2 = $res2->fetch_assoc();
			$quarterWins = $row2['c'];
			
			$quarterSuccess = round(100 * $quarterWins / $quarterMatches);
			$s[$i + 1] = $quarterSuccess;
			
			$html .= '<div class="list-subtitle">Performance from ' . $quarterDateStart  . ' to ' . $quarterDateEnd . ': Matches: ' . $quarterMatches . ' | Wins: ' . $quarterWins . ' | Success: ' .  $quarterSuccess . '%</div>';
			$html .= getMatches($id, $quarterDateStart, $quarterDateEnd);
		}
	
		return $html;
	}


	$this->html = '
	<h1>TEAMS</h1>';

	if($id) {
		$sql = 'select name, matches, matchesWon, success from teams where id=' . $id;
		$res = $db->query($sql);
		$row = $res->fetch_assoc();
		$name = $row['name'];
		$matches = $row['matches'];
		$matchesWon = $row['matchesWon'];
		$success = $row['success'];

		$this->html .= '
		<section>
			<div class="list-title">
				<div>
					<form action="teams.nba.php" method="POST">
						<input type="hidden" name="id" value="' . $id . '">
						<input type="text" name="name" value="' . $name . '" id="team-name-input"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="height:18px"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"/></svg>
					</form>
				</div>
				<div>Matches: ' . $matches . '</div>
				<div>Wins: ' . $matchesWon . '</div>
				<div class="right">Success: ' . $success . '%</div>
			</div>
		</section>' . quarters($id);
	} else {
		$arrouUp = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="height:18px"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"/></svg>';
		$arrowDown = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="height:18px"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M169.4 470.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 370.8 224 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 306.7L54.6 265.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/></svg>';
		$teamsList = '
		<div class="list-title grid21111 bg-white">
			<div></div>
			<div class="right">MATCHES <a href="teams.nba.php?s=matches&d=asc">' . $arrouUp . '</a> <a href="teams.nba.php?s=matches&d=desc">' . $arrowDown . '</a></div>
			<div class="right">WINS <a href=teams.nba.php?s=matchesWon&d=asc">' . $arrouUp . '</a> <a href=teams.nba.php?s=matchesWon&d=desc">' . $arrowDown . '</a></div>
			<div class="right">SUCCESS <a href="teams.nba.php?s=success&d=asc">' . $arrouUp . '</a> <a href="teams.nba.php?s=success&d=desc">' . $arrowDown . '</a></div>
			<div class="right">TREND <a href="teams.nba.php?s=trend&d=asc">' . $arrouUp . '</a> <a href="teams.nba.php?s=trend&d=desc">' . $arrowDown . '</a></div>
		</div>';

		$sql = 'select id, name, matches, matchesWon, success, trend from teams order by ' . $sort . ' ' . $dir;
		$res = $db->query($sql);
		while($row = $res->fetch_assoc())
		{
			$id = $row['id'];
			$name = $row['name'];
			$matches = $row['matches'];
			$matchesWon = $row['matchesWon'];
			$success = $row['success'];
			$trend = $row['trend'];

			$teamsList .= '
			<div class="list-title grid21111">
				<div><a href="teams.nba.php?id=' . $id . '">' . $name . '</a></div>
				<div class="right">' . $matches . '</div>
				<div class="right">' . $matchesWon . '</div>
				<div class="right">' . $success . '%</div>
				<div class="right">' . $trend . '</div>
			</div>';
		}

		$this->html .= '
		<section id="teams-list">
			' . $teamsList . '
		</section>
		<section id="teams-new-team">
			<form action="teams.nba.php" method="POST">
				<input type="hidden" name="create" value="1">
				NEW TEAM: <input type="text" placeholder="Team name" name="name">
				<input type="submit" value="Create">
			</form>
		</section>';
	}
?>