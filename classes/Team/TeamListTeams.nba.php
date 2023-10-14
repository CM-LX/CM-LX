<?php
    function teamListTeams($body) {
        $sort = isset($_GET['s']) ? strip_tags($_GET['s']) : 'name';
        $dir = isset($_GET['d']) ? strip_tags($_GET['d']) : 'asc';

        $teamsList = '
		<div class="first-level-title grid">
			<div class="span3"></div>
			<div class="span3">MATCHES <a href="teams.nba.php?s=matches&d=asc">' . $body->arrowUp . '</a> <a href="teams.nba.php?s=matches&d=desc">' . $body->arrowDown . '</a></div>
			<div class="span3">WINS <a href="teams.nba.php?s=wins&d=asc">' . $body->arrowUp . '</a> <a href="teams.nba.php?s=wins&d=desc">' . $body->arrowDown . '</a></div>
			<div class="span3">SUCCESS <a href="teams.nba.php?s=success&d=asc">' . $body->arrowUp . '</a> <a href="teams.nba.php?s=success&d=desc">' . $body->arrowDown . '</a></div>
		</div>';

		$sql = 'select id, name, matches, wins, success from teams order by ' . $sort . ' ' . $dir;
		$res = $body->db->query($sql);
		while($row = $res->fetch_assoc())
		{
			$id = $row['id'];
			$name = $row['name'];
			$matches = $row['matches'];
			$wins = $row['wins'];
			$success = $row['success'];

			$teamsList .= '
			<div class="second-level-title grid left">
				<span>';

			if($body->cm) {	
				$teamsList .= '
				<a href="javascript:void(0)" class="delete-button delete" id="delete' . $id . '" onClick="handleDelete(' . $id . ')">Delete</a>
				<a href="teams.nba.php?deleteTeam=' . $id . '" class="delete-button confirm-delete" id="confirmDelete' . $id . '">Confirm</a>';
			}	

			$teamsList .= '
				</span>
				<span class="span2 left">
					<a href="teams.nba.php?id=' . $id . '">' . $name . '</a>
				</span>
				<span class="span3 right">' . $matches . '</span>
				<span class="span3 right">' . $wins . '</span>
				<span class="span3 right">' . $success . '%</span>
			</div>';
		}

		$html = '
		<section id="teams-list">
			' . $teamsList . '
		</section>';
		if($body->cm) {
			$html .=
'			<section id="teams-new-team">
				<form action="teams.nba.php" method="POST">
					<input type="hidden" name="createTeam" value="1">
					NEW TEAM: <input type="text" placeholder="Team name" name="name">
					<input type="submit" value="Create">
				</form>
			</section>';
		}

        return $html;
    }
?>