<?
	global $db;
	
	$get_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$lpage = isset($_GET['lpage']) ? intval($_GET['lpage']) : 1;

	$this->html = '
	<h1>MATCHES</h1>';

	if($get_id) {
		$sql = 'select date, team1, team2, odd1, odd2, winner from matches where id=' . $get_id;
		$res = $db->query($sql);
		$row = $res->fetch_assoc();
		$date = $row['date'];
		$team1 = $row['team1'];
		$team2 = $row['team2'];
		$odd1 = $row['odd1'];
		$odd2 = $row['odd2'];
		$winner = $row['winner'];
		$winner = $winner ? $winner : '';

		$this->html = '
		<section id="edit-match">
			<form action="admin.matches.nba.php" method="POST">
				<input type="hidden" name="id" value="' . $get_id . '">
				<input type="date" name="date" value="' . $date . '">
				<select name="team1">
					<option value="0">Team 1</option>'
					. selectTeams($team1) .
'				</select>
				<select name="team2">
					<option value="0">Team 2</option>'
					. selectTeams($team2) .
'				</select>
				<input type="text" name="odd1" value="' . $odd1 . '">
				<input type="text" name="odd2" value="' . $odd2 . '">
				<input type="text" name="winner" placeholder="Winner" value="' . $winner . '">
				<input type="submit" value="Submit">
			</form>
		<section>';
	} else {
		$matchesList = '';

		$sql = 'select count(id) as num from matches';
		$res = $db->query($sql);
		$row = $res->fetch_assoc();
		$matchCount = $row['num']; 

		$resultsPerPage = 20;
		$currentPage = $lpage;

		$previousPage = $lpage - 1;
		$previousPageLink = $previousPage > 0 ? "<a href='matches.nba.php?lpage=$previousPage'>Previous</a> " : '';
		
		$nextPage = $lpage + 1; 
		$nextPageLink = $nextPage < 1 + ($matchCount / $resultsPerPage) ? "<a href='matches.nba.php?lpage=$nextPage'>Next</a>" : '';

		$start = $resultsPerPage * ($currentPage - 1);
		$sql = 'select id, date, team1, team2 from matches order by date desc limit ' . $start . ', ' . $resultsPerPage;
		$res = $db->query($sql);
		while($row = $res->fetch_assoc())
		{
			$id = $row['id'];
			$date = $row['date'];
			$team1 = $row['team1'];
			$team2 = $row['team2'];
			$sql2 = 'select name from teams where id=' . $team1;
			$res2 = $db->query($sql2);
			$row2 = $res2->fetch_assoc();
			$team1name = $row2['name'];
			$sql2 = 'select name from teams where id=' . $team2;
			$res2 = $db->query($sql2);
			$row2 = $res2->fetch_assoc();
			$team2name = $row2['name'];
			$matchesList .= '
			<div class="list-title">
				<a href="matches.nba.php?id=' . $id . '">' . $date . ' ' . $team1name . '-' . $team2name . '</a> 
				<a href="javascript:void(0)" class="match-delete" id="delete' . $id . '" onClick="handleDelete(' . $id . ')">Delete</a>
				<a href="matches.nba.php?delete=' . $id . '" class="match-confirmDelete" id="confirmDelete' . $id . '">Confirm</a>
			</div>';
		}

		$this->html .= '
		<section id="list">
				' . $matchesList . '
			<style>

			</style>
			<div id="matches-pagination">
				<div id="page">Page ' . $lpage . '</div>
				<div id="previousNext">' . $previousPageLink . $nextPageLink . '</div>
			</div>
		</section>
		<script>
			function handleDelete(id) {
				const deleteID = "delete" + id;
				const myDelete = document.getElementById(deleteID);
				myDelete.style.display = "none";
				const confirmID = "confirmDelete" + id;
				const confirm = document.getElementById(confirmID);
				confirm.style.display = "block";
			}
		</script>';
	}
?>