<?
if(!isset(($_COOKIE['ckcm'])) || $_COOKIE['ckcm'] != 1962) die();

if(!isset(($_GET['id'])) || !is_numeric($_GET['id'])) die();

$id = $_GET['id'];

ini_set('log_errors', TRUE);
ini_set('html_errors', TRUE);
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);

set_include_path('..:../inc:../classes');

include('Error/Error.nba.php');
// include('Db/Db.nba.php');
include('Body/Body.nba.php');
// include('Match/Match.nba.php');

$db = new Db;
$body = new Body;
$match = new MyMatch($body);
$team = new Team($body);
$bet = new Bet($body);

function colorValue($value) {
	$html = ($value >= 0) ? '<span class="green">' . number_format($value, 2) . '</span>' : '<span class="red">' . number_format($value, 2) . '</span>';
	return $html;
}

$html = '';

function listBet($id) {
	global $html; // $match, $team, $bet, $dailyMinBalance, $dailyMaxBalance;

// 	$body->bet->sum1 = 0;
// 	$body->bet->sum2 = 0;
	
// 	$betDetail = '
// 	<div class="grid-bet-detail bold right">
// 		<div class="left">Value</div>
// 		<div>Team1</div>
// 		<div>Team2</div>
// 		<div>Sum1</div>
// 		<div>Sum2</div>
// 		<div>Gain</div>
// 		<div>Balance</div>
// 	</div>';
	
// // 	$date = $body->match->get('date', $id);
// 	$team1 = $match->get('team1', $id);
// 	$team2 = $match->get('team2', $id);
// 	$odd1 = $match->get('odd1', $id);
// 	$odd2 = $match->get('odd2', $id);
// // 	$todd1 = $body->match->get('todd1', $id);
// // 	$todd2 = $body->match->get('todd2', $id);
// 	$betMin1 = $match->get('betMin1', $id);
// 	$betMin2 = $match->get('betMin2', $id);
// 	$betMax1 = $match->get('betMax1', $id);
// 	$betMax2 = $match->get('betMax2', $id);
// 	$winner = $match->get('winner', $id);
	
// 	$team1Name = $team->getName($team1);
// 	$team2Name = $team->getName($team2);
	
// 	$matchStyle = '';
// 	$matchStyle = (($odd1 < $odd2) && $winner == 1) || (($odd2 < $odd1) && $winner == 2) ? 'green' : 'red';
// 	$team1Style = $winner == 1 ? 'bold' : '';
// 	$team2Style = $winner == 2 ? 'bold' : '';

	$betDetail .= formatLine('Odd success', $body->odd->oddComparativeSuccess->get($todd1, $todd2));
	$betDetail .= formatLine('Odd probability', $body->odd->oddComparativeProbability->get($todd1, $todd2, $date));
	$betDetail .= formatLine('Odd gains', $body->odd->oddComparativeGains->get($todd1, $todd2));
	$betDetail .= formatLine('Team success', $body->team->teamComparativeSuccess->get($team1, $team2));
	$betDetail .= formatLine('Team probability', $body->team->teamComparativeProbability->get($team1, $team2, $date));
	$betDetail .= formatLine('Team gains', $body->team->teamComparativeGains->get($team1, $team2));

	// $sum1 = $bet->sum1;
	// $sum2 = $bet->sum2;

	// MIN

	// $team1BetMin = $sum1 > $sum2 ? 1 : 0;
	// $team2BetMin = $sum2 > $sum1 ? 1 : 0;

	// $gainBetMin = (($betMin1 && $winner == 1) || ($betMin2 && $winner == 2)) ? ($betMin1 * ($odd1 - 1)) + ($betMin2 * ($odd2 - 1)) : -1;

	// $dailyMinBalance += $gainBetMin;

// 	$betStyle = $gainBetMin > 0 ? 'green' : 'red';

// 	$betDetail .= '
// 	<div class="grid-bet-detail right bold ' . $betStyle . '">
// 		<div class="left">Min (sum1 > sum2)</div>
// 		<div></div>
// 		<div></div>
// 		<div>' . $team1BetMin . '</div>
// 		<div>' . $team2BetMin . '</div>
// 		<div>' . number_format($gainBetMin, 2) . '</div>
// 		<div>' . number_format($dailyMinBalance, 2) . '</div>
// 		</div>';
		
// 	// MAX

	// if ($sum1 > 0 && $sum2 > 0) {
	// 	$team1BetMax = floor($sum1 / $sum2);
	// 	$team2BetMax = floor($sum2 / $sum1);
	// } else {
	// 	$team1BetMax = $sum1 > $sum2 ? 1 : 0;
	// 	$team2BetMax = $sum2 > $sum1 ? 1 : 0;
	// }

// 	// $maxBet = 1 + (floor($body->betListBets->maxBalance / 4)) * ($body->betListBets->maxBalance > 0); 

// 	// $team1BetMax = min($team1BetMax, $maxBet);
// 	// $team2BetMax = min($team2BetMax, $maxBet);

	// $gainBetMax = (($betMax1 && $winner == 1) || ($betMax2 && $winner == 2)) ? ($betMax1 * ($odd1 - 1)) + ($betMax2 * ($odd2 - 1)) : -($betMax1 + $betMax2);

	// $dailyMaxBalance += $gainBetMax;

// 	$betStyle = $gainBetMax > 0 ? 'green' : 'red';

// 	$betDetail .= '
// 	<div class="grid-bet-detail right bold ' . $betStyle . '">
// 	<div class="left">Max (sum1 / sum2)</div>
// 	<div></div>
// 		<div></div>
// 		<div>' . $team1BetMax . '</div>
// 		<div>' . $team2BetMax . '</div>
// 		<div>' . number_format($gainBetMax, 2) . '</div>
// 		<div>' . number_format($dailyMaxBalance, 2) . '</div>
// 	</div>';

// 	$html = '
// 		<div class="list-detail grid211 grid-gap50 pointer" onClick="toggleVisibility(\'match' . $id . '\')">
// 			<div class="list-detail-heading">
// 				' . $id . ': <span class="' . $team1Style . ' ' . $matchStyle . '">' . $team1Name . '</span>-<span class="' . $team2Style . ' ' . $matchStyle . '">' . $team2Name . '</span> (' . $odd1 . '-' . $odd2 . ')
// 			</div>
// 			<div class="list-detail-heading grid3">
// 				<span></span>
// 				<span class="right">' . colorValue($gainBetMin) . '</span>
// 				<span class="right">' . colorValue($gainBetMax) . '</span>
// 			</div>
// 			<div class="list-detail-heading grid3">
// 				<span></span>
// 				<span class="right">' . colorValue($dailyMinBalance) . '</span>
// 				<span class="right">' . colorValue($dailyMaxBalance) . '</span>
// 			</div>
// 		</div>
// 		<div id="match' . $id . '" class="hidden bet-detail" style="display:none">
// betDetail
// 		</div>';

// 	echo $html;    
// }

// $html = '
// <script>
	
// </script>';

// $sql = 'select id, todd1, todd2 from matches where date="' . $date . '" order by id';
// $res = $db->query($sql);
// while($row = $res->fetch_assoc()) {
// 	$id = $row['id'];
// 	$todd1 = $row['todd1'];
// 	$todd2 = $row['todd2'];

// 	// skip matches with the same odds
// 	$sql2 = 'select id from matches where id!=' . $id . ' and date="' . $date . '" and (todd1=' . $todd1 . ' or todd2=' . $todd1 . ' or todd1=' . $todd2 . ' or todd2=' . $todd2 . ')'; 
// 	// echo "BetListBets::listBets $sql3<br>";
// 	$db->query($sql2);
// 	if($db->num_rows()) continue;

// 	$html .= listBet($id);
	$html = "BET DETAIL";
}

echo $html;

// include('inc.clean.nba.php');
?>