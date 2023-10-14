<?
class MatchGetRightMatches extends MyMatch {
    public $body;

    function __construct($body) {
		$this->body = $body;
    }

    function getRightMatches() {
    	$page = isset($_GET['rpage']) ? intval($_GET['rpage']) : 1;

        $matchesList = '';

        $sql = 'select count(id) as num from matches';
		$res = $this->body->db->query($sql);
		$row = $res->fetch_assoc();
		$num = $row['num']; 

		$resultsPerPage = 20;

        $firstPage = $page > 1 ? '<a href="matches.nba.php">First</a> | ' : '';
		$previousPage = $page > 2 ? '<a href="matches.nba.php?rpage=' . ($page - 1) . '">Previous</a>' : '';
		$nextPage = $num > ($page * $resultsPerPage) ? '<a href="matches.nba.php?rpage=' . ($page + 1) . '">Next</a>' : '';
        $lastPage = floor($num / $resultsPerPage) + 1;
        $nextPage = $page == ($lastPage - 1) ? '' : $nextPage;
        $lastPage = $page < $lastPage ? ' | <a href="matches.nba.php?rpage=' . $lastPage . '">Last</a>' : '';
		$paginationSeparator = $previousPage && $nextPage ? ' | ' : '';

		$start = $resultsPerPage * ($page - 1);

		$sql = 'select id from matches order by date desc limit ' . $start . ', ' . $resultsPerPage;
		$res = $this->body->db->query($sql);
		while($row = $res->fetch_assoc())
		{
			$id = $row['id'];

            $date = $this->get($id, 'date');
			$team1 = $this->get($id, 'team1');
			$team2 = $this->get($id, 'team2');
    
            $team1name = $this->body->team->getField($team1, 'name');
            $team2name = $this->body->team->getField($team2, 'name');

            $matchesList .= '
			<div class="first-level-title grid left">
				<span>';
			if($this->body->cm) {	
				$matchesList .= '<a href="javascript:void(0)" class="delete-button delete" id="delete' . $id . '" onClick="handleDelete(' . $id . ')">Delete</a>
					<a href="matches.nba.php?deleteMatch=' . $id . '" class="delete-button confirm-delete" id="confirmDelete' . $id . '">Confirm</a>';
			}	
			$matchesList .= '
				</span>
				<span class="span2">' . $date . '</span>
				<span class="span4"><a href="matches.nba.php?id=' . $id . '"> ' . $team1name . '-' . $team2name . '</a></span>
			</div>';
		}

		$html = '
		<section id="list">
				' . $matchesList . '
			<div id="matches-pagination">
				<div id="page">Page ' . $page . '</div>
				<div id="previousNext">' . $firstPage . $previousPage . $paginationSeparator . $nextPage . $lastPage . '</div>
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

        return $html;
    }
}
?>