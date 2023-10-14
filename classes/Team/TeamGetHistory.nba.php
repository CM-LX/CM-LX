<?php
	function teamGetHistory($id, $body) {
        include_once('Team/TeamGetHistoryByDate.nba.php');
        include_once('Team/TeamGetMatchesByMonth.nba.php');
		$html = '
<!-- Team::teamGetHistory -->        
        ';
	
		$sql = 'select date_format(date, "%Y") as year from matches where final=1 && (team1=' . $id . ' || team2=' . $id . ') group by year order by year desc'; // echo "Team::getHistory $sql<br>";
		$res = $body->db->query($sql);
		$num = $body->db->num_rows();
		if(!$num) return null;

		while($row = $res->fetch_assoc()) {
			$year = $row['year']; // echo "Team::getHistory YEAR: $year<br>";

			$html .= '<div>' . 
			teamGetHistoryByDate($id, $year, 'second-level-title', $body) 
			. '<div>
			<div id="' . $year . '" class="hidden" style="display:none">';

			$sql2 = 'select date_format(date, "%Y-%m") as month from matches where final=1 && (team1=' . $id . ' || team2=' . $id . ') group by month order by month asc'; // echo "Team::getHistory $sql2<br>";
			$res2 = $body->db->query($sql2);
			while($row2 = $res2->fetch_assoc()) {
				$month = $row2['month'];
				$html .= teamGetHistoryByDate($id, $month, 'third-level-title', $body);
				$html .= '<div id="' . $month . '" class="hidden" style="display:none">' . teamGetMatchesByMonth($id, $month, $body) . '</div>';

			} 
			$html .= '</div>';
	
		}

		return $html;
	}
?>