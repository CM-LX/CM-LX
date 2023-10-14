<?php

class Body {

	// GET variables
	private $page 		= isset($_GET['page']) 		? intval($_GET['page']) 		: 1;
	
	// POST variables
	private $post_id 	= isset($_POST['id']) 		? intval($_POST['id']) 			: 0;
	private $date 		= isset($_POST['date']) 	? strip_tags($_POST['date']) 	: 0;
	private $team1 		= isset($_POST['team1']) 	? intval($_POST['team1']) 		: 0;
	private $team2 		= isset($_POST['team2']) 	? intval($_POST['team2']) 		: 0;
	private $odd1 		= isset($_POST['odd1']) 	? floatval($_POST['odd1']) 		: 0;
	private $odd2 		= isset($_POST['odd2']) 	? floatval($_POST['odd2']) 		: 0;
	private $winner 	= isset($_POST['winner']) 	? intval($_POST['winner']) 		: 0;

	function __construct($db) {
		$this->page = $this->page < 1 ? 1 : $this->page; 
	}

		function selectTeams($team = 0) {
			global $db;

			$selectTeams = '';
			$sql = 'select id, name from teams order by name';
			$res = $db->query($sql);
			while($row = $res->fetch_assoc()) {
				$id = $row['id'];
				$name = $row['name'];
				$selected = ($team == $id) ? ' selected' : '';
				$selectTeams .= '
						<option value="' . $id . '"' . $selected . '>' . $name . '</option>';
			};
			return $selectTeams;
		}
		
		function quarterSuccess($id) {
			global $db;

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
	
				$sql = 'select date from matches where final>0 and (team1=' . $id . ' or team2=' . $id . ') limit ' . $quarterEnd . ', 1'; // echo "$sql<br>";
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
			}

			$sql = "update teams set success1=$s[1], success2=$s[2], success3=$s[3], success4=$s[4] where id=$id";
			$res = $db->query($sql);	
		}
		
		function updateTeams($id) {
			global $db;

			$sql = 'select count(id) as c from matches where final>0 and (team1=' . $id . ' or team2=' . $id . ')';
			$res = $db->query($sql);
			$row = $res->fetch_assoc();
			$matches = $row['c'];

			$sql = 'select count(id) as c from matches where (team1=' . $id . ' and winner=1) or (team2=' . $id . ' and winner=2)';
			$res = $db->query($sql);
			$row = $res->fetch_assoc();
			$wins = $row['c'];

			$success = round(100 * $wins / $matches);

			$sql = 'update teams set matches=' . $matches . ', matchesWon=' . $wins . ', success=' . $success . ' where id=' . $id; // echo "$sql<br>";
			$res = $db->query($sql);

			quarterSuccess($id);
		}

		function updateTrends($id) {
			global $db;
	
			$success = [];
			$trends = [];
			
			$sql = 'select success1, success2, success3, success4 from teams where id=' . $id;
			$res = $db->query($sql);
			$row = $res->fetch_assoc();
			$success[0] = $row['success1'];
			$success[1] = $row['success2'];
			$success[2] = $row['success3'];
			$success[3] = $row['success4'];
	
			$quarterFactor = [-1.5, -0.5, 0.5, 1.5];
			$meanSuccess = array_sum($success) / 4;
			$sumProducts = 0;
			for($i = 0; $i < 4; $i++) {
				$sumProducts += $quarterFactor[$i] * ($success[$i] - $meanSuccess);
			}
			$trends[1] = $sumProducts / 5;
			
			$quarterFactor = [-1, 0, 1];
			array_splice($success, 0, 1);
			$meanSuccess = array_sum($success) / 3;
			$sumProducts = 0;
			for($i = 0; $i < 3; $i++) {
				$sumProducts += $quarterFactor[$i] * ($success[$i] - $meanSuccess);
			}
			$trends[2] = $sumProducts / 2;
	
			$trends[3] = $success[2] - $success[1];
	
			$quarterFactor = [-1, 0, 1];
			$sumProducts = $trends[3] - $trends[1];
			$trends[0] = $sumProducts / 2;
	
			$sql = "update teams set trend=$trends[0] where id=$id";
			$res = $db->query($sql);
	
			return $trends;
		}

		function updateOdds ($date, $odd, $winner) {
			global $db;
			
			$sql = 'select id from odds where odd=' . 10 * $odd . ' and date="' . $date . '"'; // echo "$sql<br>";
			$res = $db->query($sql);
			$num = $db->num_rows();
			
			if(!$num) {
				$winLoss = $winner ? 'seqWins=1, seqLosses=0, ' : 'seqWins=0, seqLosses=1, ';
				$sql = 'insert into odds set odd=' . 10 * $odd . ', ' . $winLoss . 'date="' . $date . '"'; echo "$sql<br>";
				$db->query($sql);	
			} else {
				$row = $res->fetch_assoc();
				$id = $row['id'];

				$sql = 'select seqWins, seqLosses from odds where odd=' . 10 * $odd . ' and id<' . $id . ' order by id desc limit 1';
				$res = $db->query($sql);
				$num = $db->num_rows();

				if ($num) {
					$row = $res->fetch_assoc();
					$seqWins = $row['seqWins'] + 1;
					$seqLosses = $row['seqLosses'] + 1;
				} else {
					$seqWins = 1;
					$seqLosses = 1;
				}

				$winLoss = $winner ? 'seqWins=' . $seqWins . ', seqLosses=0' : 'seqWins=0, seqLosses=' . $seqLosses; 
				$sql = 'update odds set ' . $winLoss . ' where id=' . $id; "$sql<br>"; echo "$sql<br>";
				$db->query($sql);
			}
			
// REMOVER A PARTIR DE 1/1/2024 ----------- SERVE PARA CORRIGIR EVENTUAIS ERROS AO CRIAR A TABELA	
			$sql = 'select date from odds where odd=' . 10 * $odd . ' order by id';
			$res = $db->query($sql);
			while($row = $res->fetch_assoc()) {
				$date = $row['date'];
				$sql2 = 'select id, winner from matches where date="' . $date . '" and (floor(10 * odd1)=' . 10 * $odd . ')'; // echo "$sql2<br>";
				$res2 = $db->query($sql2);
				$num = $db->num_rows();
				if($num) {
					$row2 = $res2->fetch_assoc();
					$id = $row2['id'];
					$winner = $row2['winner'] == 1;
	
					$sql = 'select seqWins, seqLosses from odds where odd=' . 10 * $odd . ' and id<' . $id . ' order by id desc limit 1';
					$res = $db->query($sql);
					$num = $db->num_rows();
	
					if ($num) {
						$row = $res->fetch_assoc();
						$seqWins = $row['seqWins'] + 1;
						$seqLosses = $row['seqLosses'] + 1;
					} else {
						$seqWins = 1;
						$seqLosses = 1;
					}
	
					$winLoss = $winner ? 'seqWins=' . $seqWins . ', seqLosses=0' : 'seqWins=0, seqLosses=' . $seqLosses; 
					$sql = 'update odds set ' . $winLoss . ' where id=' . $id; "$sql<br>"; // echo "$sql<br>";
					$db->query($sql);
				}				
				$sql2 = 'select id, winner from matches where date="' . $date . '" and (floor(10 * odd2)=' . 10 * $odd . ')'; // echo "$sql2<br>";
				$res2 = $db->query($sql2);
				$num = $db->num_rows();
				if($num) {
					$row2 = $res2->fetch_assoc();
					$id = $row2['id'];
					$winner = $row2['winner'] == 2;
	
					$sql = 'select seqWins, seqLosses from odds where odd=' . 10 * $odd . ' and id<' . $id . ' order by id desc limit 1';
					$res = $db->query($sql);
					$num = $db->num_rows();
	
					if ($num) {
						$row = $res->fetch_assoc();
						$seqWins = $row['seqWins'] + 1;
						$seqLosses = $row['seqLosses'] + 1;
					} else {
						$seqWins = 1;
						$seqLosses = 1;
					}
	
					$winLoss = $winner ? 'seqWins=' . $seqWins . ', seqLosses=0' : 'seqWins=0, seqLosses=' . $seqLosses; 
					$sql = 'update odds set ' . $winLoss . ' where id=' . $id; "$sql<br>"; // echo "$sql<br>";
					$db->query($sql);
				}
			}
// REMOVER A PARTIR DE 1/1/2024
		}

		function updateOddsSuccess ($odd) {
			global $db;

// 			insert into oddsSuccess set odd=' . 10 * $odd . ', matches=1, wins=' . $winner . ', success=' . 100 * $winner . ', gainsPro=' . $gainsPro . ', gainsCon=' . $gainsCon

			$sql = 'select id from matches where odd1=' . 10 * $odd . ' or odd2=' . 10 * $odd;
			$res = $db->query($sql);
			$matches = $db->num_rows();

			$gainsPro = 0;
			$gainsCon = 0;

			$sql = 'select id, odd1, odd2, winner from matches where (odd1=' . 10 * $odd . ' and winner=1) or (odd2=' . 10 * $odd . ' and winner=2)';
			$res = $db->query($sql);
			$wins = $db->num_rows();

			while($row = $res->fetch_assoc()) {
				$odd1 = $row['odd1'];				
				$odd2 = $row['odd2'];				
				$winner = $row['winner'];
				
				$gainsPro += ($odd1); // corrigir,está incompleto
			}





			$sql = 'select id from oddsSuccess where odd=' . 10 * $odd;
			$res = $db->query($sql);
			$num = $db->num_rows();

			if ($num) {
				$row = $res->fetch_assoc();
				$id = $row['id'];				
				$matches = $row['matches']++;				
				$wins = $row['wins'] + $winner;				
				$gainsPro = $row['gainsPro'];				
				$gainsCon = $row['gainsCon'];			
				
				$gainsPro += $pro ? $odd - 1 : -1;
				$gainsCon += $pro ? -1 : $odd - 1;

				$success = floor(100 * $wins / $matches);
				$sql = 'update oddsSuccess set matches=' . $matches . ', wins=' . $wins . ', success=' . $success . ', gainsPro=' . $gainsPro . ', gainsCon=' . $gainsCon . ' where id=' . $id; echo "$sql<br>";
				$db->query($sql);
			} else {
				$gainsPro = $pro ? $odd - 1 : -1;
				$gainsCon = $pro ? -1 : $odd - 1;		

				$sql = 'insert into oddsSuccess set odd=' . 10 * $odd . ', matches=1, wins=' . $winner . ', success=' . 100 * $winner . ', gainsPro=' . $gainsPro . ', gainsCon=' . $gainsCon; echo "$sql<br>";
				$db->query($sql);
			}
		}

		$delete = isset($_GET['delete']) ? intval($_GET['delete']) : 0;
		if($delete) {
			$sql = 'delete from matches where id=' . $delete;
			$db->query($sql);
		}

		$match = isset($_GET['match']) ? intval($_GET['match']) : 0;
		$winner = isset($_GET['winner']) ? intval($_GET['winner']) : 0;
		if(($match && $winner)) {
			$sql = 'update matches set winner=' . $winner . ', final=1 where id=' . $match;
			$db->query($sql);

			$sql = 'select team1, team2, odd1, odd2, date, winner from matches where id=' . $match;
			$res = $db->query($sql);
			$row = $res->fetch_assoc();
			$team1 = $row['team1'];
			$team2 = $row['team2'];
			$rodd1 = floor($row['odd1'] * 10) / 10; 
			$rodd2 = floor($row['odd2'] * 10) / 10; 
			$date = $row['date'];
			$rwinner = $row['winner'];
			$pro = (($rodd1 < $rodd2) && $winner == 1) || (($rodd2 < $rodd1) && $winner == 2);

			updateTeams($team1);
			updateTeams($team2);
			updateTrends($team1);
			updateTrends($team2);
			updateOdds($date, $rodd1, $rwinner == 1);
			updateOdds($date, $rodd2, $rwinner == 2);
			// updateOddsSuccess($rodd1, $pro, ($rodd1 < $rodd2) && $winner == 1 ? 1 : 0 );
			// updateOddsSuccess($rodd2, $pro, ($rodd2 < $rodd1) && $winner == 2 ? 1 : 0 );
			updateOddsSuccess($rodd1);
			updateOddsSuccess($rodd2);
		} else {
			$create = $date && $team1 && $team2 && $odd1 && $odd2 && !$post_id;
	
			if($create & !$match) {
				$sql = 'insert into matches set date="' . $date . '", team1="' . $team1 . '", team2="' . $team2 . '", odd1="' . $odd1 . '", odd2="' . $odd2 . '"';
				$db->query($sql);
				$team1 = 0;
				$team2 = 0;
				$odd1 = 0;
				$odd2 = 0;
			}
		}

		if($post_id) {
			$sql = 'update matches set date="' . $date . '", team1="' . $team1 . '", team2="' . $team2 . '", odd1="' . $odd1 . '", odd2="' . $odd2 . '", winner="' . $winner . '"  where id=' . $post_id;
			$res = $db->query($sql);	
		}

		$date = $date ? $date : date("Y-m-d");
		
		$this->initialHtml = 
'		<wrapper>
			<header>
				<div id="header-left">
					<a href="index.php">Home</a> 
					<a href="teams.nba.php">Teams</a> 
					<a href="matches.nba.php">Matches</a>
					<a href="odds.nba.php">Odds</a>
					<a href="bets.nba.php">Bets</a>
				</div>
				<div id="header-right">
					<form action="index.php" method="POST">
						<input type="date" name="date" value="' . $date . '"> 
						<select name="team1"><option value="0">Team1</option>' . selectTeams($team1) . '</select> 
						<select name="team2"><option value="0">Team2</option>' . selectTeams($team2) . '</select>
						<input type="text" name="odd1" value="' . $odd1 . '"> <input type="text" name="odd2" value="' . $odd2 . '"> 
						<input type="submit" value="Create">
					</form>
				</div>
			</header>
			<main>
				<section id="main-left">';

				$matchesList = '';

				$sql = 'select date from matches group by date';
				$res = $db->query($sql);
				$num = $db->num_rows();

				$datesPerPage = 10;
				$previousPage = $page > 1 ? '<a href="index.php?page=' . ($page - 1) . '">Previous</a>' : '';
				$nextPage = $num > ($page * $datesPerPage) ? '<a href="index.php?page=' . ($page + 1) . '">Next</a>' : '';
				$paginationSeparator = $previousPage && $nextPage ? ' | ' : '';

				$sql = 'select date from matches group by date order by date desc limit ' . ($datesPerPage * ($page - 1)) . ', ' . $datesPerPage;
				$res = $db->query($sql);
				while($row = $res->fetch_assoc()) {
					$date = $row['date'];
					$matchesList .= '<div class="main-left-date">' . $date . '</div>';

					$sql3 = 'select id, date, team1, team2, odd1, odd2, winner from matches where date="' . $date . '"';
					$res3 = $db->query($sql3);
					while($row3 = $res3->fetch_assoc())
					{
						$id = $row3['id'];
						$team1 = $row3['team1'];
						$team2 = $row3['team2'];
						$odd1 = number_format($row3['odd1'], 2);
						$odd2 = number_format($row3['odd2'], 2);
						$winner = $row3['winner'];

						$procon = (($odd1 < $odd2) && $winner == 2) || (($odd1 > $odd2) && $winner == 1) ? ' con' : '';
						$procon = (($odd1 > $odd2) && $winner == 2) || (($odd1 < $odd2) && $winner == 1) ? ' pro' : $procon;
						$sql2 = 'select name from teams where id=' . $team1;
						$res2 = $db->query($sql2);
						$row2 = $res2->fetch_assoc();
						$team1name = $row2['name'];
						$sql2 = 'select name from teams where id=' . $team2;
						$res2 = $db->query($sql2);
						$row2 = $res2->fetch_assoc();
						$team2name = $row2['name'];
						$winner1 = $winner == 1 ? ' class="winner"' : '';
						$winner2 = $winner == 2 ? ' class="winner"' : '';
						$matchesList .= '
							<div class="main-left-match' . $procon . '">
								<span>
									<a href="index.php?match=' . $id . '&winner=1"' . $winner1 . '>' . $team1name . '</a> -
									<a href="index.php?match=' . $id . '&winner=2"' . $winner2 . '>' . $team2name . '</a>
								</span>
								<span><a href="index.php?match=' . $id . '&draw=1">D</a> ' . $odd1 . '-' . $odd2 . '</span>
							</div>';
					}
				}

				$this->initialHtml .= '
					<ul>
' . $matchesList . '
					</ul>
					<div class="main-left-pagination">
						<div>Page ' . $page . '</div>
						<div>' . $previousPage . $paginationSeparator . $nextPage . '</div>
					</div>
				</section>
				<section id="main-right">';
		$this->finalHtml = '
				</section>
			</main>
		</wrapper>';
	}

	public function create($page)
	{
		include('bodyClass.nba.' . $page . '.php');

		$this->html = 
$this->initialHtml .
$this->html .
$this->finalHtml;
	}
}
?>