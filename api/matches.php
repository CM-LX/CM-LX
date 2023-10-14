<?
if(!isset(($_COOKIE['ckcm'])) || $_COOKIE['ckcm'] != 1962) die();

function validate($date) {
	$d = DateTime::createFromFormat('Y-m-d', $date);
	return $d && $d->format('Y-m-d') == $date;
}

if(!isset(($_GET['date'])) || !validate($_GET['date'])) die();

$date = $_GET['date'];

ini_set('log_errors', TRUE);
ini_set('html_errors', TRUE);
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);

set_include_path('..:../inc:../classes');

include('Error/Error.nba.php');
include('Body/Body.nba.php');

$db = new Db;
$body = new Body;
$match = new MyMatch($body);
$team = new Team($body);
$bet = new Bet($body);

function colorValue($value) {
	$html = ($value >= 0) ? '<span class="green">' . number_format($value, 2) . '</span>' : '<span class="red">' . number_format($value, 2) . '</span>';
	return $html;
}

function listBet($id) {
 	global $match, $team, $bet, $dailyMinBalance, $dailyMaxBalance;

	$team1 = $match->get($id, 'team1');
	$team2 = $match->get($id, 'team2');
	$odd1 = $match->get($id, 'odd1');
	$odd2 = $match->get($id, 'odd2');
	$betMin1 = $match->get($id, 'betMin1');
	$betMin2 = $match->get($id, 'betMin2');
	$betMax1 = $match->get($id, 'betMax1');
	$betMax2 = $match->get($id, 'betMax2');
	$winner = $match->get($id, 'winner');
	
	$team1Name = $team->get($team1, 'name');
	$team2Name = $team->get($team2, 'name');
	
	$matchStyle = '';
	$matchStyle = (($odd1 < $odd2) && $winner == 1) || (($odd2 < $odd1) && $winner == 2) ? 'green' : 'red';
	$team1Style = $winner == 1 ? 'bold' : '';
	$team2Style = $winner == 2 ? 'bold' : '';

	$gainBetMin = (($betMin1 && $winner == 1) || ($betMin2 && $winner == 2)) ? ($betMin1 * ($odd1 - 1)) + ($betMin2 * ($odd2 - 1)) : -1;
	$dailyMinBalance += $gainBetMin;

	$gainBetMax = (($betMax1 && $winner == 1) || ($betMax2 && $winner == 2)) ? ($betMax1 * ($odd1 - 1)) + ($betMax2 * ($odd2 - 1)) : -($betMax1 + $betMax2);
	$dailyMaxBalance += $gainBetMax;

	$html = '
		<div class="list-detail grid" onClick="toggleVisibility(\'bet' . $id . '\')">
			<div class="list-detail-heading span3 left" onClick="getBetDetail(' . $id . ')">
				' . $id . ': <span class="' . $team1Style . ' ' . $matchStyle . '">' . $team1Name . '</span>-<span class="' . $team2Style . ' ' . $matchStyle . '">' . $team2Name . '</span> (' . $odd1 . '-' . $odd2 . ')
			</div>
			<div class="list-detail-heading span3 grid">
				<span></span>
				<span class="right">' . $betMin1 . ':' . $betMin2 . '</span>
				<span class="right">' . $betMin1 . ':' . $betMin2 . '</span>
			</div>
			<div class="list-detail-heading span3 grid">
				<span></span>
				<span class="right">' . colorValue($gainBetMin) . '</span>
				<span class="right">' . colorValue($gainBetMax) . '</span>
			</div>
			<div class="list-detail-heading span3 grid">
				<span></span>
				<span class="right">' . colorValue($dailyMinBalance) . '</span>
				<span class="right">' . colorValue($dailyMaxBalance) . '</span>
			</div>
		</div>
		<div id="bet' . $id . '" class="hidden bet-detail" style="display:none"></div>';

	echo $html;    
}

$html = '
<script>
	
</script>';

$sql = 'select id, todd1, todd2 from matches where date="' . $date . '" order by id';
$res = $db->query($sql);
while($row = $res->fetch_assoc()) {
	$id = $row['id'];
	$todd1 = $row['todd1'];
	$todd2 = $row['todd2'];

	// skip matches with the same odds
	$sql2 = 'select id from matches where id!=' . $id . ' and date="' . $date . '" and (todd1=' . $todd1 . ' or todd2=' . $todd1 . ' or todd1=' . $todd2 . ' or todd2=' . $todd2 . ')'; 
	$db->query($sql2);
	if($db->num_rows()) continue;

	$html .= listBet($id);
}
        
$html .= '
	<script>
		function showMatches(date) {
			getMatches(date);
			toggleVisibility(date);
		}

		function toggleVisibility(id) {
			const el = document.getElementById(id);
			const elDisplay = el.style.display;
			if(elDisplay == "none") {
				el.style.display = "block";
			} else {
				el.style.display = "none";
			}
		}
	</script>';        

echo $html;
?>